<?php

namespace App\Http\Controllers;

use App\Services\Billing\PlanCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PlanCheckoutController extends Controller
{
    public function store(Request $request, string $plan, PlanCheckoutService $checkout): RedirectResponse
    {
        $data = $request->validate([
            'return_to' => ['nullable', 'in:plans,onboarding'],
            'billing_period' => ['nullable', 'in:monthly,annual'],
        ]);

        $returnTo = $data['return_to'] ?? 'plans';
        $billingPeriod = $data['billing_period'] ?? 'monthly';

        try {
            $subscription = $checkout->start($request->user(), $plan, $returnTo, $billingPeriod);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if (!$subscription->checkout_url) {
            return back()->with('error', 'O Asaas nao retornou o link de pagamento. Tente novamente.');
        }

        return redirect()->away($subscription->checkout_url);
    }

    public function result(Request $request, string $status): RedirectResponse
    {
        $messages = [
            'sucesso' => 'Pagamento iniciado. Assim que o Asaas confirmar, seu plano sera liberado automaticamente.',
            'cancelado' => 'Pagamento cancelado. Voce pode tentar novamente quando quiser.',
            'expirado' => 'O checkout expirou. Gere um novo link de pagamento para continuar.',
        ];

        return redirect()
            ->route($request->query('return_to') === 'onboarding' ? 'onboarding.index' : 'plans.index')
            ->with($status === 'sucesso' ? 'success' : 'error', $messages[$status] ?? 'Retorno do pagamento recebido.');
    }
}
