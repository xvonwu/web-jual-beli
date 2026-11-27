@extends('layouts.app')
@section('title', $product->name)

@section('content')
    <nav class="detail-crumb" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Beranda</a> /
        <a href="{{ $product->category->url() }}">{{ $product->category->platform }} {{ $product->category->name }}</a> /
        <span aria-current="page">{{ $product->name }}</span>
    </nav>

    <section class="detail">
        <div class="thumb"><img src="{{ $product->imageUrl() }}" {!! $product->imageAttrs() !!} fetchpriority="high" alt="{{ $product->name }}"></div>
        <div>
            @php $st = $product->is_preorder ? 'st-pre' : ($product->stock > 0 ? 'st-ready' : 'st-sold'); @endphp
            <span class="status {{ $st }}">{{ $product->statusLabel() }}</span>
            <h1>{{ $product->name }}</h1>
            <div>
                <span class="price">@rupiah($product->price)</span>
                @if ($product->old_price)<span class="old-price">@rupiah($product->old_price)</span>@endif
            </div>

            <dl class="meta">
                <dt>Platform</dt><dd>{{ $product->category->platform }}</dd>
                <dt>Kategori</dt><dd>{{ $product->category->name }}</dd>
                @if ($product->region)<dt>Region</dt><dd>{{ $product->region }}</dd>@endif
                <dt>Stok</dt><dd>{{ $product->is_preorder ? 'Preorder' : $product->stock.' pcs' }}</dd>
                <dt>Berat</dt><dd>{{ rtrim(rtrim(number_format($product->weight, 2, ',', ''), '0'), ',') }} kg</dd>
                @if ($product->release_date)<dt>Tanggal Rilis</dt><dd>{{ $product->release_date->translatedFormat('d F Y') }}</dd>@endif
            </dl>

            @if ($product->isSoldOut())
                <button class="btn btn-dark" disabled>Sold Out</button>
                <a class="btn btn-outline" target="_blank" rel="noopener"
                   href="https://api.whatsapp.com/send?phone={{ config('store.whatsapp') }}&text={{ rawurlencode('Halo, apakah '.$product->name.' akan restock?') }}">Tanya via WhatsApp</a>
            @else
                @auth
                    <form method="POST" action="{{ route('cart.add', $product->id) }}">
                        @csrf
                        <div class="qty-row">
                            <label style="margin:0">Jumlah</label>
                            <input type="number" name="qty" value="1" min="1" @unless($product->is_preorder) max="{{ $product->stock }}" @endunless>
                        </div>
                        <button class="btn btn-red">Beli Sekarang</button>
                    </form>
                @else
                    <div class="alert alert-info">Kamu perlu <b>login</b> dulu sebelum membeli. Setelah login kamu akan kembali ke proses beli.</div>
                    <a href="{{ route('cart.add', $product->id) }}" class="btn btn-red">Beli Sekarang</a>
                @endauth
            @endif

            @if (auth()->user()?->is_admin)
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:18px;padding-top:18px;border-top:1px solid var(--line)">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline btn-sm">Edit Produk (Admin)</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline"
                          data-confirm-delete="{{ $product->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline btn-sm" style="color:var(--signal-text);border-color:var(--signal-text)">
                            Hapus Produk
                        </button>
                    </form>
                </div>
            @endif

            <h2 class="block-title" style="margin:34px 0 8px">Deskripsi</h2>
            <p style="color:#555;margin:0">{!! nl2br(e($product->description)) !!}</p>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="section">
            <div class="section-head"><h2>Produk <span>Terkait</span></h2></div>
            <div class="products">
                @foreach ($related as $p) @include('partials.product-card') @endforeach
            </div>
        </section>
    @endif
@endsection
