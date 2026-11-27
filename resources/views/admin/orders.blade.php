@extends('layouts.app')
@section('title', 'Admin: Pesanan')

@section('content')
    <div class="page-head">
        <div class="crumb">Panel Admin</div>
        <h1>Verifikasi Pesanan</h1>
        <div class="ghost" aria-hidden="true">ADMIN</div>
    </div>

    <div class="stat-row">
        @foreach (\App\Models\Order::STATUSES as $key => $label)
            <a href="{{ route('admin.orders.index', ['status' => $key]) }}" @class(['stat', 'on' => $status === $key])>
                <b>{{ $counts[$key] ?? 0 }}</b>{{ $label }}
            </a>
        @endforeach
    </div>
    @if ($status)
        <p><a href="{{ route('admin.orders.index') }}" style="color:var(--red)">&larr; Tampilkan semua</a></p>
    @endif

    <div class="card">
        @if ($orders->isEmpty())
            <div class="empty">Belum ada pesanan.</div>
        @else
            <div class="table-scroll">
            <table class="table">
                <thead><tr><th>Invoice</th><th>Pembeli</th><th class="hide-sm">Kurir</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($orders as $o)
                    <tr>
                        <td><b>{{ $o->invoice }}</b><br><small style="color:var(--muted)">{{ $o->created_at->translatedFormat('d M Y, H:i') }} WIB</small></td>
                        <td>{{ $o->customer_name }}<br><small style="color:var(--muted)">{{ $o->user?->email }}</small></td>
                        <td class="hide-sm">{{ $o->courierLabel() }}</td>
                        <td>@rupiah($o->grand_total)</td>
                        <td><span class="pill pill-{{ $o->status }}">{{ $o->statusLabel() }}</span></td>
                        <td><a href="{{ route('admin.orders.show', $o) }}" class="btn btn-outline btn-sm">Buka</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
    @include('partials.pagination', ['paginator' => $orders, 'pageUrl' => fn ($p) => $orders->url($p)])

    <p style="font-size:13px;color:var(--muted);margin-top:24px">
        Kelola produk, stok, kategori, kupon &amp; tarif ongkir langsung dari <b>phpMyAdmin</b> (database <code>{{ config('database.connections.mysql.database') }}</code>).
    </p>
@endsection
