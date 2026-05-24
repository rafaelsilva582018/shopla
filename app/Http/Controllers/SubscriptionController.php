<?php

namespace App\Http\Controllers;

use App\Services\Asaas\AsaasClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class SubscriptionController extends Controller
{
    public function destroy(Request $request, AsaasClient $asaas): RedirectResponse
    {
        $user = $request->user();
        $subscription = $user->activePlanSubscription;

        if (!$subscription) {
            return back()->with('error', 'Nenhuma assinatura ativa foi encontrada para cancelar.');
        }

        if (!$subscription->asaas_subscription_id) {
            return back()->with('error', 'Essa assinatura nao possui identificador do Asaas. Fale com o suporte para cancelar manualmente.');
        }

        try {
            $asaas->cancelSubscription($subscription->asaas_subscription_id);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        if (($user->plan ?: 'free') === $subscription->plan) {
            $user->update([
                'plan' => 'free',
                'plan_started_at' => now(),
            ]);
        }

        return back()->with('success', 'Assinatura cancelada com sucesso.');
    }
}
