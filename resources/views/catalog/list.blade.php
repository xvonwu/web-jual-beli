@extends('layouts.app')
@section('title', $title)

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('home') }}">Beranda</a> @foreach ($breadcrumb as $b) / {{ $b }} @endforeach</div>
        <h1>{{ $title }}</h1>
        <div class="ghost" aria-hidden="true">{{ strtoupper($breadcrumb[0] ?? 'KATALOG') }}</div>
    </div>

    @isset($q)
        <form action="{{ route('search') }}" method="GET" role="search" class="search-page">
            <label for="pageQ">Cari produk</label>
            <div class="search-row">
                <input type="text" id="pageQ" name="q" value="{{ $q }}" placeholder="Nama game, konsol, atau aksesoris">
                <button class="btn btn-red">Cari</button>
            </div>
        </form>
    @endisset

    <p class="result-count">Menampilkan {{ $products->count() }} dari {{ $products->total() }} produk</p>

    @if ($products->isEmpty())
        <div class="empty">
            @if (! empty($q))
                <p>Tidak ada produk yang cocok dengan "{{ $q }}". Coba kata yang lebih pendek, misalnya "PS5" atau "DualSense".</p>
            @else
                <p>Belum ada produk di halaman ini.</p>
            @endif
            <a href="{{ route('search') }}" class="btn btn-red">Lihat semua produk</a>
        </div>
    @else
        <div class="products">
            @foreach ($products as $p) @include('partials.product-card') @endforeach
        </div>
        @include('partials.pagination', ['paginator' => $products, 'pageUrl' => $pageUrl])
    @endif
@endsection
