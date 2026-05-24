<?php

namespace Tests\Feature;

use App\Models\PlanSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OnboardingPlanSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_selection_moves_onboarding_to_store_configuration(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'dashboard_theme' => 'blush',
            'onboarding_plan' => null,
        ]);

        $this->actingAs($user)
            ->post(route('onboarding.plan'), [
                'plan' => 'free',
            ])
            ->assertRedirect(route('onboarding.index'));

        $this->assertSame('free', $user->fresh()->onboarding_plan);

        $this->actingAs($user)
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertSee('Configure sua loja');
    }

    public function test_paid_plan_selection_saves_billing_data_and_starts_checkout(): void
    {
        config([
            'services.asaas.access_token' => '$aact_hmlg_test',
            'services.asaas.base_url' => 'https://api-sandbox.asaas.com/v3',
            'services.asaas.checkout_url' => 'https://asaas.com/checkoutSession/show?id=',
        ]);

        Http::fake([
            'https://api-sandbox.asaas.com/v3/checkouts' => Http::response([
                'id' => 'checkout_onboarding',
            ]),
        ]);

        $user = User::factory()->create([
            'plan' => 'free',
            'dashboard_theme' => 'blush',
            'onboarding_plan' => null,
        ]);

        $this->actingAs($user)
            ->post(route('onboarding.plan'), [
                'plan' => 'pro',
                'billing_period' => 'annual',
                'last_name' => 'Silva',
                'phone' => '18999999999',
                'document' => '12345678909',
                'zip_code' => '16700000',
                'address' => 'Rua Teste',
                'address_number' => '123',
                'address_complement' => 'Casa',
                'district' => 'Centro',
                'city' => 'Guararapes',
                'state' => 'sp',
            ])
            ->assertRedirect('https://asaas.com/checkoutSession/show?id=checkout_onboarding');

        $user->refresh();

        $this->assertSame('pro', $user->onboarding_plan);
        $this->assertSame('Silva', $user->last_name);
        $this->assertSame('SP', $user->state);

        $this->assertDatabaseHas('plan_subscriptions', [
            'user_id' => $user->id,
            'plan' => 'pro',
            'status' => 'pending',
            'asaas_checkout_id' => 'checkout_onboarding',
        ]);

        Http::assertSent(fn ($request) => $request->url() === 'https://api-sandbox.asaas.com/v3/checkouts'
            && $request['subscription']['cycle'] === 'YEARLY'
            && $request['customerData']['cpfCnpj'] === '12345678909'
            && $request['customerData']['postalCode'] === '16700000');
    }

    public function test_pending_paid_checkout_keeps_user_on_plan_step(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'dashboard_theme' => 'blush',
            'onboarding_plan' => 'plus',
        ]);

        PlanSubscription::create([
            'user_id' => $user->id,
            'plan' => 'plus',
            'status' => 'pending',
            'amount' => 6.99,
            'external_reference' => 'shopla-plan-pending-onboarding',
            'checkout_url' => 'https://asaas.com/checkoutSession/show?id=pending',
        ]);

        $this->actingAs($user)
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertSee('Pagamento em andamento')
            ->assertDontSee('Configure sua loja');
    }

    public function test_active_paid_plan_can_continue_to_store_configuration(): void
    {
        $user = User::factory()->create([
            'plan' => 'plus',
            'dashboard_theme' => 'blush',
            'onboarding_plan' => 'plus',
        ]);

        $this->actingAs($user)
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertSee('Configure sua loja');
    }
}
