<?php

namespace App\Livewire;

use App\Models\Store;
use Illuminate\View\View;
use Livewire\Component;

class PublicStore extends Component
{
    public Store $store;

    public array $storefrontTheme = [];

    public string $search = '';
    public string $selectedCategory = '';
    public string $availabilityStatus = '';
    public string $sortBy = 'best_sellers';
    public string $minPrice = '';
    public string $maxPrice = '';

    public function mount(Store $store, array $storefrontTheme = []): void
    {
        $this->store = $store;
        $this->storefrontTheme = $storefrontTheme ?: $store->storefrontTheme();
        $this->search = (string) request('busca', '');
        $this->selectedCategory = (string) request('categoria', '');
        $this->availabilityStatus = (string) request('disponibilidade', '');
        $this->sortBy = (string) request('ordem', 'best_sellers');
        $this->minPrice = (string) request('preco_min', '');
        $this->maxPrice = (string) request('preco_max', '');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->availabilityStatus = '';
        $this->sortBy = 'best_sellers';
        $this->minPrice = '';
        $this->maxPrice = '';
    }

    public function selectCategory(string $categoryId = ''): void
    {
        $this->selectedCategory = $categoryId;
    }

    public function applyFilters(): void
    {
        //
    }

    public function addProductToCart(int $productId): void
    {
        $this->dispatch('add-product', productId: $productId)->to(StoreCart::class);
    }

    public function render(): View
    {
        $searchOperator = $this->store->getConnection()->getDriverName() === 'pgsql'
            ? 'ilike'
            : 'like';

        $products = $this->store->products()
            ->with(['category', 'categories'])
            ->withSum('orderItems as sold_quantity', 'quantity')
            ->where('is_active', true)
            ->when($this->search, function ($query) use ($searchOperator) {
                $query->where(function ($q) use ($searchOperator) {
                    $q->where('name', $searchOperator, '%' . $this->search . '%')
                      ->orWhere('description', $searchOperator, '%' . $this->search . '%')
                      ->orWhereHas('category', function ($categoryQuery) use ($searchOperator) {
                          $categoryQuery->where('name', $searchOperator, '%' . $this->search . '%');
                      })
                      ->orWhereHas('categories', function ($categoryQuery) use ($searchOperator) {
                          $categoryQuery->where('name', $searchOperator, '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where(function ($categoryFilter) {
                    $categoryFilter->where('category_id', $this->selectedCategory)
                        ->orWhereHas('categories', function ($categoryQuery) {
                            $categoryQuery->where('categories.id', $this->selectedCategory);
                        });
                });
            })
            ->when($this->availabilityStatus, function ($query) {
                $query->where('availability_status', $this->availabilityStatus);
            })
            ->when($this->minPrice !== '', function ($query) {
                $query->where('price', '>=', (float) $this->minPrice);
            })
            ->when($this->maxPrice !== '', function ($query) {
                $query->where('price', '<=', (float) $this->maxPrice);
            });

        $products->orderByDesc('is_featured');

        $products = match ($this->sortBy) {
            'recent' => $products->latest(),
            'price_asc' => $products->orderBy('price'),
            'price_desc' => $products->orderByDesc('price'),
            'name' => $products->orderBy('name'),
            default => $products
                ->orderByDesc('sold_quantity')
                ->latest(),
        };

        $products = $products->get();

        return view('components.public-store', compact('products'));
    }
}
