<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PlanSubscription;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\Plans\PlanCatalog;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(PlanCatalog $planCatalog): View
    {
        $plans = $planCatalog->all();
        $startOfMonth = now()->startOfMonth();

        $planCounts = User::query()
            ->selectRaw("COALESCE(plan, 'free') as plan_key, count(*) as total")
            ->groupBy('plan_key')
            ->pluck('total', 'plan_key');

        $metrics = [
            'users' => User::count(),
            'new_users' => User::where('created_at', '>=', $startOfMonth)->count(),
            'stores' => Store::count(),
            'new_stores' => Store::where('created_at', '>=', $startOfMonth)->count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'paid_users' => User::where('plan', '!=', 'free')->count(),
            'active_subscriptions' => PlanSubscription::where('status', 'active')->count(),
            'pending_subscriptions' => PlanSubscription::where('status', 'pending')->count(),
            'stores_without_products' => Store::doesntHave('products')->count(),
            'monthly_revenue' => Order::where('created_at', '>=', $startOfMonth)
                ->whereNotIn('status', ['cancelado'])
                ->sum('total'),
        ];

        $latestUsers = User::query()
            ->with(['store' => fn ($query) => $query->withCount(['products', 'orders'])])
            ->latest()
            ->take(6)
            ->get();

        $latestSubscriptions = PlanSubscription::query()
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'plans',
            'planCounts',
            'metrics',
            'latestUsers',
            'latestSubscriptions',
        ));
    }
}
