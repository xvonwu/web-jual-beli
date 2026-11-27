@extends('layouts.app')
@section('title', 'Invoice '.$order->invoice)

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('orders.index') }}">Pesanan Saya</a> / Invoice</div>
        <h1>{{ $order->invoice }}</h1>
        <div class="ghost" aria-hidden="true">INVOICE</div>
    </div>

    <div class="layout-2">
        <div class="card">
            <dl class="meta" style="margin-top:0">
                <dt>Tanggal pesanan</dt><dd>{{ $order->created_at->translatedFormat('l, d F Y, H:i') }} WIB</dd>
                <dt>Status</dt><dd><span class="pill pill-{{ $order->status }}">{{ $order->statusLabel() }}</span></dd>
            </dl>
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
                @if ($order->discount)
                    <div class="summary-row"><span>Diskon ({{ $order->coupon_code }})</span><span>- @rupiah($order->discount)</span></div>
                @endif
                <div class="summary-row"><span>Ongkir ({{ $order->courierLabel() }})</span><span>@rupiah($order->shipping_cost)</span></div>
                <div class="summary-row total"><span>Grand Total</span><span class="price">@rupiah($order->grand_total)</span></div>
            </div>

            <h2 class="block-title" style="margin:30px 0 10px">Data Penerima</h2>
            <dl class="meta" style="margin:0">
                <dt>Nama</dt><dd>{{ $order->customer_name }}</dd>
                <dt>HP</dt><dd>{{ $order->phone }}</dd>
                <dt>Email</dt><dd>{{ $order->email }}</dd>
                @if ($order->address)<dt>Alamat</dt><dd>{{ $order->fullAddress() }}</dd>@endif
                @if ($order->note)<dt>Catatan</dt><dd>{{ $order->note }}</dd>@endif
            </dl>
        </div>

        <aside class="card">
            <h2 class="block-title" style="margin:0 0 10px">Pembayaran</h2>
            <p style="margin:0 0 6px"><b>{{ $order->paymentLabel() }}</b></p>
            @if ($order->payment_method_id === \App\Models\Order::PAYMENT_TRANSFER)
                @if ($order->status === 'menunggu_pembayaran')
                    <div class="alert alert-info">
                        Silakan transfer <b>@rupiah($order->grand_total)</b> ke<br><b>{{ config('store.bank') }}</b> @include('partials.sample-tag'),
                        lalu isi form konfirmasi pembayaran.
                    </div>
                    <a href="{{ route('payment.form', ['invoice' => $order->invoice]) }}" class="btn btn-red btn-block">Konfirmasi Pembayaran</a>
                @elseif ($order->status === 'menunggu_verifikasi')
                    <div class="alert alert-info">Konfirmasi sudah diterima. Admin akan memverifikasi di hari &amp; jam kerja.</div>
                @endif
            @else
                <div class="alert alert-info">Bayar cash saat mengambil barang di kantor ({{ config('store.hours') }}). Bawa nomor invoice ini.</div>
            @endif
            @if ($order->paid_at)
                <p style="font-size:13px;color:var(--muted)">Lunas pada {{ $order->paid_at->translatedFormat('l, d F Y, H:i') }} WIB</p>
            @endif

            @foreach ($order->confirmations as $c)
                <div style="border-top:1px solid var(--line);padding-top:12px;margin-top:12px;font-size:13px">
                    <b>Konfirmasi {{ $c->created_at->translatedFormat('d F Y, H:i') }} WIB</b><br>
                    {{ $c->dari_bank }} a.n. {{ $c->rekening_pengirim }}, @rupiah($c->jml_transfer)
                </div>
            @endforeach
        </aside>
    </div>
@endsection
