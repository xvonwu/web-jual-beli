<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Game PS5, PS4, Xbox Original') | {{ config('app.name') }}</title>
    <meta name="description" content="Jual game, konsol, dan aksesoris PS5, PS4, Xbox original. Garansi sesuai ketentuan distributor.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:ital,wdth,wght@0,62..125,100..900;1,62..125,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
@php $brand = strtoupper(str_replace(' ', '', config('app.name'))); @endphp
<header class="header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="logo"><img src="{{ asset('images/logo.png') }}" {!! \App\Support\Img::attrs('images/logo.png') !!} alt="{{ config('app.name') }}, ke beranda"></a>

        <nav class="nav" id="mainNav">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Beranda</a>
            <div class="dd">
                <button type="button" class="dd-toggle" aria-expanded="false" aria-controls="megaMenu">Belanja <span aria-hidden="true">&#9662;</span></button>
                <div class="mega" id="megaMenu">
                    @foreach ($menu as $platform => $cats)
                        <div>
                            <p class="mega-title">{{ $platform }}</p>
                            @foreach ($cats as $c)
                                <a href="{{ $c->url() }}">{{ $c->name }}</a>
                            @endforeach
                            @if ($platform !== 'Others')
                                <a href="{{ route('preorder', strtolower($platform)) }}">Preorder {{ $platform }}</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('promo') }}" @class(['active' => request()->routeIs('promo')])>Promo</a>
            <a href="{{ route('payment.form') }}" @class(['active' => request()->routeIs('payment.*')])>Konfirmasi Bayar</a>
            <a href="{{ route('contact') }}" @class(['active' => request()->routeIs('contact')])>Kontak</a>
        </nav>

        <div class="header-actions">
            @auth
                <div class="user-menu">
                    <button type="button" class="icon-btn" data-toggle-user aria-expanded="false" aria-label="Akun saya" title="Akun saya">@include('partials.icon', ['n' => 'user'])</button>
                    <div class="menu">
                        <div style="padding:8px 12px;font-size:13px;color:var(--muted)">Halo, <b style="color:var(--text)">{{ auth()->user()->name }}</b></div>
                        <a href="{{ route('orders.index') }}">Pesanan Saya</a>
                        <a href="{{ route('payment.form') }}">Konfirmasi Pembayaran</a>
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.orders.index') }}">Panel Admin</a>
                            <a href="{{ route('admin.products.index') }}">Kelola Produk</a>
                            <a href="{{ route('admin.products.create') }}">+ Upload Produk</a>
                            <a href="{{ route('admin.coupons.index') }}">Kupon Promo</a>
                            <a href="{{ route('admin.shipping.index') }}">Tarif Ongkir</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="login-link">Masuk</a>
            @endauth
            <button type="button" class="icon-btn" data-toggle-search aria-expanded="false" aria-controls="searchBar" aria-label="Cari produk" title="Cari produk">@include('partials.icon', ['n' => 'search'])</button>
            <a href="{{ route('cart.index') }}" class="icon-btn" aria-label="Keranjang, {{ $cartCount }} produk" title="Keranjang">
                @include('partials.icon', ['n' => 'cart'])
                <span class="badge" aria-hidden="true">{{ $cartCount }}</span>
            </a>
            <button type="button" class="icon-btn burger" data-toggle-nav aria-expanded="false" aria-controls="mainNav" aria-label="Menu" title="Menu">@include('partials.icon', ['n' => 'menu'])</button>
        </div>
    </div>
    <div class="container search-bar" id="searchBar">
        <form action="{{ route('search') }}" method="GET" role="search">
            <label for="searchQ">Cari produk</label>
            <div class="search-row">
                <input type="text" id="searchQ" name="q" value="{{ request('q') }}" placeholder="Nama game, konsol, atau aksesoris">
                <button class="btn btn-red">Cari</button>
            </div>
        </form>
    </div>
</header>

<main class="container">
    @include('partials.flash')
    @yield('content')

    <section class="newsletter">
        <div>
            <h2>Kabar restock dan rilis game, langsung ke email kamu.</h2>
            <p style="margin:10px 0 0">Satu email tiap ada stok baru atau preorder dibuka. Bisa berhenti kapan saja.</p>
        </div>
        <form method="POST" action="{{ route('subscribe') }}">
            @csrf
            <label for="newsletterEmail">Alamat email</label>
            <div class="newsletter-row">
                <input type="email" id="newsletterEmail" name="newsletterEmail" placeholder="nama@email.com" required>
                <button class="btn btn-dark">Daftar</button>
            </div>
        </form>
        <div class="ghost-sm" aria-hidden="true">GAME</div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="{{ route('home') }}" class="logo logo-footer"><img src="{{ asset('images/logo-white.png') }}" {!! \App\Support\Img::attrs('images/logo-white.png') !!} alt="{{ config('app.name') }}, ke beranda" loading="lazy"></a>
                <p style="margin:0;max-width:280px">Konsol, game, dan aksesoris PS5, PS4, dan Xbox. Semua original, garansi sesuai ketentuan distributor.</p>
            </div>
            <div>
                <h2 class="footer-title">Belanja</h2>
                @foreach (['PS5', 'PS4', 'XBOX', 'Others'] as $pf)
                    @if ($menu->has($pf))<a href="{{ $menu[$pf]->first()->url() }}">{{ $pf }}</a>@endif
                @endforeach
                <a href="{{ route('promo') }}">Promo</a>
            </div>
            <div>
                <h2 class="footer-title">Informasi</h2>
                <a href="{{ route('search') }}">Cari Games</a>
                <a href="{{ route('payment.form') }}">Konfirmasi Pembayaran</a>
                <a href="{{ route('privacy') }}">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}">Syarat &amp; Ketentuan</a>
                <a href="{{ route('contact') }}">Hubungi Kami</a>
            </div>
            <div>
                <h2 class="footer-title">Kontak @include('partials.sample-tag')</h2>
                <p style="margin:0 0 6px">{{ config('store.address') }}</p>
                <a href="mailto:{{ config('store.email') }}">{{ config('store.email') }}</a>
                <p style="margin:6px 0 0">{{ config('store.hours') }}</p>
            </div>
        </div>
        <div class="copy"><span>&copy; {{ date('Y') }} {{ config('app.name') }}. Hak cipta dilindungi.</span><span>{{ config('store.hours') }}</span></div>
    </div>
</footer>

<a class="wa-float" target="_blank" rel="noopener" title="Chat WhatsApp" aria-label="Chat WhatsApp (buka di tab baru)"
   href="https://api.whatsapp.com/send?phone={{ config('store.whatsapp') }}&text={{ rawurlencode('Halo, saya mau beli game') }}">
    @include('partials.icon', ['n' => 'wa'])
</a>

@include('partials.popup')

<script src="{{ asset('js/vendor/gsap.min.js') }}"></script>
<script src="{{ asset('js/vendor/ScrollTrigger.min.js') }}"></script>
<script src="{{ asset('js/vendor/SplitText.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
