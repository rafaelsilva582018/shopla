<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class StoreCart extends Component
{
    public Store $store;

    public array $storefrontTheme = [];

    public array $cart = [];

    public bool $open = false;

    public ?string $stockMessage = null;

    public string $customer_name = '';
    public string $customer_whatsapp = '';
    public string $customer_address = '';
    public string $notes = '';

    public function mount(Store $store, array $storefrontTheme = []): void
    {
        $this->store = $store;
        $this->storefrontTheme = $storefrontTheme ?: $store->storefrontTheme();
    }

    public function openCart(): void
    {
        $this->open = true;
    }

    public function closeCart(): void
    {
        $this->open = false;
    }

    #[On('add-product')]
    public function addToCart(int $productId): void
    {
        $this->stockMessage = null;

        $product = Product::where('store_id', $this->store->id)
            ->whereKey($productId)
            ->first();

        if (!$product) {
            return;
        }

        if (!$this->canAddProduct($product)) {
            $this->open = true;
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'image' => $product->image,
                'quantity' => 1,
            ];
        }

        $this->open = true;
    }

    public function increase(int $productId): void
    {
        $this->stockMessage = null;

        if (!isset($this->cart[$productId])) {
            return;
        }

        $product = Product::where('store_id', $this->store->id)
            ->whereKey($productId)
            ->first();

        if (!$product || !$this->canAddProduct($product)) {
            return;
        }

        $this->cart[$productId]['quantity']++;
    }

    public function decrease(int $productId): void
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
        } else {
            unset($this->cart[$productId]);
        }
    }

    public function remove(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function getTotalProperty(): float
    {
        return collect($this->cart)->sum(function (array $item): float {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getItemsCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function whatsappLink(): string
    {
        $phone = preg_replace('/\D/', '', $this->store->whatsapp ?? '');

        if (!$phone) {
            return '#';
        }

        if (!str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }

        $message = "Olá! Quero fazer um pedido na loja {$this->store->name}:\n\n";

        foreach ($this->cart as $item) {
            $subtotal = $item['price'] * $item['quantity'];

            $message .= "- {$item['name']} x{$item['quantity']} - R$ " .
                number_format($subtotal, 2, ',', '.') . "\n";
        }

        $message .= "\nTotal: R$ " .
            number_format($this->total, 2, ',', '.') . "\n\n";

        $message .= "Meus dados:\n";
        $message .= "Nome: {$this->customer_name}\n";
        $message .= "WhatsApp: {$this->customer_whatsapp}\n";
        $message .= "Endereço: {$this->customer_address}\n";
        $message .= "Observações: {$this->notes}";

        return "https://wa.me/{$phone}?text=" . rawurlencode($message);
    }

    public function render(): View
    {
        return view('components.store-cart');
    }

    public function confirmOrder(): void
    {
        $this->stockMessage = null;

        if (empty($this->cart)) {
            return;
        }

        foreach ($this->cart as $item) {
            $product = Product::where('store_id', $this->store->id)
                ->whereKey($item['id'])
                ->first();

            if ($product && $product->track_stock && $product->stock_quantity < $item['quantity']) {
                $this->stockMessage = "O produto {$product->name} tem apenas {$product->stock_quantity} unidade(s) em estoque.";
                $this->open = true;
                return;
            }
        }

        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order = Order::create([
            'store_id' => $this->store->id,
            'customer_name' => $this->customer_name,
            'customer_whatsapp' => $this->customer_whatsapp,
            'customer_address' => $this->customer_address,
            'notes' => $this->notes,
            'total' => $this->total,
            'status' => 'pendente',
        ]);

        foreach ($this->cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);

            $product = Product::where('store_id', $this->store->id)
                ->whereKey($item['id'])
                ->first();

            if ($product && $product->track_stock) {
                $product->update([
                    'stock_quantity' => max(0, $product->stock_quantity - $item['quantity']),
                ]);
            }
        }

        $this->redirect($this->whatsappLink());
    }

    private function canAddProduct(Product $product): bool
    {
        if ($product->availability_status === 'esgotado') {
            $this->stockMessage = "{$product->name} está esgotado.";
            return false;
        }

        if (!$product->track_stock) {
            return true;
        }

        $quantityInCart = $this->cart[$product->id]['quantity'] ?? 0;

        if ($quantityInCart >= $product->stock_quantity) {
            $this->stockMessage = "{$product->name} tem apenas {$product->stock_quantity} unidade(s) em estoque.";
            return false;
        }

        return true;
    }
}
