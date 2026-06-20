<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Database\Seeders\DemoStoresSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoStoresSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_stores_can_be_seeded_without_duplicates(): void
    {
        $this->seed(DemoStoresSeeder::class);
        $this->seed(DemoStoresSeeder::class);

        $this->assertSame(5, User::where('email', 'like', 'demo%@shopla.test')->count());
        $this->assertSame(5, Store::where('slug', 'like', 'demo-%')->count());
        $this->assertFalse(
            Store::where('slug', 'like', 'demo-%')->get()->contains(
                fn (Store $store) => str_starts_with((string) $store->instagram, '@')
            )
        );
        $this->assertSame(100, Product::whereHas('store', fn ($query) => $query->where('slug', 'like', 'demo-%'))->count());
        $this->assertTrue(
            Store::where('slug', 'like', 'demo-%')
                ->withCount('products')
                ->get()
                ->every(fn (Store $store) => $store->products_count === 20)
        );
        $this->assertSame(30, Order::whereHas('store', fn ($query) => $query->where('slug', 'like', 'demo-%'))->count());
        $this->assertSame(60, Order::whereHas('store', fn ($query) => $query->where('slug', 'like', 'demo-%'))->withCount('items')->get()->sum('items_count'));
    }
}
