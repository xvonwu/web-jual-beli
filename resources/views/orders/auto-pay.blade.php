@extends('layouts.app')
@section('title', 'Pembayaran Otomatis '.$order->invoice)

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('orders.show', $order->invoice) }}">{{ $order->invoice }}</a> / Pembayaran Otomatis</div>
        <h1>Bayar Instan</h1>
        <div class="ghost" aria-hidden="true">QRIS / VA</div>
    </div>

    <div class="layout-2">
        <div class="card" style="text-align:center">
            <span class="pill pill-menunggu_verifikasi" style="margin-bottom:12px">Verifikasi Otomatis 24 Jam</span>
            <h2 class="card-title" style="margin-bottom:6px">Scan QRIS atau Transfer Virtual Account</h2>
            <p style="color:var(--muted);margin:0 0 22px">Total yang harus dibayar: <b style="color:var(--ink)">@rupiah($order->grand_total)</b></p>

            <div style="display:inline-block;padding:18px;background:#fff;border:3px solid var(--ink);border-radius:14px;margin-bottom:20px">
                <svg width="180" height="180" viewBox="0 0 21 21" shape-rendering="crispEdges" aria-label="QR Code Pembayaran {{ $order->invoice }}">
                    <rect width="21" height="21" fill="#fff"/>
                    <path d="M1,1 h7 v7 h-7 z M2,2 v5 h5 v-5 z M3,3 h3 v3 h-3 z M13,1 h7 v7 h-7 z M14,2 v5 h5 v-5 z M15,3 h3 v3 h-3 z M1,13 h7 v7 h-7 z M2,14 v5 h5 v-5 z M3,15 h3 v3 h-3 z M9,1 h1 v2 h-1 z M11,2 h1 v3 h-1 z M9,5 h2 v1 h-2 z M1,9 h3 v1 h-3 z M5,9 h2 v2 h-2 z M9,8 h3 v3 h-3 z M13,9 h2 v1 h-2 z M16,9 h4 v1 h-4 z M9,13 h2 v2 h-2 z M12,12 h2 v2 h-2 z M15,12 h3 v2 h-3 z M19,12 h1 v3 h-1 z M9,17 h3 v3 h-3 z M13,16 h2 v4 h-2 z M16,16 h4 v2 h-4 z M16,19 h2 v1 h-2 z" fill="#121633"/>
                </svg>
            </div>

            <div class="bank-box" style="max-width:380px;margin:0 auto 22px;text-align:left">
                <div style="font-size:13px;color:var(--muted);margin-bottom:4px">Nomor Virtual Account (BCA / Mandiri / BRI)</div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px">
                    <b style="font-size:20px;letter-spacing:1px">{{ $vaNumber }}</b>
                    <span class="pill pill-lunas">Otomatis</span>
                </div>
            </div>

            <div class="alert alert-info" style="text-align:left;max-width:460px;margin:0 auto 22px">
                <b>Mode Uji Lokal:</b> Klik tombol di bawah untuk mensimulasikan pembayaran berhasil dari Payment Gateway. Status pesanan akan langsung berubah menjadi <b>Lunas</b> tanpa verifikasi manual admin.
            </div>

            <form method="POST" action="{{ route('payment.auto.confirm', $order->invoice) }}" style="max-width:380px;margin:0 auto">
                @csrf
                <button type="submit" class="btn btn-red btn-block">Simulasi Bayar Berhasil (Otomatis Lunas)</button>
            </form>
            <a href="{{ route('orders.show', $order->invoice) }}" class="btn btn-outline" style="margin-top:10px">Lihat Invoice</a>
        </div>

        <aside class="card">
            <h2 class="block-title" style="margin-top:0">Ringkasan {{ $order->invoice }}</h2>
            <div class="table-scroll">
                <table class="table">
                    <tbody>
                        @foreach ($order->items as $it)
                            <tr>
                                <td>{{ $it->product_name }} <small style="color:var(--muted)">&times; {{ $it->qty }}</small></td>
                                <td style="text-align:right">@rupiah($it->subtotal)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:14px">
                <div class="summary-row"><span>Subtotal</span><span>@rupiah($order->subtotal)</span></div>
                @if ($order->discount)
                    <div class="summary-row"><span>Diskon ({{ $order->coupon_code }})</span><span>- @rupiah($order->discount)</span></div>
                @endif
                <div class="summary-row"><span>Ongkir ({{ $order->courierLabel() }})</span><span>@rupiah($order->shipping_cost)</span></div>
                <div class="summary-row total"><span>Grand Total</span><span class="price">@rupiah($order->grand_total)</span></div>
            </div>
        </aside>
    </div>
@endsection
