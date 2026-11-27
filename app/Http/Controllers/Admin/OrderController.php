<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $revenue = (int) Order::whereIn('status', ['lunas', 'dikirim', 'selesai'])->sum('grand_total');
        $lowStockProducts = Product::where('is_preorder', false)
            ->where('stock', '<=', 2)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('admin.orders', compact('orders', 'status', 'counts', 'revenue', 'lowStockProducts'));
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
        $prevStatus = $order->status;

        DB::transaction(function () use ($order, $data, $prevStatus) {
            // Kembalikan stok otomatis jika pesanan dibatalkan
            if ($prevStatus !== 'batal' && $data['status'] === 'batal') {
                $order->loadMissing('items');
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->increment('stock', $item->qty);
                    }
                }
            }

            $order->status = $data['status'];
            if ($data['status'] === 'lunas' && ! $order->paid_at) {
                $order->paid_at = now();
            }
            $order->save();
        });

        return back()->with('success', "Status {$order->invoice} diubah menjadi {$order->statusLabel()}.");
    }
}
