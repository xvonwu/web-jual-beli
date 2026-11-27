@extends('layouts.app')
@section('title', 'Admin: '.$order->invoice)

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('admin.orders.index') }}">Panel Admin</a> / Pesanan</div>
        <h1>{{ $order->invoice }}</h1>
        <div class="ghost" aria-hidden="true">CEK</div>
    </div>

    <div class="layout-2">
        <div class="card">
            <div class="table-scroll">
            <table class="table">
                <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
                <tbody>
                @foreach ($order->items as $it)
                    <tr><td>{{ $it->product_name }}</td><td>@rupiah($it->price)</td><td>{{ $it->qty }}</td><td>@rupiah($it->subtotal)</td></tr>
                @endforeach
                </tbody>
            </table>
            </div>
            <div style="max-width:340px;margin-left:auto;margin-top:16px">
                <div class="summary-row"><span>Subtotal</span><span>@rupiah($order->subtotal)</span></div>
                <div class="summary-row"><span>Diskon</span><span>- @rupiah($order->discount)</span></div>
                <div class="summary-row"><span>Ongkir ({{ $order->courierLabel() }})</span><span>@rupiah($order->shipping_cost)</span></div>
                <div class="summary-row total"><span>Grand Total</span><span class="price">@rupiah($order->grand_total)</span></div>
            </div>
            <h2 class="block-title" style="margin:26px 0 10px">Penerima</h2>
            <dl class="meta" style="margin:0">
                <dt>Tanggal pesanan</dt><dd>{{ $order->created_at->translatedFormat('l, d F Y, H:i') }} WIB</dd>
                <dt>Nama</dt><dd>{{ $order->customer_name }} ({{ $order->phone }})</dd>
                <dt>Email</dt><dd>{{ $order->email }}</dd>
                <dt>Alamat</dt><dd>{{ $order->fullAddress() ?: 'Ambil langsung di toko' }}</dd>
                <dt>Pembayaran</dt><dd>{{ $order->paymentLabel() }}@if ($order->paid_at), lunas {{ $order->paid_at->translatedFormat('l, d F Y, H:i') }} WIB @endif</dd>
                <dt>Catatan</dt><dd>{{ $order->note ?: '-' }}</dd>
            </dl>

            <h2 class="block-title" style="margin:26px 0 10px">Konfirmasi Transfer dari Pembeli</h2>
            @forelse ($order->confirmations as $c)
                <dl class="meta" style="background:var(--bg-soft);padding:14px;border-radius:12px;margin:0 0 10px">
                    <dt>Waktu transfer</dt><dd>{{ $c->tgl_transfer->translatedFormat('l, d F Y, H:i') }} WIB</dd>
                    <dt>Jumlah</dt><dd><b>@rupiah($c->jml_transfer)</b> @if ($c->jml_transfer < $order->grand_total)<span class="pill pill-batal">kurang</span>@endif</dd>
                    <dt>Dari</dt><dd>{{ $c->dari_bank }} a.n. {{ $c->rekening_pengirim }}</dd>
                    <dt>Ke rekening</dt><dd>{{ $c->rekening_tujuan }}</dd>
                </dl>
            @empty
                <p style="color:var(--muted)">Belum ada konfirmasi.</p>
            @endforelse
        </div>

        <aside class="card">
            <h2 class="block-title" style="margin-top:0">Ubah Status</h2>
            <p>Status sekarang: <span class="pill pill-{{ $order->status }}">{{ $order->statusLabel() }}</span></p>
            <p style="font-size:13px;color:var(--muted)">Cek mutasi rekening dulu. Kalau dana sudah masuk, ubah ke <b>Lunas</b>, lalu <b>Dikirim</b> setelah paket dikirim/diambil.</p>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf
                <div class="field">
                    <select name="status">
                        @foreach (\App\Models\Order::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-red btn-block">Simpan Status</button>
            </form>
        </aside>
    </div>
@endsection
