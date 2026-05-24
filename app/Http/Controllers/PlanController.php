<?php

namespace App\Http\Controllers;

use App\Services\Plans\PlanCatalog;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request, PlanCatalog $planCatalog)
    {
        $user = $request->user();
        $store = $user->store;
        $plans = $planCatalog->all();
        $latestSubscription = $user->planSubscriptions()->latest()->first();

        return view('plans.index', compact('user', 'store', 'plans', 'latestSubscription'));
    }
}
