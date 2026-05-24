<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanSubscription;
use App\Models\User;
use App\Services\Plans\PlanCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request, PlanCatalog $planCatalog): View
    {
        $plans = $planCatalog->all();
        $plan = $request->query('plan');
        $search = trim((string) $request->query('search'));

        $users = User::query()
            ->with(['store' => fn ($query) => $query->withCount(['products', 'orders'])])
            ->withCount('planSubscriptions')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('store', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('slug', 'like', "%{$search}%");
                        });
                });
            })
            ->when($plan, function ($query) use ($plan) {
                if ($plan === 'free') {
                    $query->where(fn ($query) => $query->whereNull('plan')->orWhere('plan', 'free'));

                    return;
                }

                $query->where('plan', $plan);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'plans', 'plan', 'search'));
    }

    public function show(User $user, PlanCatalog $planCatalog): View
    {
        $plans = $planCatalog->all();

        $user->load([
            'store' => fn ($query) => $query->withCount(['products', 'orders', 'categories']),
            'planSubscriptions' => fn ($query) => $query->latest()->take(10),
        ]);
        $user->loadCount('planSubscriptions');

        $subscriptionStats = PlanSubscription::query()
            ->where('user_id', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.users.show', compact('user', 'plans', 'subscriptionStats'));
    }

    public function update(Request $request, User $user, PlanCatalog $planCatalog): RedirectResponse
    {
        $planKeys = $planCatalog->keys();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'document' => ['nullable', 'string', 'max:40'],
            'plan' => ['required', Rule::in($planKeys)],
            'store_is_active' => ['nullable', 'boolean'],
        ]);

        $oldPlan = $user->plan ?: 'free';

        $user->update([
            'name' => $data['name'],
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'document' => $data['document'] ?? null,
            'plan' => $data['plan'],
            'plan_started_at' => $oldPlan !== $data['plan'] ? now() : $user->plan_started_at,
        ]);

        if ($user->store) {
            $user->store->update([
                'is_active' => $request->boolean('store_is_active'),
            ]);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Conta atualizada com sucesso.');
    }
}
