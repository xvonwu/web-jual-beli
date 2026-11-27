@extends('layouts.app')

@section('content')
    @php
        $u = fn ($id) => isset($cat[$id]) ? $cat[$id]->url() : route('search');
        $shelfInfo = [
            'PS5' => ['img' => 'images/products/ps5-real.png', 'class' => 'shelf-ps5', 'title' => 'PlayStation 5'],
            'PS4' => ['img' => 'images/products/ps4-slim.png', 'class' => 'shelf-ps4', 'title' => 'PlayStation 4'],
            'XBOX' => ['img' => 'images/products/xbox-series-x.png', 'class' => 'shelf-xbox', 'title' => 'Xbox Series'],
            'Others' => ['img' => 'images/products/meta-quest-3-512gb-vr-headset.png', 'class' => 'shelf-other', 'title' => 'VR & gear'],
        ];
    @endphp

    {{-- ================= HERO ================= --}}
    <section class="hero">
        <div class="hero-copy">
            <h1 class="hero-title"><span>PS5 ready.</span> <span>Main malam ini.</span></h1>
            <p class="hero-lead">Konsol dan game original, masih segel. Ambil langsung di toko, atau kirim lewat GOJEK, Grab, JNE, dan J&amp;T.</p>
            <div class="hero-actions">
                <a href="{{ $u(161) }}" class="btn btn-yellow">Lihat konsol PS5</a>
                <a href="{{ $u(162) }}" class="btn btn-ghost">Game PS5 terbaru</a>
            </div>
        </div>
        <div class="hero-visual">
            <img src="{{ asset('images/products/ps5-real.png') }}" {!! \App\Support\Img::attrs('images/products/ps5-real.png') !!} fetchpriority="high" alt="PlayStation 5 dengan controller DualSense">
            @if ($ps5From)
                <div class="hero-sticker"><small>Mulai</small>@rupiah($ps5From)</div>
            @endif
        </div>
    </section>

    {{-- ================= RAK PLATFORM ================= --}}
    <section class="shelves">
        @foreach ($shelfInfo as $platform => $info)
            @continue(! $shelves->has($platform))
            @php($cats = $shelves[$platform])
            <div class="shelf {{ $info['class'] }}">
                <a href="{{ $cats->first()->url() }}" class="shelf-img"><img src="{{ asset($info['img']) }}" {!! \App\Support\Img::attrs($info['img']) !!} alt="{{ $info['title'] }}"></a>
                <div class="shelf-body">
                    <h2><a href="{{ $cats->first()->url() }}">{{ $info['title'] }}</a></h2>
                    <p class="shelf-count">{{ $cats->sum('products_count') }} produk</p>
                    <ul>
                        @foreach ($cats as $c)
                            <li><a href="{{ $c->url() }}">{{ $c->name }} <span>{{ $c->products_count }}</span></a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ================= INFO TOKO =================
         Pertanyaan yang paling sering masuk: "bisa ambil di toko / kirim ke kota saya?".
         Jadi pengiriman dibuat paling menonjol, info lain cukup daftar pendek. --}}
    <section class="features" aria-label="Info belanja">
        <div class="feature-lead">
            @include('partials.icon', ['n' => 'truck'])
            <div>
                <h2>Ambil di toko, atau kirim ke seluruh Indonesia</h2>
                <p>Ongkir dihitung otomatis dari provinsi tujuan di halaman keranjang.</p>
                <ul class="courier-list">
                    @foreach (\App\Models\Order::COURIERS as $label)<li>{{ $label }}</li>@endforeach
                </ul>
            </div>
        </div>
        <ul class="feature-list">
            <li><b>Original, masih segel</b><span>Garansi sesuai ketentuan distributor</span></li>
            <li><b>Transfer bank atau cash</b><span>Cash khusus ambil di toko</span></li>
            <li><b>Tanya stok via WhatsApp</b><span>{{ config('store.hours') }}</span></li>
        </ul>
    </section>

    {{-- ================= PREORDER =================
         Beda bentuk dari grid produk: yang penting di preorder adalah tanggal rilis,
         jadi tampil sebagai daftar baris dengan tanggal di depan. --}}
    @if ($preorders->isNotEmpty())
        <section class="preorder-band">
            <div class="preorder-head">
                <h2>Preorder</h2>
                <p>Amankan slot sebelum rilis. Barang dikirim saat tanggal rilis resmi.</p>
                <a href="{{ route('preorder') }}">Semua preorder</a>
            </div>
            <ol class="pre-list">
                @foreach ($preorders->sortBy('release_date') as $p)
                    <li class="pre-row">
                        <span class="pre-date">
                            @if ($p->release_date)
                                <small>Rilis</small>{{ $p->release_date->translatedFormat('d M Y') }}
                            @else
                                <small>Rilis</small>Segera
                            @endif
                        </span>
                        <a href="{{ $p->url() }}" class="pre-thumb"><img src="{{ $p->imageUrl() }}" {!! $p->imageAttrs() !!} alt="" loading="lazy"></a>
                        <span class="pre-name"><a href="{{ $p->url() }}">{{ $p->name }}</a><small>{{ $p->category?->name }}</small></span>
                        <span class="pre-price">@rupiah($p->price)</span>
                        <a href="{{ route('cart.add', $p->id) }}" class="btn btn-yellow btn-sm" aria-label="Preorder {{ $p->name }}">Preorder</a>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    {{-- ================= NEW RELEASE ================= --}}
    <section class="section">
        <div class="section-head">
            <h2>Baru rilis</h2>
            <p>Game dan konsol yang baru masuk rak.</p>
            <a href="{{ route('search') }}">Semua produk</a>
        </div>
        <div class="products">
            @forelse ($newReleases as $p) @include('partials.product-card') @empty
                <div class="empty" style="grid-column:1/-1">
                    <p>Belum ada produk baru yang masuk rak.</p>
                    <a href="{{ route('search') }}" class="btn btn-red">Lihat semua produk</a>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ================= LATEST UPDATES ================= --}}
    <section class="section">
        <div class="section-head">
            <h2>Baru diperbarui</h2>
            <p>Harga dan stok yang terakhir berubah.</p>
            <a href="{{ route('promo') }}">Lihat promo</a>
        </div>
        <div class="products">
            @foreach ($latest as $p) @include('partials.product-card') @endforeach
        </div>
    </section>
@endsection
