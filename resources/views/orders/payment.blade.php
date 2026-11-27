@extends('layouts.app')
@section('title', 'Konfirmasi Pembayaran')

@section('content')
<div class="pay-page">
    <div class="page-head">
        <div class="crumb"><a href="{{ route('home') }}">Beranda</a> / Konfirmasi Pembayaran</div>
        <h1>Konfirmasi Pembayaran</h1>
        <div class="ghost" aria-hidden="true">BAYAR</div>
    </div>

    <div class="layout-2">
        <div class="card">
            <p class="pay-intro">Sudah transfer? Isi form ini supaya admin bisa mencocokkan pembayaranmu.</p>

            @if ($orders->isEmpty())
                <div class="empty">Tidak ada invoice yang menunggu pembayaran transfer.<br><br>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline">Lihat Pesanan Saya</a></div>
            @else
                <form method="POST" action="{{ route('payment.save') }}">
                    @csrf
                    <div class="field">
                        <label for="f-invoice">No. Invoice</label>
                        <select id="f-invoice" name="invoice" required>
                            @foreach ($orders as $o)
                                <option value="{{ $o->invoice }}" @selected(old('invoice', $selected) === $o->invoice)>
                                    {{ $o->invoice }} (Rp {{ number_format($o->grand_total, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('invoice')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid-2">
                        <div class="field"><label for="f-email">Email *</label><input id="f-email" type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
                        <div class="field"><label for="f-nama">Nama Customer</label><input id="f-nama" type="text" name="nama" value="{{ old('nama', $user->name) }}" required></div>
                    </div>
                    <div class="field">
                        <label for="jml_transfer">Jumlah Transfer (Rp)</label>
                        <input type="number" name="jml_transfer" id="jml_transfer" value="{{ old('jml_transfer') }}" min="1" required>
                        @error('jml_transfer')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <p class="field-label" id="rekTujuanLabel">Rekening tujuan transfer @include('partials.sample-tag')</p>
                        <div class="bank-box" aria-labelledby="rekTujuanLabel">
                            @if (config('store.bank_account'))
                                <div class="bank-box-info">
                                    <span class="bank-box-name">{{ config('store.bank_name') }} a.n. {{ config('store.bank_holder') }}</span>
                                    <b class="bank-box-number" data-copy-source>{{ config('store.bank_account') }}</b>
                                </div>
                                <button type="button" class="btn btn-outline btn-sm" data-copy="{{ config('store.bank_account') }}">
                                    @include('partials.icon', ['n' => 'copy']) <span data-copy-label>Salin nomor</span>
                                </button>
                            @else
                                <b class="bank-box-number">{{ config('store.bank') }}</b>
                            @endif
                        </div>
                        <p class="sr-only" aria-live="polite" data-copy-status></p>
                    </div>
                    <div class="grid-2">
                        <div class="field" data-bank>
                            <label for="dari_bank">Transfer dari Bank</label>
                            <select name="dari_bank" id="dari_bank" required>
                                <option value="">Pilih bank / e-wallet</option>
                                @foreach (config('store.banks') as $group => $banks)
                                    <optgroup label="{{ $group }}">
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank }}" @selected(old('dari_bank') === $bank)>{{ $bank }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                <option value="Lainnya" @selected(old('dari_bank') === 'Lainnya')>Lainnya</option>
                            </select>
                            @error('dari_bank')<div class="error-text">{{ $message }}</div>@enderror
                            <input type="text" name="dari_bank_lain" id="dari_bank_lain" value="{{ old('dari_bank_lain') }}"
                                   placeholder="Tulis nama bank" aria-label="Nama bank lainnya" class="bank-other" @if (old('dari_bank') !== 'Lainnya') hidden @endif>
                            @error('dari_bank_lain')<div class="error-text">{{ $message }}</div>@enderror
                        </div>
                        <div class="field"><label for="f-rekening_pengirim">Nama Pemilik Rekening Pengirim</label><input id="f-rekening_pengirim" type="text" name="rekening_pengirim" value="{{ old('rekening_pengirim') }}" required></div>
                    </div>
                    <p class="pay-clock">
                        Waktu transfer dicatat otomatis saat kamu klik Kirim:
                        <b data-clock>{{ now()->translatedFormat('l, d F Y, H:i:s') }}</b> WIB
                    </p>
                    <button class="btn btn-red">Kirim Konfirmasi</button>
                </form>
            @endif
        </div>
        <aside class="card">
            <h2 class="card-title">Kantor {{ config('app.name') }} @include('partials.sample-tag')</h2>
            <p class="note">{{ config('store.address') }}</p>
            <p class="note">{{ config('store.email') }}</p>
            <h3 class="card-subtitle">Jam kerja</h3>
            <p class="note">{{ config('store.hours') }}<br>Sabtu - Minggu &amp; tanggal merah: Libur</p>
        </aside>
    </div>
</div>
@endsection
