@extends('layouts.app')
@section('title', 'Admin: Tarif Ongkir')

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('admin.orders.index') }}">Panel Admin</a> / Tarif Ongkir</div>
        <h1>Tarif Ongkir Per Provinsi</h1>
        <div class="ghost" aria-hidden="true">ONGKIR</div>
    </div>

    @include('admin._nav')

    <div class="card" style="margin-bottom:20px;padding:20px">
        <form method="GET" action="{{ route('admin.shipping.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama provinsi..." style="flex:1;min-width:200px">
            <select name="courier" style="min-width:160px">
                <option value="">Semua Kurir</option>
                <option value="jne" @selected($courier === 'jne')>JNE</option>
                <option value="jnt" @selected($courier === 'jnt')>J&amp;T</option>
            </select>
            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            @if ($q !== '' || $courier)
                <a href="{{ route('admin.shipping.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if ($rates->isEmpty())
            <div class="empty">Tarif ongkir tidak ditemukan.</div>
        @else
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kurir</th>
                            <th>Provinsi Tujuan</th>
                            <th>Tarif per kg (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rates as $r)
                            <tr>
                                <td style="width:110px">
                                    <span class="pill {{ $r->courier === 'jne' ? 'pill-menunggu_verifikasi' : 'pill-batal' }}">
                                        {{ $r->courier === 'jnt' ? 'J&T' : strtoupper($r->courier) }}
                                    </span>
                                </td>
                                <td><b>{{ $r->province }}</b></td>
                                <td style="width:280px">
                                    <form method="POST" action="{{ route('admin.shipping.update', $r) }}" style="display:flex;gap:8px;align-items:center">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="cost" value="{{ $r->cost }}" min="0" step="500" style="width:140px" required>
                                        <button type="submit" class="btn btn-outline btn-sm">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials.pagination', ['paginator' => $rates, 'pageUrl' => fn ($p) => $rates->url($p)])
@endsection
