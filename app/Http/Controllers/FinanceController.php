<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function index()
    {
        $store = Auth::user()->store;

        $orders = $store->orders()
            ->where('status', '!=', 'cancelado')
            ->latest()
            ->get();

        $totalRevenue = $orders->sum('total');
        $received = $store->orders()
            ->whereIn('status', ['concluído', 'pago', 'confirmado'])
            ->sum('total');

        $toReceive = $totalRevenue - $received;

        return view('finance.index', compact(
            'store',
            'orders',
            'totalRevenue',
            'received',
            'toReceive'
        ));
    }
}
