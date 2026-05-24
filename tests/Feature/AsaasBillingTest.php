<?php

namespace Tests\Feature;

use App\Models\PlanSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AsaasBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_asaas_checkout_for_paid_plan(): void
    {
        config([
            'services.asaas.access_token' => '$aact_hmlg_test',
            'services.asaas.base_url' => 'https://api-sandbox.asaas.com/v3',
            'services.asaas.checkout_url' => 'https://asaas.com/checkoutSession/show?id=',
        ]);

        Http::fake([
            'https://api-sandbox.asaas.com/v3/checkouts' => Http::response([
                'id' => 'checkout_123',
            ]),
        ]);

        $user = User::factory()->create([
            'plan' => 'free',
            'phone' => '18999999999',
            'document' => '12345678909',
            'zip_code' => '16700000',
            'address' => 'Rua Teste',
            'address_number' => '123',
            'district' => 'Centro',
        ]);

        $response = $this->actingAs($user)->post(route('plans.checkout', 'plus'));

        $response->assertRedirect('https://asaas.com/checkoutSession/show?id=checkout_123');

        $this->assertDatabaseHas('plan_subscriptions', [
            'user_id' => $user->id,
            'plan' => 'plus',
            'status' => 'pending',
            'asaas_checkout_id' => 'checkout_123',
        ]);

        Http::assertSent(fn ($request) => $request->hasHeader('access_token', '$aact_hmlg_test')
            && $request->url() === 'https://api-sandbox.asaas.com/v3/checkouts'
            && $request['chargeTypes'] === ['RECURRENT']
            && $request['billingTypes'] === ['CREDIT_CARD']
            && $request['subscription']['cycle'] === 'MONTHLY'
            && $request['items'][0]['value'] === 6.99);
    }

    public function test_checkout_requires_billing_profile_before_calling_asaas(): void
    {
        config([
            'services.asaas.access_token' => '$aact_hmlg_test',
            'services.asaas.base_url' => 'https://api-sandbox.asaas.com/v3',
        ]);

        Http::fake();

        $user = User::factory()->create([
            'plan' => 'free',
            'document' => null,
            'zip_code' => null,
            'address' => null,
            'address_number' => null,
            'district' => null,
        ]);

        $response = $this->actingAs($user)->post(route('plans.checkout', 'plus'));

        $response
            ->assertRedirect()
            ->assertSessionHas('error', 'Antes de assinar, complete no Perfil: CPF/CNPJ, CEP, rua/logradouro, numero, bairro.');

        $this->assertDatabaseMissing('plan_subscriptions', [
            'user_id' => $user->id,
            'plan' => 'plus',
        ]);

        Http::assertSentCount(0);
    }

    public function test_user_can_start_annual_checkout_with_discount(): void
    {
        config([
            'services.asaas.access_token' => '$aact_hmlg_test',
            'services.asaas.base_url' => 'https://api-sandbox.asaas.com/v3',
            'services.asaas.checkout_url' => 'https://asaas.com/checkoutSession/show?id=',
        ]);

        Http::fake([
            'https://api-sandbox.asaas.com/v3/checkouts' => Http::response([
                'id' => 'checkout_annual',
            ]),
        ]);

        $user = User::factory()->create([
            'plan' => 'free',
            'phone' => '18999999999',
            'document' => '12345678909',
            'zip_code' => '16700000',
            'address' => 'Rua Teste',
            'address_number' => '123',
            'district' => 'Centro',
        ]);

        $response = $this->actingAs($user)->post(route('plans.checkout', 'plus'), [
            'billing_period' => 'annual',
        ]);

        $response->assertRedirect('https://asaas.com/checkoutSession/show?id=checkout_annual');

        $annualPrice = config('plans.plus.annual_price');

        $this->assertDatabaseHas('plan_subscriptions', [
            'user_id' => $user->id,
            'plan' => 'plus',
            'status' => 'pending',
            'amount' => $annualPrice,
        ]);

        Http::assertSent(fn ($request) => $request['subscription']['cycle'] === 'YEARLY'
            && abs($request['items'][0]['value'] - $annualPrice) < 0.001
            && str_contains($request['items'][0]['description'], 'anual'));
    }

    public function test_asaas_webhook_activates_paid_plan(): void
    {
        config(['services.asaas.webhook_token' => 'secret-webhook-token-with-more-than-32-chars']);

        $user = User::factory()->create(['plan' => 'free']);
        $subscription = PlanSubscription::create([
            'user_id' => $user->id,
            'plan' => 'pro',
            'status' => 'pending',
            'amount' => 12.99,
            'external_reference' => 'shopla-plan-test',
            'asaas_checkout_id' => 'checkout_123',
        ]);

        $response = $this
            ->withHeader('asaas-access-token', 'secret-webhook-token-with-more-than-32-chars')
            ->postJson(route('webhooks.asaas'), [
                'id' => 'evt_123',
                'event' => 'PAYMENT_RECEIVED',
                'payment' => [
                    'id' => 'pay_123',
                    'customer' => 'cus_123',
                    'subscription' => 'sub_123',
                    'externalReference' => $subscription->external_reference,
                    'status' => 'RECEIVED',
                    'value' => 12.99,
                ],
            ]);

        $response->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('plan_subscriptions', [
            'id' => $subscription->id,
            'status' => 'active',
            'asaas_payment_id' => 'pay_123',
            'asaas_subscription_id' => 'sub_123',
        ]);

        $this->assertSame('pro', $user->fresh()->plan);
    }

    public function test_asaas_webhook_rejects_invalid_token(): void
    {
        config(['services.asaas.webhook_token' => 'secret-webhook-token-with-more-than-32-chars']);

        $response = $this
            ->withHeader('asaas-access-token', 'wrong-token')
            ->postJson(route('webhooks.asaas'), [
                'id' => 'evt_123',
                'event' => 'PAYMENT_RECEIVED',
            ]);

        $response->assertUnauthorized();
    }

    public function test_checkout_return_can_redirect_back_to_onboarding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('plans.return', [
                'status' => 'sucesso',
                'return_to' => 'onboarding',
            ]))
            ->assertRedirect(route('onboarding.index'))
            ->assertSessionHas('success');
    }

    public function test_user_can_cancel_active_subscription(): void
    {
        config([
            'services.asaas.access_token' => '$aact_hmlg_test',
            'services.asaas.base_url' => 'https://api-sandbox.asaas.com/v3',
        ]);

        Http::fake([
            'https://api-sandbox.asaas.com/v3/subscriptions/sub_123' => Http::response([
                'deleted' => true,
            ]),
        ]);

        $user = User::factory()->create(['plan' => 'pro']);

        $subscription = PlanSubscription::create([
            'user_id' => $user->id,
            'plan' => 'pro',
            'status' => 'active',
            'amount' => 12.99,
            'external_reference' => 'shopla-plan-cancel-test',
            'asaas_subscription_id' => 'sub_123',
            'paid_at' => now(),
        ]);

        $this->actingAs($user)
            ->delete(route('subscription.cancel'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('free', $user->fresh()->plan);
        $this->assertSame('canceled', $subscription->fresh()->status);

        Http::assertSent(fn ($request) => $request->method() === 'DELETE'
            && $request->url() === 'https://api-sandbox.asaas.com/v3/subscriptions/sub_123');
    }
}
