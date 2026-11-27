@extends('layouts.app')
@section('title', 'Kontak')

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('home') }}">Beranda</a> / Kontak</div>
        <h1>Hubungi Kami</h1>
        <div class="ghost" aria-hidden="true">KONTAK</div>
    </div>
    <div class="layout-2">
        <div class="card">
            <h2 class="card-title">Kantor {{ config('app.name') }} @include('partials.sample-tag')</h2>
            <dl class="meta">
                <dt>Alamat</dt><dd>{{ config('store.address') }}</dd>
                <dt>WhatsApp</dt><dd><a style="color:var(--red)" target="_blank" rel="noopener" href="https://api.whatsapp.com/send?phone={{ config('store.whatsapp') }}">+{{ config('store.whatsapp') }}</a></dd>
                <dt>Email</dt><dd><a href="mailto:{{ config('store.email') }}">{{ config('store.email') }}</a></dd>
                <dt>Jam Kerja</dt><dd>{{ config('store.hours') }}<br>Sabtu - Minggu &amp; tanggal merah: Libur</dd>
            </dl>
            <p class="note">Untuk pertanyaan stok, preorder, atau bundling, silakan hubungi kami via WhatsApp.</p>
        </div>
        <aside class="card" style="background:var(--dark);color:#fff;border:0">
            <h2 class="card-title">Ambil langsung?</h2>
            <p style="opacity:.8">Harap konfirmasi dulu sebelum datang agar barang disiapkan. Bawa invoice dan bukti transfer (bila sudah transfer).</p>
        </aside>
    </div>
@endsection
