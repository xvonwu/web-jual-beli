@extends('layouts.app')
@section('title', 'Admin: Kelola Produk')

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('admin.orders.index') }}">Panel Admin</a> / Kelola Produk</div>
        <h1>Daftar Produk</h1>
        <div class="ghost" aria-hidden="true">PRODUK</div>
    </div>

    @include('admin._nav')

    <div class="card" style="margin-bottom:20px;padding:20px">
        <form method="GET" action="{{ route('admin.products.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama produk..." style="flex:1;min-width:200px">
            <select name="category" style="min-width:200px">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $platform => $cats)
                    <optgroup label="{{ $platform }}">
                        @foreach ($cats as $cat)
                            <option value="{{ $cat->id }}" @selected((string) $categoryId === (string) $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            @if ($q !== '' || $categoryId)
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if ($products->isEmpty())
            <div class="empty">
                Tidak ada produk yang ditemukan.
                <div style="margin-top:14px">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-red btn-sm">+ Upload Produk Baru</a>
                </div>
            </div>
        @else
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Produk</th>
                            <th class="hide-sm">Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $p)
                            <tr>
                                <td style="width:84px">
                                    <div class="mini">
                                        <img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" loading="lazy">
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ $p->url() }}" target="_blank" style="font-weight:700;color:var(--ink)">{{ $p->name }}</a>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px">
                                        @if ($p->is_preorder)<span class="pill pill-menunggu_verifikasi">Preorder</span>@endif
                                        @if ($p->is_promo)<span class="pill pill-batal">Promo</span>@endif
                                        @if ($p->is_new_release)<span class="pill pill-lunas">Rilis Baru</span>@endif
                                        @if ($p->region)<span class="pill">{{ $p->region }}</span>@endif
                                    </div>
                                </td>
                                <td class="hide-sm">
                                    {{ $p->category?->platform }} &middot; {{ $p->category?->name }}
                                </td>
                                <td>
                                    <b>@rupiah($p->price)</b>
                                    @if ($p->old_price)
                                        <br><small style="color:var(--muted);text-decoration:line-through">@rupiah($p->old_price)</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($p->isSoldOut())
                                        <span class="pill pill-batal">Sold Out</span>
                                    @else
                                        <span class="pill pill-lunas">{{ $p->stock }} pcs</span>
                                    @endif
                                </td>
                                <td style="text-align:right;white-space:nowrap">
                                    <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $p) }}" style="display:inline" data-confirm-delete="{{ $p->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color:var(--signal-text);border-color:var(--signal-text)">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials.pagination', ['paginator' => $products, 'pageUrl' => fn ($page) => $products->url($page)])
@endsection
