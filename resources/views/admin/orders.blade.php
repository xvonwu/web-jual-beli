@extends('layouts.app')
@section('title', 'Admin: Pesanan')

@section('content')
    <div class="page-head">
        <div class="crumb">Panel Admin</div>
        <h1>Verifikasi Pesanan</h1>
        <div class="ghost" aria-hidden="true">ADMIN</div>
    </div>

    @include('admin._nav')

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-bottom:20px">
        <div class="card" style="padding:20px">
            <div style="font-size:13px;color:var(--muted);margin-bottom:4px">Total Omzet Lunas</div>
            <div style="font-size:24px;font-weight:800;color:var(--ink)">@rupiah($revenue)</div>
        </div>
        <div class="card" style="padding:20px">
            <div style="font-size:13px;color:var(--muted);margin-bottom:4px">Perlu Verifikasi Manual</div>
            <div style="font-size:24px;font-weight:800;color:var(--cobalt)">{{ $counts['menunggu_verifikasi'] ?? 0 }} pesanan</div>
        </div>
        <div class="card" style="padding:20px">
            <div style="font-size:13px;color:var(--muted);margin-bottom:6px">Produk Stok Menipis (&le; 2)</div>
            @if ($lowStockProducts->isEmpty())
                <div style="font-size:15px;font-weight:600;color:#136b38">Semua stok aman</div>
            @else
                <div style="display:flex;flex-direction:column;gap:4px;font-size:14px">
                    @foreach ($lowStockProducts as $lp)
                        <a href="{{ route('admin.products.edit', $lp) }}" style="color:var(--signal-text);font-weight:600">
                            {{ $lp->name }} ({{ $lp->stock }} pcs)
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
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
        Untuk menambah atau mengubah produk, gunakan tab <b>Kelola Produk</b> atau <b>+ Upload Produk Baru</b> di atas.
    </p>
@endsection
