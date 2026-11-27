<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $orders = Order::with('user')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()->paginate(15)->withQueryString();

        $counts = Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.orders', compact('orders', 'status', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'confirmations', 'user']);

        return view('admin.order-show', compact('order'));
    }

    /** Flowchart: Admin cek mutasi & ubah status invoice -> LUNAS -> dikirim */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate(['status' => ['required', Rule::in(array_keys(Order::STATUSES))]]);

        $order->status = $data['status'];
        if ($data['status'] === 'lunas' && ! $order->paid_at) {
            $order->paid_at = now();
        }
        $order->save();

        return back()->with('success', "Status {$order->invoice} diubah menjadi {$order->statusLabel()}.");
    }
}
