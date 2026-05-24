<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_gets_generated_slug_and_submitted_slug_is_ignored(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'dashboard_theme' => 'blush',
        ]);

        $response = $this->actingAs($user)->post(route('onboarding.store'), [
            'name' => 'Doceria Azul',
            'slug' => 'meu-link-premium',
            'whatsapp' => '18999999999',
            'store_theme' => 'candy',
        ]);

        $response->assertRedirect(route('onboarding.index'));

        $store = $user->fresh()->store;

        $this->assertNotNull($store);
        $this->assertMatchesRegularExpression('/^loja-[a-z0-9]{6}$/', $store->slug);
        $this->assertNotSame('meu-link-premium', $store->slug);
    }

    public function test_paid_plan_can_choose_custom_slug(): void
    {
        $user = User::factory()->create([
            'plan' => 'plus',
            'dashboard_theme' => 'blush',
        ]);

        $response = $this->actingAs($user)->post(route('onboarding.store'), [
            'name' => 'HQ Zone',
            'slug' => 'hq-zone',
            'whatsapp' => '18999999999',
            'store_theme' => 'candy',
        ]);

        $response->assertRedirect(route('onboarding.index'));

        $this->assertDatabaseHas('stores', [
            'user_id' => $user->id,
            'slug' => 'hq-zone',
        ]);
    }

    public function test_paid_plan_cannot_use_existing_or_reserved_slug(): void
    {
        $owner = User::factory()->create(['plan' => 'plus']);

        Store::create([
            'user_id' => $owner->id,
            'name' => 'Loja Existente',
            'slug' => 'loja-usada',
        ]);

        $user = User::factory()->create([
            'plan' => 'plus',
            'dashboard_theme' => 'blush',
        ]);

        $this->actingAs($user)->post(route('onboarding.store'), [
            'name' => 'Nova Loja',
            'slug' => 'loja-usada',
            'store_theme' => 'candy',
        ])->assertSessionHasErrors('slug');

        $this->actingAs($user)->post(route('onboarding.store'), [
            'name' => 'Nova Loja',
            'slug' => 'login',
            'store_theme' => 'candy',
        ])->assertSessionHasErrors('slug');
    }

    public function test_slug_availability_endpoint_reports_status(): void
    {
        $owner = User::factory()->create(['plan' => 'plus']);

        Store::create([
            'user_id' => $owner->id,
            'name' => 'Loja Usada',
            'slug' => 'loja-usada',
        ]);

        $user = User::factory()->create(['plan' => 'plus']);

        $this->actingAs($user)
            ->getJson(route('onboarding.slug-check', ['slug' => 'loja-usada']))
            ->assertOk()
            ->assertJsonPath('slug', 'loja-usada')
            ->assertJsonPath('available', false);

        $this->actingAs($user)
            ->getJson(route('onboarding.slug-check', ['slug' => 'minha-nova-loja']))
            ->assertOk()
            ->assertJsonPath('slug', 'minha-nova-loja')
            ->assertJsonPath('available', true);
    }

    public function test_free_plan_cannot_change_slug_from_store_settings(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        Store::create([
            'user_id' => $user->id,
            'name' => 'Minha Loja',
            'slug' => 'minha-loja',
        ]);

        $this->actingAs($user)->put(route('store.update'), [
            'name' => 'Minha Loja Atualizada',
            'slug' => 'slug-pago',
            'whatsapp' => '18999999999',
            'description' => 'Descricao',
            'store_theme_mode' => 'preset',
            'store_theme' => 'candy',
            'dashboard_theme_mode' => 'preset',
            'dashboard_theme' => 'blush',
        ])->assertRedirect();

        $this->assertDatabaseHas('stores', [
            'user_id' => $user->id,
            'name' => 'Minha Loja Atualizada',
            'slug' => 'minha-loja',
        ]);
    }
}
