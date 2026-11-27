@extends('layouts.app')
@section('title', 'Profil & Alamat Saya')

@section('content')
    <div class="page-head">
        <div class="crumb"><a href="{{ route('home') }}">Beranda</a> / Akun Saya</div>
        <h1>Profil &amp; Alamat</h1>
        <div class="ghost" aria-hidden="true">AKUN</div>
    </div>

    <div class="layout-2">
        <div>
            {{-- ===== 1. Informasi Profil & Foto ===== --}}
            <div class="card" style="margin-bottom:24px">
                <h2 class="card-title">Informasi Profil</h2>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;margin-bottom:22px;padding:16px;background:var(--cloud);border-radius:12px">
                        @php $avatar = $user->avatarUrl(); @endphp
                        <div style="width:88px;height:88px;border-radius:50%;overflow:hidden;border:3px solid var(--ink);background:#fff;display:grid;place-items:center;flex-shrink:0">
                            @if ($avatar)
                                <img id="avatarPreview" src="{{ $avatar }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover">
                            @else
                                <img id="avatarPreview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none">
                                <span id="avatarInitials" style="font-size:28px;font-weight:800;color:var(--cobalt)">
                                    {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                                </span>
                            @endif
                        </div>
                        <div style="flex:1;min-width:200px">
                            <label for="avatar" class="field-label" style="display:block">Foto Profil</label>
                            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
                            <p style="font-size:13px;color:var(--muted);margin:6px 0 0">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
                            @error('avatar')<p class="field-status" style="margin-top:6px">{{ $message }}</p>@enderror
                        </div>
                        @if ($avatar)
                            <button type="submit" form="removeAvatarForm" class="btn btn-outline btn-sm" style="color:var(--signal-text);border-color:var(--signal-text)">
                                Hapus Foto
                            </button>
                        @endif
                    </div>

                    <div class="field">
                        <label for="name" class="field-label">Nama Lengkap *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<p class="field-status">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="email" class="field-label">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<p class="field-status">{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label for="phone" class="field-label">No. HP / WhatsApp *</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                            @error('phone')<p class="field-status">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-red">Simpan Profil</button>
                </form>
                @if ($user->avatarUrl())
                    <form id="removeAvatarForm" method="POST" action="{{ route('profile.avatar.remove') }}" style="display:none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>

            {{-- ===== 2. Alamat Pengiriman Default ===== --}}
            <div class="card" id="alamat">
                <h2 class="card-title">Alamat Pengiriman Default</h2>
                <p style="color:var(--muted);margin:-6px 0 20px;font-size:15px">
                    Alamat ini otomatis terisi saat Anda menghitung ongkir di keranjang belanja.
                </p>

                @php
                    $selProv = old('province', $user->province ?? '');
                    $selKota = old('city', $user->city ?? '');
                    $selKec = old('district', $user->district ?? '');
                    $kotaList = \App\Support\Wilayah::cities($selProv);
                    $kecList = \App\Support\Wilayah::districts($selProv, $selKota);
                @endphp

                <form method="POST" action="{{ route('profile.address') }}" data-wilayah="{{ asset('data/wilayah.json') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid-2">
                        <div class="field">
                            <label for="province" class="field-label">Provinsi *</label>
                            <select name="province" id="province" data-level="prov" required>
                                <option value="">Pilih provinsi</option>
                                @foreach ($provinces as $prov)
                                    <option value="{{ $prov }}" @selected($selProv === $prov)>{{ $prov }}</option>
                                @endforeach
                            </select>
                            @error('province')<p class="field-status">{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label for="city" class="field-label">Kota / Kabupaten *</label>
                            <select name="city" id="city" data-level="kota" @disabled(! $kotaList) required>
                                <option value="">{{ $kotaList ? 'Pilih kota / kabupaten' : 'Pilih provinsi dulu' }}</option>
                                @foreach ($kotaList as $k)
                                    <option value="{{ $k }}" @selected($selKota === $k)>{{ $k }}</option>
                                @endforeach
                            </select>
                            @error('city')<p class="field-status">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="district" class="field-label">Kecamatan *</label>
                            <select name="district" id="district" data-level="kec" @disabled(! $kecList) required>
                                <option value="">{{ $kecList ? 'Pilih kecamatan' : 'Pilih kota / kabupaten dulu' }}</option>
                                @foreach ($kecList as $k)
                                    <option value="{{ $k }}" @selected($selKec === $k)>{{ $k }}</option>
                                @endforeach
                            </select>
                            @error('district')<p class="field-status">{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label for="zip" class="field-label">Kode Pos *</label>
                            <input type="text" name="zip" id="zip" inputmode="numeric" maxlength="5" pattern="[0-9]{5}"
                                   value="{{ old('zip', $user->zip) }}" placeholder="5 angka" required>
                            @error('zip')<p class="field-status">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="address" class="field-label">Alamat Lengkap *</label>
                        <textarea name="address" id="address" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, gedung, atau patokan" required>{{ old('address', $user->address) }}</textarea>
                        @error('address')<p class="field-status">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn btn-dark">Simpan Alamat Default</button>
                </form>
            </div>
        </div>

        {{-- ===== Sidebar Akun & Ganti Password ===== --}}
        <aside>
            <div class="card" style="margin-bottom:24px">
                <h2 class="block-title" style="margin-top:0">Ringkasan Akun</h2>
                <dl class="meta" style="margin:0">
                    <dt>Status</dt><dd><span class="pill {{ $user->is_admin ? 'pill-menunggu_verifikasi' : 'pill-lunas' }}">{{ $user->is_admin ? 'Admin' : 'Pembeli' }}</span></dd>
                    <dt>Bergabung</dt><dd>{{ $user->created_at?->translatedFormat('d F Y') }}</dd>
                    <dt>Total Pesanan</dt><dd><a href="{{ route('orders.index') }}"><b>{{ $ordersCount }} pesanan</b></a></dd>
                    <dt>Alamat Utama</dt><dd>{{ $user->fullAddress() ?: 'Belum diatur' }}</dd>
                </dl>
                <div style="margin-top:18px;display:flex;flex-direction:column;gap:8px">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline btn-block btn-sm">Lihat Pesanan Saya</a>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline btn-block btn-sm">Buka Keranjang</a>
                </div>
            </div>

            <div class="card">
                <h2 class="block-title" style="margin-top:0">Ganti Password</h2>
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="current_password" class="field-label">Password Saat Ini *</label>
                        <input type="password" id="current_password" name="current_password" required>
                        @error('current_password')<p class="field-status">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="password" class="field-label">Password Baru *</label>
                        <input type="password" id="password" name="password" minlength="6" required>
                        @error('password')<p class="field-status">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="password_confirmation" class="field-label">Ulangi Password Baru *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" minlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-outline btn-block">Ubah Password</button>
                </form>
            </div>
        </aside>
    </div>

    <script>
        document.getElementById('avatar')?.addEventListener('change', function (e) {
            const file = e.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (ev) {
                const img = document.getElementById('avatarPreview');
                const ini = document.getElementById('avatarInitials');
                if (img) {
                    img.src = ev.target.result;
                    img.style.display = 'block';
                }
                if (ini) ini.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    </script>
@endsection
