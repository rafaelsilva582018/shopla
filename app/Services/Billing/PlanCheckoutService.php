<?php

namespace App\Services\Billing;

use App\Models\PlanSubscription;
use App\Models\User;
use App\Services\Asaas\AsaasClient;
use App\Services\Plans\PlanCatalog;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PlanCheckoutService
{
    public function __construct(private AsaasClient $asaas, private PlanCatalog $plans)
    {
    }

    public function start(User $user, string $planKey, string $returnTo = 'plans', string $billingPeriod = 'monthly'): PlanSubscription
    {
        $plan = $this->plans->find($planKey);

        if (!$plan || !isset($plan['price']) || $planKey === 'free' || $planKey === 'enterprise') {
            throw ValidationException::withMessages([
                'plan' => 'Este plano ainda nao esta disponivel para pagamento online.',
            ]);
        }

        if (($user->plan ?: 'free') === $planKey) {
            throw ValidationException::withMessages([
                'plan' => 'Voce ja esta usando este plano.',
            ]);
        }

        $this->ensureBillingProfileIsComplete($user);
        $billing = $this->billingOption($plan, $billingPeriod);

        $subscription = PlanSubscription::create([
            'user_id' => $user->id,
            'plan' => $planKey,
            'status' => 'pending',
            'amount' => $billing['amount'],
            'external_reference' => 'shopla-plan-' . $user->id . '-' . Str::uuid(),
        ]);

        $checkout = $this->asaas->createCheckout(
            $this->checkoutPayload($user, $subscription, $plan, $returnTo, $billing)
        );

        $checkoutId = $checkout['id'] ?? null;
        $checkoutUrl = $checkoutId
            ? config('services.asaas.checkout_url') . $checkoutId
            : null;

        $subscription->update([
            'asaas_checkout_id' => $checkoutId,
            'checkout_url' => $checkoutUrl,
            'raw_response' => $checkout,
        ]);

        return $subscription->refresh();
    }

    private function checkoutPayload(User $user, PlanSubscription $subscription, array $plan, string $returnTo, array $billing): array
    {
        $returnTo = in_array($returnTo, ['plans', 'onboarding'], true) ? $returnTo : 'plans';
        $successUrl = route('plans.return', ['status' => 'sucesso', 'return_to' => $returnTo]);
        $cancelUrl = route('plans.return', ['status' => 'cancelado', 'return_to' => $returnTo]);
        $expiredUrl = route('plans.return', ['status' => 'expirado', 'return_to' => $returnTo]);

        return array_filter([
            'billingTypes' => ['CREDIT_CARD'],
            'chargeTypes' => ['RECURRENT'],
            'minutesToExpire' => config('services.asaas.checkout_expiration_minutes', 120),
            'externalReference' => $subscription->external_reference,
            'callback' => [
                'successUrl' => $successUrl,
                'cancelUrl' => $cancelUrl,
                'expiredUrl' => $expiredUrl,
            ],
            'items' => [
                [
                    'name' => 'Plano ' . $plan['name'] . ' - ' . ucfirst($billing['label']),
                    'description' => 'Assinatura ' . $billing['label'] . ' do Shopla',
                    'quantity' => 1,
                    'value' => (float) $subscription->amount,
                ],
            ],
            'customerData' => $this->customerData($user),
            'subscription' => [
                'cycle' => $billing['cycle'],
                'nextDueDate' => now()->format('Y-m-d H:i:s'),
            ],
        ], fn ($value) => filled($value));
    }

    private function billingOption(array $plan, string $billingPeriod): array
    {
        if ($billingPeriod === 'annual' && isset($plan['annual_price'])) {
            return [
                'amount' => (float) $plan['annual_price'],
                'cycle' => $plan['annual_cycle'] ?? 'YEARLY',
                'label' => $plan['annual_billing_label'] ?? 'anual',
            ];
        }

        return [
            'amount' => (float) $plan['price'],
            'cycle' => $plan['cycle'] ?? 'MONTHLY',
            'label' => $plan['billing_label'] ?? 'mensal',
        ];
    }

    private function customerData(User $user): array
    {
        return array_filter([
            'name' => trim($user->name . ' ' . ($user->last_name ?? '')),
            'email' => $user->email,
            'phone' => $this->onlyNumbers($user->phone),
            'cpfCnpj' => $this->onlyNumbers($user->document),
            'postalCode' => $this->onlyNumbers($user->zip_code),
            'address' => $user->address,
            'addressNumber' => $user->address_number,
            'complement' => $user->address_complement,
            'province' => $user->district,
        ], fn ($value) => filled($value));
    }

    private function onlyNumbers(?string $value): ?string
    {
        return $value ? preg_replace('/\D+/', '', $value) : null;
    }

    private function ensureBillingProfileIsComplete(User $user): void
    {
        $missing = collect([
            'CPF/CNPJ' => $this->onlyNumbers($user->document),
            'CEP' => $this->onlyNumbers($user->zip_code),
            'rua/logradouro' => $user->address,
            'numero' => $user->address_number,
            'bairro' => $user->district,
        ])
            ->filter(fn ($value) => blank($value))
            ->keys()
            ->all();

        if ($missing) {
            throw new RuntimeException(
                'Antes de assinar, complete no Perfil: ' . implode(', ', $missing) . '.'
            );
        }
    }
}
