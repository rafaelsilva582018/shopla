<?php

namespace Tests\Feature;

use App\Models\DismissedNotification;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_dismiss_one_dashboard_notification(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'document' => '12345678909',
            'zip_code' => '16700000',
            'address' => 'Rua Teste',
            'address_number' => '123',
            'district' => 'Centro',
        ]);

        Store::create([
            'user_id' => $user->id,
            'name' => 'Loja Teste',
            'slug' => 'loja-teste',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Cadastre seu primeiro produto');

        $this->actingAs($user)
            ->delete(route('notifications.dismiss'), [
                'notification_key' => 'first-product',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('dismissed_notifications', [
            'user_id' => $user->id,
            'notification_key' => 'first-product',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Cadastre seu primeiro produto');
    }

    public function test_user_can_clear_current_dashboard_notifications(): void
    {
        $user = User::factory()->create();

        Store::create([
            'user_id' => $user->id,
            'name' => 'Loja Teste',
            'slug' => 'loja-teste',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('notifications.clear'), [
                'notification_keys' => ['first-product', 'first-category', 'missing-billing-data'],
            ])
            ->assertRedirect();

        $this->assertSame(3, DismissedNotification::where('user_id', $user->id)->count());

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Cadastre seu primeiro produto')
            ->assertDontSee('Organize sua vitrine por categorias')
            ->assertDontSee('Complete os dados da conta');
    }
}
