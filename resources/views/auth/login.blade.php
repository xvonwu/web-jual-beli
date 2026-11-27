@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
    <div class="auth-wrap">
        <div class="auth-side">
            
            <h2>Lanjutkan belanja kamu</h2>
            <p style="opacity:.85;margin:0">Keranjang, pembayaran, dan riwayat pesanan tersimpan di akun kamu.</p>
            <img src="{{ asset('images/products/dualsense-wireless-controller-white.png') }}" {!! \App\Support\Img::attrs('images/products/dualsense-wireless-controller-white.png') !!} alt="">
        </div>
        <div class="auth-form">
            <h1>Masuk</h1>
            <p class="sub">Belum punya akun? <a href="{{ route('register') }}" style="color:var(--cobalt);font-weight:700">Daftar sekarang</a></p>

            @if (session('url.intended'))
                <div class="alert alert-info">Masuk dulu untuk melanjutkan. Setelah masuk, kamu kembali ke halaman tadi.</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="field">
                    <label for="f-email">Email</label>
                    <input id="f-email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="f-password">Password</label>
                    <input id="f-password" type="password" name="password" required>
                    @error('password')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <label style="display:flex;gap:8px;align-items:center;font-weight:400">
                    <input type="checkbox" name="remember" value="1"> Ingat saya
                </label>
                <button class="btn btn-red btn-block" style="margin-top:18px">Masuk</button>
            </form>
            <p style="font-size:12px;color:var(--muted);margin-top:20px">Akun demo: <b>user@pakhar.test</b> / <b>password</b></p>
        </div>
    </div>
@endsection
