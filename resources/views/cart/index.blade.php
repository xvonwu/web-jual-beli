@extends('layouts.app')
@section('title', 'Keranjang Belanja')

@section('content')
<div class="cart-page">
    <div class="page-head">
        <div class="crumb"><a href="{{ route('home') }}">Beranda</a> / Keranjang</div>
        <h1>Keranjang Belanja</h1>
        <div class="ghost" aria-hidden="true">KERANJANG</div>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    @if ($items->isEmpty())
        <div class="empty">
            Keranjang belanja kamu masih kosong.<br><br>
            <a href="{{ route('home') }}" class="btn btn-red">Mulai Belanja</a>
        </div>
    @else
        <div class="layout-2">
            <div>
                {{-- ===== Daftar produk ===== --}}
                <div class="card" style="margin-bottom:24px">
                    <table class="table table-stack table-cart">
                        <thead>
                        <tr><th colspan="2">Nama Produk</th><th class="hide-sm">Harga Satuan</th><th>Jumlah</th><th>Sub-Total</th><th></th></tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="c-img" style="width:70px"><div class="mini"><img src="{{ $item->product->imageUrl() }}" {!! $item->product->imageAttrs() !!} alt=""></div></td>
                                <td class="c-name"><a href="{{ $item->product->url() }}">{{ $item->product->name }}</a></td>
                                <td class="hide-sm">@rupiah($item->product->price)</td>
                                <td class="c-qty" data-label="Jumlah">
                                    <form method="POST" action="{{ route('cart.update', $item) }}" class="qty-form">
                                        @csrf @method('PATCH')
                                        <input type="number" name="qty" value="{{ $item->qty }}" min="1" onchange="this.form.submit()" aria-label="Jumlah {{ $item->product->name }}">
                                    </form>
                                </td>
                                <td class="c-sub" data-label="Sub-total"><b>@rupiah($item->subtotal())</b></td>
                                <td class="c-del">
                                    <form method="POST" action="{{ route('cart.remove', $item) }}">
                                        @csrf @method('DELETE')
                                        <button class="icon-del" title="Hapus dari keranjang" aria-label="Hapus {{ $item->product->name }} dari keranjang">@include('partials.icon', ['n' => 'trash'])</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <form method="POST" action="{{ route('cart.clear') }}" style="margin-top:14px">
                        @csrf
                        <button class="link-del">@include('partials.icon', ['n' => 'trash']) Kosongkan keranjang</button>
                    </form>
                </div>

                {{-- ===== STEP 1: Cara pengiriman -> POST /hitung_ongkir ===== --}}
                @php
                    $s = $shipping['data'] ?? [];
                    $type = old('jenisongkir', $s['jenisongkir'] ?? 'pickup');
                @endphp
                <div class="card" style="margin-bottom:24px">
                    <h2 class="step-title"><span class="step">1</span> <span class="step-text">Pilih Cara Pengiriman</span></h2>
                    <form method="POST" action="{{ route('cart.shipping') }}" id="shippingForm">
                        @csrf
                        <input type="hidden" name="jenisongkir" value="{{ $type }}">
                        <div class="tabs">
                            @foreach (\App\Models\Order::COURIERS as $key => $label)
                                <button type="button" class="tab" data-type="{{ $key }}">{{ $label }}</button>
                            @endforeach
                        </div>
                        <div class="tab-note"></div>

                        <div class="grid-2">
                            <div class="field">
                                <label for="f-namatemp">Nama Penerima</label>
                                <input id="f-namatemp" type="text" name="namatemp" value="{{ old('namatemp', $s['namatemp'] ?? $user->name) }}" required>
                            </div>
                            <div class="field">
                                <label for="f-hptemp">No. HP / WhatsApp</label>
                                <input id="f-hptemp" type="text" name="hptemp" value="{{ old('hptemp', $s['hptemp'] ?? $user->phone) }}" required>
                            </div>
                        </div>
                        <div class="field">
                            <label for="f-emailtemp">Email</label>
                            <input id="f-emailtemp" type="email" name="emailtemp" value="{{ old('emailtemp', $s['emailtemp'] ?? $user->email) }}" required>
                        </div>
                        @php
                            $selProv = old('provinsitemp', $s['provinsitemp'] ?? '');
                            $selKota = old('kotatemp', $s['kotatemp'] ?? '');
                            $selKec = old('kecamatantemp', $s['kecamatantemp'] ?? '');
                            $kotaList = \App\Support\Wilayah::cities($selProv);
                            $kecList = \App\Support\Wilayah::districts($selProv, $selKota);
                        @endphp
                        <fieldset class="address" data-show="gojek grab jne jnt" data-wilayah="{{ asset('data/wilayah.json') }}">
                            <legend>Alamat pengiriman</legend>
                            <div class="grid-2">
                                <div class="field">
                                    <label for="provinsitemp">Provinsi</label>
                                    <select name="provinsitemp" id="provinsitemp" data-level="prov">
                                        <option value="">Pilih provinsi</option>
                                        @foreach ($provinces as $prov)
                                            <option value="{{ $prov }}" @selected($selProv === $prov)>{{ $prov }}</option>
                                        @endforeach
                                    </select>
                                    @error('provinsitemp')<div class="error-text">{{ $message }}</div>@enderror
                                </div>
                                <div class="field">
                                    <label for="kotatemp">Kota / Kabupaten</label>
                                    <select name="kotatemp" id="kotatemp" data-level="kota" @disabled(! $kotaList)>
                                        <option value="">{{ $kotaList ? 'Pilih kota / kabupaten' : 'Pilih provinsi dulu' }}</option>
                                        @foreach ($kotaList as $k)
                                            <option value="{{ $k }}" @selected($selKota === $k)>{{ $k }}</option>
                                        @endforeach
                                    </select>
                                    @error('kotatemp')<div class="error-text">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="grid-2">
                                <div class="field">
                                    <label for="kecamatantemp">Kecamatan</label>
                                    <select name="kecamatantemp" id="kecamatantemp" data-level="kec" @disabled(! $kecList)>
                                        <option value="">{{ $kecList ? 'Pilih kecamatan' : 'Pilih kota / kabupaten dulu' }}</option>
                                        @foreach ($kecList as $k)
                                            <option value="{{ $k }}" @selected($selKec === $k)>{{ $k }}</option>
                                        @endforeach
                                    </select>
                                    @error('kecamatantemp')<div class="error-text">{{ $message }}</div>@enderror
                                </div>
                                <div class="field">
                                    <label for="ziptemp">Kode Pos</label>
                                    <input type="text" name="ziptemp" id="ziptemp" inputmode="numeric" maxlength="5" pattern="[0-9]{5}"
                                           value="{{ old('ziptemp', $s['ziptemp'] ?? '') }}" placeholder="5 angka">
                                    @error('ziptemp')<div class="error-text">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="field">
                                <label for="alamattemp">Deskripsi lengkap alamat</label>
                                <textarea name="alamattemp" id="alamattemp" rows="3"
                                          placeholder="Nama jalan, nomor rumah, RT/RW, nama gedung, atau patokan">{{ old('alamattemp', $s['alamattemp'] ?? $user->address) }}</textarea>
                                @error('alamattemp')<div class="error-text">{{ $message }}</div>@enderror
                            </div>
                            <p class="hint" data-show="jne jnt">Ongkir JNE dan J&amp;T dihitung dari provinsi tujuan dan berat paket.</p>
                        </fieldset>
                        <button class="btn btn-dark">Hitung Ongkir</button>
                    </form>
                </div>

                {{-- ===== STEP 2: Pembayaran -> POST /checkout_proses ===== --}}
                <div class="card" id="pembayaran">
                    <h2 class="step-title"><span class="step">2</span> <span class="step-text">Pilih Cara Pembayaran</span></h2>
                    @if (! $shipping['data'])
                        <div class="alert alert-info" style="margin-top:0">Isi cara pengiriman lalu klik <b>Hitung Ongkir</b> terlebih dahulu.</div>
                    @endif
                    @php $isPickup = ($shipping['data']['jenisongkir'] ?? null) === 'pickup'; @endphp
                    <form method="POST" action="{{ route('checkout') }}">
                        @csrf
                        <label class="radio">
                            <input type="radio" name="payment_method_id" value="{{ \App\Models\Order::PAYMENT_AUTO }}" checked>
                            <span><b>QRIS / Virtual Account (Otomatis Lunas)</b> <span class="pill pill-lunas" style="font-size:12px">Instan</span><br><small class="muted-text">Verifikasi otomatis tanpa perlu konfirmasi manual</small></span>
                        </label>
                        <label class="radio">
                            <input type="radio" name="payment_method_id" value="{{ \App\Models\Order::PAYMENT_TRANSFER }}">
                            <span><b>Transfer ke BCA</b><br><small class="muted-text">{{ config('store.bank') }}</small> @include('partials.sample-tag')</span>
                        </label>
                        <label class="radio {{ $isPickup ? '' : 'disabled' }}">
                            <input type="radio" name="payment_method_id" value="{{ \App\Models\Order::PAYMENT_CASH }}" @disabled(! $isPickup)>
                            <span><b>Cash di kantor</b><br><small style="color:var(--muted)">Khusus pengambilan langsung di kantor</small></span>
                        </label>

                        <div class="field" style="margin-top:16px">
                            <label for="f-kupon">Kupon Promo</label>
                            <input id="f-kupon" type="text" name="kupon" value="{{ old('kupon') }}" placeholder="Kosongkan bila tidak ada (contoh: HEMAT10)">
                        </div>
                        <div class="field">
                            <label for="f-comment">Catatan Khusus</label>
                            <textarea id="f-comment" name="comment" rows="2" placeholder="Permintaan khusus, tulis di sini">{{ old('comment') }}</textarea>
                        </div>
                        <button class="btn btn-red btn-block" @disabled(! $shipping['data'])>Bayar Sekarang</button>
                    </form>
                </div>
            </div>

            {{-- ===== Ringkasan ===== --}}
            <aside class="card" style="position:sticky;top:150px">
                <h2 class="block-title" style="margin:0 0 14px">Total Belanja</h2>
                <div class="summary-row"><span>Subtotal</span><span>@rupiah($subtotal)</span></div>
                <div class="summary-row">
                    <span>Ongkos Kirim</span>
                    <span>{{ $shipping['data'] ? 'Rp '.number_format($shipping['cost'], 0, ',', '.') : '-' }}</span>
                </div>
                @if ($shipping['data'])
                    <div class="ship-note">
                        {{ \App\Models\Order::COURIERS[$shipping['data']['jenisongkir']] }}: {{ $shipping['note'] }}
                        @if (! empty($shipping['data']['alamattemp']))
                            <br>Dikirim ke {{ $shipping['data']['kecamatantemp'] }}, {{ $shipping['data']['kotatemp'] }}, {{ $shipping['data']['provinsitemp'] }} {{ $shipping['data']['ziptemp'] }}
                        @endif
                    </div>
                @endif
                <div class="summary-row total"><span>Grand Total</span><span class="price">@rupiah($subtotal + $shipping['cost'])</span></div>
                <p class="ship-note">Diskon kupon dihitung saat klik <b>Bayar Sekarang</b>.</p>
            </aside>
        </div>
    @endif
</div>
@endsection
