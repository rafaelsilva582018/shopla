<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $store = Auth::user()->store;

        abort_if(!$store, 404);

        $period = $request->get('period', 'month');
        $startDate = null;
        $periodLabel = 'Todos os pedidos';

        if ($period === 'today') {
            $startDate = Carbon::today();
            $periodLabel = 'Hoje';
        } elseif ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $periodLabel = 'Esta semana';
        } elseif ($period === 'all') {
            $periodLabel = 'Todos os pedidos';
        } else {
            $period = 'month';
            $startDate = Carbon::now()->startOfMonth();
            $periodLabel = 'Este mes';
        }

        $rankingQuery = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.store_id', $store->id)
            ->where('orders.status', '!=', 'cancelado')
            ->select(
                'order_items.product_id',
                DB::raw('COALESCE(products.name, order_items.product_name) as name'),
                DB::raw('MAX(products.image) as image'),
                DB::raw('SUM(order_items.quantity) as sold_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count')
            )
            ->groupBy('order_items.product_id', 'order_items.product_name', 'products.name')
            ->orderByDesc('sold_quantity')
            ->orderByDesc('total_revenue');

        if ($startDate) {
            $rankingQuery->where('orders.created_at', '>=', $startDate);
        }

        $ranking = $rankingQuery->get();

        $totalSold = (int) $ranking->sum('sold_quantity');
        $totalRevenue = (float) $ranking->sum('total_revenue');
        $topProduct = $ranking->first();

        return view('rankings.index', compact(
            'store',
            'period',
            'periodLabel',
            'ranking',
            'totalSold',
            'totalRevenue',
            'topProduct',
        ));
    }
}
