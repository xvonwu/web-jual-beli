<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /** Riwayat pesanan milik user */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /** /invoice/{invoice} */
    public function show(Request $request, string $invoice)
    {
        $order = Order::with(['items', 'confirmations'])->where('invoice', $invoice)->firstOrFail();
        abort_unless($order->user_id === $request->user()->id || $request->user()->is_admin, 403);

        return view('orders.show', compact('order'));
    }

    /** /payment_confirm : form pelunasan (wajib login), invoice dipilih dari akun */
    public function paymentForm(Request $request)
    {
        $orders = $request->user()->orders()
            ->where('status', 'menunggu_pembayaran')
            ->where('payment_method_id', Order::PAYMENT_TRANSFER)
            ->latest()->get();

        return view('orders.payment', [
            'orders' => $orders,
            'selected' => $request->query('invoice'),
            'user' => $request->user(),
        ]);
    }

    /** POST /payment_confirm_save */
    public function paymentSave(Request $request)
    {
        $data = $request->validate([
            'invoice' => ['required', 'string'],
            'email' => ['required', 'email'],
            'nama' => ['required', 'string', 'max:100'],
            'jml_transfer' => ['required', 'integer', 'min:1'],
            'dari_bank' => ['required', Rule::in(array_merge(array_merge(...array_values(config('store.banks'))), ['Lainnya']))],
            'dari_bank_lain' => ['required_if:dari_bank,Lainnya', 'nullable', 'string', 'max:50'],
            'rekening_pengirim' => ['required', 'string', 'max:100'],
        ], [
            'dari_bank.required' => 'Pilih bank atau e-wallet pengirim.',
            'dari_bank.in' => 'Pilih bank dari daftar, atau pilih Lainnya.',
            'dari_bank_lain.required_if' => 'Tulis nama bank pengirim.',
        ]);

        if ($data['dari_bank'] === 'Lainnya') {
            $data['dari_bank'] = trim($data['dari_bank_lain']);
        }
        unset($data['dari_bank_lain']);

        $order = $request->user()->orders()->where('invoice', $data['invoice'])->first();

        if (! $order || $order->status !== 'menunggu_pembayaran') {
            return back()->withInput()->withErrors(['invoice' => 'Invoice tidak ditemukan atau sudah dikonfirmasi.']);
        }

        // Waktu transfer = saat konfirmasi dikirim (jam server, WIB)
        $order->confirmations()->create(collect($data)->except('invoice')->all() + [
            'user_id' => $request->user()->id,
            'tgl_transfer' => now(),
            // Rekening tujuan tidak diisi pembeli: selalu rekening toko dari .env (STORE_BANK)
            'rekening_tujuan' => config('store.bank'),
        ]);
        $order->update(['status' => 'menunggu_verifikasi']);

        return redirect()->route('orders.show', $order->invoice)
            ->with('popup', [
                'type' => 'payment',
                'title' => 'Pembayaran berhasil dikirim!',
                'message' => 'Konfirmasi transfer Rp '.number_format($data['jml_transfer'], 0, ',', '.').' untuk invoice '.$order->invoice.' sudah kami terima. Admin memverifikasi di hari dan jam kerja.',
            ]);
    }

    /** GET /bayar/{invoice} : halaman pembayaran otomatis (QRIS / Virtual Account) */
    public function autoPay(Request $request, string $invoice)
    {
        $order = Order::with('items')->where('invoice', $invoice)->firstOrFail();
        abort_unless($order->user_id === $request->user()->id || $request->user()->is_admin, 403);

        if ($order->payment_method_id !== Order::PAYMENT_AUTO || $order->status !== 'menunggu_pembayaran') {
            return redirect()->route('orders.show', $order->invoice);
        }

        $vaNumber = '8808'.str_pad((string) $order->id, 10, '0', STR_PAD_LEFT);

        return view('orders.auto-pay', compact('order', 'vaNumber'));
    }

    /** POST /bayar/{invoice}/proses : simulasi callback payment gateway -> otomatis lunas */
    public function autoPayConfirm(Request $request, string $invoice)
    {
        $order = Order::where('invoice', $invoice)->firstOrFail();
        abort_unless($order->user_id === $request->user()->id || $request->user()->is_admin, 403);

        if ($order->payment_method_id !== Order::PAYMENT_AUTO || $order->status !== 'menunggu_pembayaran') {
            return redirect()->route('orders.show', $order->invoice);
        }

        $order->confirmations()->create([
            'user_id' => $order->user_id,
            'email' => $order->email,
            'nama' => $order->customer_name,
            'tgl_transfer' => now(),
            'jml_transfer' => $order->grand_total,
            'rekening_tujuan' => 'Gateway QRIS / VA Otomatis',
            'dari_bank' => 'QRIS / VA Otomatis',
            'rekening_pengirim' => 'Pembayaran Instan',
        ]);

        $order->update([
            'status' => 'lunas',
            'paid_at' => now(),
        ]);

        return redirect()->route('orders.show', $order->invoice)
            ->with('popup', [
                'type' => 'payment',
                'title' => 'Pembayaran otomatis berhasil!',
                'message' => 'Pembayaran Rp '.number_format($order->grand_total, 0, ',', '.').' untuk invoice '.$order->invoice.' terverifikasi otomatis. Status pesanan sekarang Lunas.',
            ]);
    }
}
