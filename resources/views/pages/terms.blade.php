@extends('layouts.app')
@section('title', 'Syarat & Ketentuan')

@section('content')
    <div class="page-head"><div class="crumb"><a href="{{ route('home') }}">Beranda</a> / Informasi</div><h1>Syarat &amp; Ketentuan</h1><div class="ghost" aria-hidden="true">SYARAT</div></div>
    <div class="card" style="color:#444">
        <p>Dengan mengakses dan menggunakan situs {{ config('app.name') }}, Pengguna dianggap telah memahami dan menyetujui Syarat &amp; Ketentuan berikut.</p>
        <ol>
            <li>Pengguna wajib login/mendaftar sebelum membeli dan melakukan pembayaran.</li>
            <li>Pesanan diproses setelah pembayaran diverifikasi admin pada hari dan jam kerja ({{ config('store.hours') }}).</li>
            <li>Pembayaran melalui transfer BCA, atau cash khusus untuk pengambilan langsung di kantor.</li>
            <li>Ongkir GOJEK/Grab dibayar langsung ke driver. Ongkir JNE/J&amp;T dihitung berdasarkan kota tujuan dan berat.</li>
            <li>Produk preorder dikirim setelah tanggal rilis resmi.</li>
            <li>Semua produk original dan bergaransi sesuai ketentuan masing-masing distributor.</li>
        </ol>
    </div>
@endsection
