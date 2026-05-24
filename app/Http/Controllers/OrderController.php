<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private const ALLOWED_STATUSES = [
        'pendente',
        'novo',
        'em andamento',
        'concluído',
        'pago',
        'confirmado',
        'cancelado',
    ];

    public function index()
    {
        $store = Auth::user()->store;

        $statusFilter = request('status', 'todos');

        $ordersQuery = $store->orders()
            ->with('items')
            ->latest();

        if ($statusFilter === 'pendentes') {
            $ordersQuery->whereIn('status', ['pendente', 'novo']);
        }

        if ($statusFilter === 'andamento') {
            $ordersQuery->where('status', 'em andamento');
        }

        if ($statusFilter === 'concluidos') {
            $ordersQuery->whereIn('status', ['concluído', 'pago', 'confirmado']);
        }

        if ($statusFilter === 'cancelados') {
            $ordersQuery->where('status', 'cancelado');
        }

        $orders = $ordersQuery->get();

        return view('orders.index', compact('orders', 'statusFilter'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string', Rule::in(self::ALLOWED_STATUSES)],
        ]);

        $store = Auth::user()->store;
        abort_if($order->store_id !== $store->id, 403);

        $order->update([
            'status' => $request->status,
        ]);

        $statusFilter = $request->input('status_filter', 'todos');
        $routeParameters = $statusFilter === 'todos' ? [] : ['status' => $statusFilter];

        return redirect()
            ->route('orders.index', $routeParameters)
            ->with('success', 'Status atualizado!');
    }
}
