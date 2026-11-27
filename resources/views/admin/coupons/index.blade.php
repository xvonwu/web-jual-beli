@extends('layouts.app')
@section('title', 'Admin: Kelola Kupon')

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('admin.orders.index') }}">Panel Admin</a> / Kupon Promo</div>
        <h1>Kelola Kupon</h1>
        <div class="ghost" aria-hidden="true">KUPON</div>
    </div>

    @include('admin._nav')

    <div class="layout-2">
        <div class="card">
            <h2 class="card-title">Daftar Kupon</h2>
            @if ($coupons->isEmpty())
                <div class="empty">Belum ada kupon promo.</div>
            @else
                <div class="table-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Diskon</th>
                                <th class="hide-sm">Min. Belanja</th>
                                <th>Pemakaian</th>
                                <th>Status</th>
                                <th style="text-align:right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $c)
                                <tr>
                                    <td>
                                        <b>{{ $c->code }}</b>
                                        @if ($c->expires_at)
                                            <br><small style="color:var(--muted)">Exp: {{ $c->expires_at->format('d M Y') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $c->type === 'percent' ? $c->value.'%' : 'Rp '.number_format($c->value, 0, ',', '.') }}
                                    </td>
                                    <td class="hide-sm">@rupiah($c->min_total)</td>
                                    <td>
                                        {{ $c->used_count }}{{ $c->max_uses ? ' / '.$c->max_uses : 'x' }}
                                    </td>
                                    <td>
                                        <span class="pill {{ $c->is_active ? 'pill-lunas' : 'pill-batal' }}">
                                            {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td style="text-align:right;white-space:nowrap">
                                        <form method="POST" action="{{ route('admin.coupons.toggle', $c) }}" style="display:inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                {{ $c->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $c) }}" style="display:inline"
                                              onsubmit="return confirm('Hapus kupon {{ $c->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm" style="color:var(--signal-text);border-color:var(--signal-text)">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @include('partials.pagination', ['paginator' => $coupons, 'pageUrl' => fn ($p) => $coupons->url($p)])
        </div>

        <aside class="card">
            <h2 class="card-title">Buat Kupon Baru</h2>
            <form method="POST" action="{{ route('admin.coupons.store') }}">
                @csrf
                <div class="field">
                    <label for="code" class="field-label">Kode Kupon *</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="Contoh: GAJIAN20" required style="text-transform:uppercase">
                    @error('code')<p class="field-status">{{ $message }}</p>@enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div class="field">
                        <label for="type" class="field-label">Tipe *</label>
                        <select id="type" name="type">
                            <option value="fixed" @selected(old('type') === 'fixed')>Nominal (Rp)</option>
                            <option value="percent" @selected(old('type') === 'percent')>Persen (%)</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="value" class="field-label">Nilai *</label>
                        <input type="number" id="value" name="value" min="1" value="{{ old('value') }}" placeholder="50000" required>
                        @error('value')<p class="field-status">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div class="field">
                        <label for="min_total" class="field-label">Min. Belanja (Rp)</label>
                        <input type="number" id="min_total" name="min_total" min="0" value="{{ old('min_total', 0) }}">
                    </div>
                    <div class="field">
                        <label for="max_uses" class="field-label">Batas Kuota (opsional)</label>
                        <input type="number" id="max_uses" name="max_uses" min="1" value="{{ old('max_uses') }}" placeholder="Tanpa batas">
                    </div>
                </div>
                <div class="field">
                    <label for="expires_at" class="field-label">Berlaku Sampai (opsional)</label>
                    <input type="date" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                </div>
                <button type="submit" class="btn btn-red btn-block">Simpan Kupon</button>
            </form>
        </aside>
    </div>
@endsection
