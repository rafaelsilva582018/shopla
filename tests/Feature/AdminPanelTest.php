<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Plans\PlanCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        config(['admin.emails' => ['admin@shopla.test']]);

        $admin = User::factory()->create([
            'email' => 'admin@shopla.test',
            'plan' => 'free',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Painel administrativo');
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        config(['admin.emails' => ['admin@shopla.test']]);

        $user = User::factory()->create([
            'email' => 'cliente@shopla.test',
            'plan' => 'free',
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_change_user_plan_and_store_status(): void
    {
        config(['admin.emails' => ['admin@shopla.test']]);

        $admin = User::factory()->create(['email' => 'admin@shopla.test']);
        $user = User::factory()->create([
            'name' => 'Cliente',
            'email' => 'cliente@shopla.test',
            'plan' => 'free',
        ]);

        Store::create([
            'user_id' => $user->id,
            'name' => 'Loja Cliente',
            'slug' => 'loja-cliente',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $user), [
                'name' => 'Cliente Atualizado',
                'last_name' => 'Teste',
                'email' => 'cliente@shopla.test',
                'phone' => '18999999999',
                'document' => '12345678900',
                'plan' => 'pro',
                'store_is_active' => '0',
            ])
            ->assertRedirect(route('admin.users.show', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Cliente Atualizado',
            'plan' => 'pro',
        ]);

        $this->assertDatabaseHas('stores', [
            'user_id' => $user->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_update_global_plan_settings(): void
    {
        config(['admin.emails' => ['admin@shopla.test']]);

        $admin = User::factory()->create(['email' => 'admin@shopla.test']);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'free_limit' => 6,
                'plus_limit' => 55,
                'plus_price' => 8.99,
                'pro_limit' => 120,
                'pro_price' => 14.99,
                'premium_limit' => 240,
                'premium_price' => 24.99,
                'annual_discount_percent' => 12,
            ])
            ->assertRedirect(route('admin.settings.index'));

        $this->assertDatabaseHas('system_settings', [
            'key' => 'plans.plus.price',
            'value' => '8.99',
        ]);

        $plans = app(PlanCatalog::class)->all();

        $this->assertSame(55, $plans['plus']['limit']);
        $this->assertSame(8.99, $plans['plus']['price']);
        $this->assertSame(94.93, $plans['plus']['annual_price']);
        $this->assertTrue(SystemSetting::where('key', 'plans.annual_discount_percent')->exists());
    }
}
