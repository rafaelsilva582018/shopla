<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    public function index()
    {
        $store = Auth::user()->store;
        $statusFilter = request('status', 'todos');

        $productsQuery = $store->products()
            ->with(['category', 'categories'])
            ->orderBy('name');

        if ($statusFilter === 'baixo') {
            $productsQuery->where('track_stock', true)
                ->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 5);
        }

        if ($statusFilter === 'esgotado') {
            $productsQuery->where(function ($query) {
                $query->where('availability_status', 'esgotado')
                    ->orWhere(function ($q) {
                        $q->where('track_stock', true)
                            ->where('stock_quantity', 0);
                    });
            });
        }

        if ($statusFilter === 'livre') {
            $productsQuery->where('track_stock', false)
                ->where('availability_status', '!=', 'esgotado');
        }

        if ($statusFilter === 'controlado') {
            $productsQuery->where('track_stock', true)
                ->where('availability_status', '!=', 'esgotado');
        }

        $products = $productsQuery->get();
        $allProducts = $store->products()->get();

        $totalProducts = $allProducts->count();
        $trackedProducts = $allProducts->where('track_stock', true)->count();
        $freeProducts = $allProducts
            ->where('track_stock', false)
            ->where('availability_status', '!=', 'esgotado')
            ->count();
        $lowStockProducts = $allProducts
            ->where('track_stock', true)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 5)
            ->count();
        $outOfStockProducts = $allProducts->filter(function ($product) {
            return $product->availability_status === 'esgotado'
                || ($product->track_stock && (int) $product->stock_quantity === 0);
        })->count();

        return view('stock.index', compact(
            'store',
            'products',
            'statusFilter',
            'totalProducts',
            'trackedProducts',
            'freeProducts',
            'lowStockProducts',
            'outOfStockProducts'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $store = Auth::user()->store;

        abort_if($product->store_id !== $store->id, 403);

        $data = $request->validate([
            'availability_status' => ['required', Rule::in(array_keys(Product::AVAILABILITY_STATUSES))],
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $trackStock = $data['availability_status'] === 'esgotado' || $request->filled('stock_quantity');

        $product->update([
            'availability_status' => $data['availability_status'],
            'stock_quantity' => $data['availability_status'] === 'esgotado'
                ? 0
                : (int) ($data['stock_quantity'] ?? 0),
            'track_stock' => $trackStock,
        ]);

        return back()->with('success', 'Estoque atualizado!');
    }
}
