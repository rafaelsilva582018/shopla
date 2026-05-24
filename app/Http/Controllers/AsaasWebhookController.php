<?php

namespace App\Http\Controllers;

use App\Models\AsaasWebhookEvent;
use App\Models\PlanSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsaasWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->validateToken($request);

        $payload = $request->all();
        $eventName = (string) data_get($payload, 'event', 'UNKNOWN');
        $resourceType = $this->resourceType($payload);
        $resourceId = $this->resourceId($payload, $resourceType);
        $eventKey = (string) (data_get($payload, 'id') ?: sha1($eventName . '|' . $resourceType . '|' . $resourceId . '|' . json_encode($payload)));

        if (AsaasWebhookEvent::where('event_key', $eventKey)->exists()) {
            return response()->json(['ok' => true, 'duplicated' => true]);
        }

        DB::transaction(function () use ($payload, $eventName, $resourceType, $resourceId, $eventKey) {
            $event = AsaasWebhookEvent::create([
                'event_key' => $eventKey,
                'event' => $eventName,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'payload' => $payload,
            ]);

            $this->processPlanSubscription($eventName, $payload);

            $event->update(['processed_at' => now()]);
        });

        return response()->json(['ok' => true]);
    }

    private function validateToken(Request $request): void
    {
        $expected = (string) config('services.asaas.webhook_token');
        $received = (string) $request->header('asaas-access-token');

        abort_unless($expected && hash_equals($expected, $received), 401);
    }

    private function processPlanSubscription(string $eventName, array $payload): void
    {
        $subscription = $this->findSubscription($payload);

        if (!$subscription) {
            return;
        }

        $updates = [
            'last_webhook_payload' => $payload,
            'asaas_checkout_id' => data_get($payload, 'checkout.id', $subscription->asaas_checkout_id),
            'asaas_subscription_id' => data_get($payload, 'payment.subscription')
                ?: data_get($payload, 'subscription.id')
                ?: $subscription->asaas_subscription_id,
            'asaas_payment_id' => data_get($payload, 'payment.id', $subscription->asaas_payment_id),
            'asaas_customer_id' => data_get($payload, 'payment.customer')
                ?: data_get($payload, 'subscription.customer')
                ?: data_get($payload, 'checkout.customer')
                ?: $subscription->asaas_customer_id,
        ];

        if (in_array($eventName, ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED', 'CHECKOUT_PAID'], true)) {
            $updates['status'] = 'active';
            $updates['paid_at'] = $subscription->paid_at ?: now();

            $subscription->update($updates);
            $subscription->user->update([
                'plan' => $subscription->plan,
                'plan_started_at' => now(),
                'onboarding_plan' => $subscription->plan,
            ]);

            return;
        }

        if (in_array($eventName, ['PAYMENT_OVERDUE'], true)) {
            $updates['status'] = 'past_due';
        }

        if (in_array($eventName, [
            'PAYMENT_DELETED',
            'PAYMENT_REFUNDED',
            'PAYMENT_CHARGEBACK_REQUESTED',
            'SUBSCRIPTION_INACTIVATED',
            'SUBSCRIPTION_DELETED',
            'CHECKOUT_CANCELED',
            'CHECKOUT_EXPIRED',
        ], true)) {
            $updates['status'] = 'canceled';
            $updates['canceled_at'] = $subscription->canceled_at ?: now();
        }

        $subscription->update($updates);

        if (in_array($eventName, [
            'PAYMENT_REFUNDED',
            'PAYMENT_CHARGEBACK_REQUESTED',
            'SUBSCRIPTION_INACTIVATED',
            'SUBSCRIPTION_DELETED',
        ], true) && $subscription->user->plan === $subscription->plan) {
            $subscription->user->update([
                'plan' => 'free',
                'plan_started_at' => now(),
            ]);
        }
    }

    private function findSubscription(array $payload): ?PlanSubscription
    {
        $externalReference = data_get($payload, 'payment.externalReference')
            ?: data_get($payload, 'subscription.externalReference')
            ?: data_get($payload, 'checkout.externalReference')
            ?: data_get($payload, 'externalReference');

        if ($externalReference) {
            $subscription = PlanSubscription::where('external_reference', $externalReference)->first();

            if ($subscription) {
                return $subscription;
            }
        }

        $checkoutId = data_get($payload, 'checkout.id');

        if ($checkoutId) {
            $subscription = PlanSubscription::where('asaas_checkout_id', $checkoutId)->first();

            if ($subscription) {
                return $subscription;
            }
        }

        $asaasSubscriptionId = data_get($payload, 'payment.subscription') ?: data_get($payload, 'subscription.id');

        if ($asaasSubscriptionId) {
            return PlanSubscription::where('asaas_subscription_id', $asaasSubscriptionId)->first();
        }

        return null;
    }

    private function resourceType(array $payload): ?string
    {
        return collect(['payment', 'subscription', 'checkout'])
            ->first(fn ($key) => data_get($payload, $key) !== null);
    }

    private function resourceId(array $payload, ?string $resourceType): ?string
    {
        return $resourceType ? data_get($payload, $resourceType . '.id') : null;
    }
}
