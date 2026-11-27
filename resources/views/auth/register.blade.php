@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
    <div class="auth-wrap">
        <div class="auth-side">
            
            <h2>Buat akun baru</h2>
            <p style="opacity:.85;margin:0">Data kamu otomatis terisi saat checkout, dan riwayat pesanan tersimpan rapi.</p>
            <img src="{{ asset('images/products/corsair-hs80-rgb-wireless-headset.png') }}" {!! \App\Support\Img::attrs('images/products/corsair-hs80-rgb-wireless-headset.png') !!} alt="">
        </div>
        <div class="auth-form">
            <h1>Daftar</h1>
            <p class="sub">Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--cobalt);font-weight:700">Masuk</a></p>
            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <div class="field">
                    <label for="f-name">Nama Lengkap</label>
                    <input id="f-name" type="text" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label for="f-email">Email</label>
                        <input id="f-email" type="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="f-phone">No. HP</label>
                        <input id="f-phone" type="text" name="phone" value="{{ old('phone') }}" required>
                        @error('phone')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label for="f-password">Password</label>
                        <input id="f-password" type="password" name="password" required>
                        @error('password')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="f-password_confirmation">Ulangi Password</label>
                        <input id="f-password_confirmation" type="password" name="password_confirmation" required>
                    </div>
                </div>
                <button class="btn btn-red btn-block">Daftar</button>
            </form>
        </div>
    </div>
@endsection
