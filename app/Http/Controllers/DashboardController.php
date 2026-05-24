<?php

namespace App\Http\Controllers;

use App\Models\DismissedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('onboarding.index');
        }

        if (!$store->onboarding_completed_at) {
            return redirect()->route('onboarding.index');
        }

        $period = $request->get('period', 'month');

        if ($period === 'today') {
            $startDate = Carbon::today();
            $periodLabel = 'Hoje';
        } elseif ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $periodLabel = 'Esta semana';
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $period = 'month';
            $periodLabel = 'Este mes';
        }

        $ordersQuery = $store->orders()
            ->where('created_at', '>=', $startDate);

        $totalOrders = (clone $ordersQuery)->count();

        $pendingOrders = (clone $ordersQuery)
            ->whereIn('status', ['novo', 'pendente'])
            ->count();

        $totalRevenue = (clone $ordersQuery)
            ->whereNotIn('status', ['cancelado'])
            ->sum('total');

        $confirmedOrders = (clone $ordersQuery)
            ->whereIn('status', ['pago', 'confirmado', 'concluido', 'concluído'])
            ->count();

        $cancelledOrders = (clone $ordersQuery)
            ->where('status', 'cancelado')
            ->count();

        $averageTicket = $confirmedOrders > 0 ? $totalRevenue / $confirmedOrders : 0;

        $products = $store->products()->get();
        $totalProducts = $products->count();
        $totalCategories = $store->categories()->count();
        $activeProducts = $products->where('is_active', true)->count();
        $lowStockProducts = $products
            ->where('track_stock', true)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 5)
            ->count();
        $outOfStockProducts = $products->filter(function ($product) {
            return $product->availability_status === 'esgotado'
                || ($product->track_stock && (int) $product->stock_quantity === 0);
        })->count();

        $latestOrders = $store->orders()
            ->with('items')
            ->latest()
            ->take(4)
            ->get();

        $revenueByDay = (clone $ordersQuery)
            ->whereNotIn('status', ['cancelado'])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $revenueByDay->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('d/m');
        })->toArray();

        $chartRevenue = $revenueByDay->pluck('total')->map(function ($total) {
            return (float) $total;
        })->toArray();

        $dashboardNotifications = $this->notifications(
            $user,
            $store,
            $pendingOrders,
            $lowStockProducts,
            $outOfStockProducts,
            $totalProducts,
            $totalCategories
        );

        return view('dashboard', compact(
            'store',
            'period',
            'periodLabel',
            'totalOrders',
            'pendingOrders',
            'totalRevenue',
            'confirmedOrders',
            'cancelledOrders',
            'averageTicket',
            'totalProducts',
            'totalCategories',
            'activeProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'latestOrders',
            'chartLabels',
            'chartRevenue',
            'dashboardNotifications',
        ));
    }

    private function notifications($user, $store, int $pendingOrders, int $lowStockProducts, int $outOfStockProducts, int $totalProducts, int $totalCategories): array
    {
        $notifications = [];

        if ($pendingOrders > 0) {
            $notifications[] = [
                'key' => 'pending-orders-' . $pendingOrders,
                'title' => $pendingOrders . ' pedido(s) pendente(s)',
                'text' => 'Responda os pedidos novos para nao perder venda.',
                'icon' => 'receipt',
                'href' => route('orders.index', ['status' => 'pendentes']),
            ];
        }

        if ($lowStockProducts > 0) {
            $notifications[] = [
                'key' => 'low-stock-' . $lowStockProducts,
                'title' => $lowStockProducts . ' produto(s) com baixo estoque',
                'text' => 'Revise as quantidades antes que acabem.',
                'icon' => 'stock',
                'href' => route('stock.index', ['status' => 'baixo']),
            ];
        }

        if ($outOfStockProducts > 0) {
            $notifications[] = [
                'key' => 'out-of-stock-' . $outOfStockProducts,
                'title' => $outOfStockProducts . ' produto(s) esgotado(s)',
                'text' => 'Atualize disponibilidade ou reponha o estoque.',
                'icon' => 'alert',
                'href' => route('stock.index', ['status' => 'esgotado']),
            ];
        }

        if ($totalProducts === 0) {
            $notifications[] = [
                'key' => 'first-product',
                'title' => 'Cadastre seu primeiro produto',
                'text' => 'Sua vitrine precisa de pelo menos um item para comecar a vender.',
                'icon' => 'package',
                'href' => route('products.create'),
            ];
        }

        if ($totalCategories === 0) {
            $notifications[] = [
                'key' => 'first-category',
                'title' => 'Organize sua vitrine por categorias',
                'text' => 'Categorias ajudam o cliente a encontrar produtos mais rapido.',
                'icon' => 'category',
                'href' => route('categories.index'),
            ];
        }

        $missingBillingData = collect([
            $user->document,
            $user->zip_code,
            $user->address,
            $user->address_number,
            $user->district,
        ])->contains(fn ($value) => blank($value));

        if ($missingBillingData) {
            $notifications[] = [
                'key' => 'missing-billing-data',
                'title' => 'Complete os dados da conta',
                'text' => 'CPF/CNPJ e endereco sao usados para assinatura e organizacao.',
                'icon' => 'settings',
                'href' => route('profile.edit'),
            ];
        }

        $latestSubscription = $user->planSubscriptions()->latest()->first();

        if ($latestSubscription?->status === 'pending') {
            $notifications[] = [
                'key' => 'pending-subscription-' . $latestSubscription->id,
                'title' => 'Pagamento de plano pendente',
                'text' => 'Finalize o checkout para liberar o novo limite.',
                'icon' => 'diamond',
                'href' => route('profile.edit'),
            ];
        }

        $limit = $user->productLimit();

        if ($limit && $totalProducts >= max(1, $limit - 1)) {
            $notifications[] = [
                'key' => 'product-limit-' . $totalProducts . '-' . $limit,
                'title' => 'Limite de produtos quase cheio',
                'text' => "Voce esta usando {$totalProducts} de {$limit} produtos.",
                'icon' => 'diamond',
                'href' => route('profile.edit'),
            ];
        }

        $dismissedKeys = DismissedNotification::query()
            ->where('user_id', $user->id)
            ->pluck('notification_key')
            ->all();

        return collect($notifications)
            ->reject(fn ($notification) => in_array($notification['key'], $dismissedKeys, true))
            ->take(6)
            ->values()
            ->all();
    }
}
