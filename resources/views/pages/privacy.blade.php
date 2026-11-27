@extends('layouts.app')
@section('title', 'Kebijakan Privasi')

@section('content')
    <div class="page-head"><div class="crumb"><a href="{{ route('home') }}">Beranda</a> / Informasi</div><h1>Kebijakan Privasi</h1><div class="ghost" aria-hidden="true">PRIVASI</div></div>
    <div class="card" style="color:#444">
        <p>Kebijakan Privasi ini mengatur perlindungan dan penggunaan informasi yang Pengguna berikan saat menggunakan layanan {{ config('app.name') }}.</p>
        <h2 class="block-title">Informasi yang kami kumpulkan</h2>
        <p>Nama, email, nomor HP, alamat pengiriman, dan data konfirmasi pembayaran yang Pengguna isi saat mendaftar dan bertransaksi.</p>
        <h2 class="block-title">Penggunaan informasi</h2>
        <p>Data digunakan untuk memproses pesanan, pengiriman, verifikasi pembayaran, dan (bila Pengguna berlangganan) newsletter promo.</p>
        <h2 class="block-title">Keamanan</h2>
        <p>Password disimpan dalam bentuk terenkripsi (hash). Kami tidak menjual data Pengguna kepada pihak lain.</p>
    </div>
@endsection
