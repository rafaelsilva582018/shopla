<?php

namespace Tests\Feature;

use App\Livewire\PublicStore;
use App\Livewire\StoreCart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StorefrontAndProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_can_create_more_than_one_product(): void
    {
        $user = User::create([
            'name' => 'Rafael',
            'email' => 'rafael@example.com',
            'password' => 'password',
            'plan' => 'free',
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Loja Teste',
            'slug' => 'loja-teste',
            'whatsapp' => '18999999999',
        ]);

        Product::create([
            'store_id' => $store->id,
            'name' => 'Produto 1',
            'slug' => 'produto-1',
            'price' => 10,
            'availability_status' => 'sob_encomenda',
            'stock_quantity' => 0,
            'track_stock' => false,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Produto 2',
            'price' => '19,90',
            'availability_status' => 'sob_encomenda',
            'stock_quantity' => '',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'store_id' => $store->id,
            'name' => 'Produto 2',
        ]);
    }

    public function test_public_store_filters_products(): void
    {
        $user = User::create([
            'name' => 'Rafael',
            'email' => 'rafael2@example.com',
            'password' => 'password',
            'plan' => 'free',
        ]);

        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Loja Teste',
            'slug' => 'loja-teste',
            'whatsapp' => '18999999999',
        ]);

        $category = Category::create([
            'store_id' => $store->id,
            'name' => 'HQs',
            'slug' => 'hqs',
        ]);

        Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Produto filtrado',
            'slug' => 'produto-filtrado',
            'price' => 20,
            'availability_status' => 'pronta_entrega',
            'stock_quantity' => 0,
            'track_stock' => false,
            'is_active' => true,
            'is_featured' => false,
        ]);

        Product::create([
            'store_id' => $store->id,
            'name' => 'Produto fora',
            'slug' => 'produto-fora',
            'price' => 50,
            'availability_status' => 'sob_encomenda',
            'stock_quantity' => 0,
            'track_stock' => false,
            'is_active' => true,
            'is_featured' => false,
        ]);

        Livewire::test(PublicStore::class, ['store' => $store])
            ->set('selectedCategory', (string) $category->id)
            ->set('availabilityStatus', 'pronta_entrega')
            ->set('maxPrice', '25')
            ->assertSee('Produto filtrado')
            ->assertDontSee('Produto fora');
    }

    public function test_public_store_filters_products_from_query_string(): void
    {
        $user = User::create([
            'name' => 'Rafael',
            'email' => 'rafael3@example.com',
            'password' => 'password',
            'plan' => 'free',
        ]);

        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Loja Teste',
            'slug' => 'loja-teste',
            'whatsapp' => '18999999999',
        ]);

        $category = Category::create([
            'store_id' => $store->id,
            'name' => 'HQs',
            'slug' => 'hqs',
        ]);

        Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Produto filtrado',
            'slug' => 'produto-filtrado-query',
            'price' => 20,
            'availability_status' => 'pronta_entrega',
            'stock_quantity' => 0,
            'track_stock' => false,
            'is_active' => true,
            'is_featured' => false,
        ]);

        Product::create([
            'store_id' => $store->id,
            'name' => 'Produto fora',
            'slug' => 'produto-fora-query',
            'price' => 50,
            'availability_status' => 'sob_encomenda',
            'stock_quantity' => 0,
            'track_stock' => false,
            'is_active' => true,
            'is_featured' => false,
        ]);

        $this->get(route('store.public', [
            'slug' => $store->slug,
            'categoria' => $category->id,
            'disponibilidade' => 'pronta_entrega',
            'preco_max' => 25,
        ]))
            ->assertOk()
            ->assertSee('Produto filtrado')
            ->assertDontSee('Produto fora');
    }

    public function test_public_store_dispatches_add_product_event_to_cart(): void
    {
        $user = User::create([
            'name' => 'Rafael',
            'email' => 'rafael4@example.com',
            'password' => 'password',
            'plan' => 'free',
        ]);

        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Loja Teste',
            'slug' => 'loja-teste',
            'whatsapp' => '18999999999',
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'name' => 'Produto do carrinho',
            'slug' => 'produto-do-carrinho',
            'price' => 20,
            'availability_status' => 'pronta_entrega',
            'stock_quantity' => 0,
            'track_stock' => false,
            'is_active' => true,
            'is_featured' => false,
        ]);

        Livewire::test(PublicStore::class, ['store' => $store])
            ->call('addProductToCart', $product->id)
            ->assertDispatchedTo(StoreCart::class, 'add-product', productId: $product->id)
            ->assertDispatched('add-product', productId: $product->id);
    }

    public function test_cart_adds_product_when_storefront_event_is_received(): void
    {
        $user = User::create([
            'name' => 'Rafael',
            'email' => 'rafael5@example.com',
            'password' => 'password',
            'plan' => 'free',
        ]);

        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Loja Teste',
            'slug' => 'loja-teste',
            'whatsapp' => '18999999999',
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'name' => 'Produto no carrinho',
            'slug' => 'produto-no-carrinho',
            'price' => 20,
            'availability_status' => 'pronta_entrega',
            'stock_quantity' => 0,
            'track_stock' => false,
            'is_active' => true,
            'is_featured' => false,
        ]);

        Livewire::test(StoreCart::class, ['store' => $store])
            ->call('openCart')
            ->assertSet('open', true)
            ->call('closeCart')
            ->assertSet('open', false)
            ->dispatch('add-product', productId: $product->id)
            ->assertSet('open', true)
            ->assertSet('cart', function (array $cart) use ($product): bool {
                return isset($cart[$product->id])
                    && $cart[$product->id]['name'] === 'Produto no carrinho'
                    && $cart[$product->id]['quantity'] === 1;
            });
    }
}
