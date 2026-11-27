@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('home') }}">Beranda</a> / Akun</div>
        <h1>Pesanan Saya</h1>
        <div class="ghost" aria-hidden="true">PESANAN</div>
    </div>

    @if ($orders->isEmpty())
        <div class="empty">
            <p>Belum ada pesanan. Pesanan yang kamu checkout dari keranjang akan muncul di sini.</p>
            <a href="{{ route('home') }}" class="btn btn-red">Mulai belanja</a>
        </div>
    @else
        <div class="card">
            <table class="table table-stack table-orders">
                <thead><tr><th>Invoice</th><th class="hide-sm">Tanggal</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($orders as $o)
                    <tr>
                        <td class="o-inv"><b>{{ $o->invoice }}</b></td>
                        <td class="hide-sm">{{ $o->created_at->translatedFormat('d M Y, H:i') }} WIB</td>
                        <td class="o-total" data-label="Total">@rupiah($o->grand_total)</td>
                        <td class="o-status"><span class="pill pill-{{ $o->status }}">{{ $o->statusLabel() }}</span></td>
                        <td class="o-act" style="text-align:right">
                            <a href="{{ route('orders.show', $o->invoice) }}" class="btn btn-outline btn-sm" aria-label="Detail {{ $o->invoice }}">Detail</a>
                            @if ($o->status === 'menunggu_pembayaran' && $o->payment_method_id === \App\Models\Order::PAYMENT_TRANSFER)
                                <a href="{{ route('payment.form', ['invoice' => $o->invoice]) }}" class="btn btn-red btn-sm">Konfirmasi</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @include('partials.pagination', ['paginator' => $orders, 'pageUrl' => fn ($p) => $orders->url($p)])
    @endif
@endsection
