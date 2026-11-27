@extends('layouts.app')
@php $isEdit = $product->exists; @endphp
@section('title', $isEdit ? 'Admin: Edit '.$product->name : 'Admin: Upload Produk Baru')

@section('content')
    <div class="page-head">
        <div class="crumb">
            <a href="{{ route('admin.orders.index') }}">Panel Admin</a> /
            <a href="{{ route('admin.products.index') }}">Produk</a> /
            {{ $isEdit ? 'Edit' : 'Upload Baru' }}
        </div>
        <h1>{{ $isEdit ? 'Edit Produk' : 'Upload Produk Baru' }}</h1>
        <div class="ghost" aria-hidden="true">{{ $isEdit ? 'EDIT' : 'UPLOAD' }}</div>
    </div>

    @include('admin._nav')

    <form method="POST"
          action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="layout-2">
            <div class="card">
                <h2 class="card-title">Informasi Produk</h2>

                <div class="field">
                    <label for="name" class="field-label">Nama Produk *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                           placeholder="Contoh: PS5 Astro Bot Game" required>
                    @error('name')<p class="field-status">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label for="category_id" class="field-label">Kategori &amp; Platform *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $platform => $cats)
                            <optgroup label="{{ $platform }}">
                                @foreach ($cats as $cat)
                                    <option value="{{ $cat->id }}" @selected((string) old('category_id', $product->category_id) === (string) $cat->id)>
                                        {{ $platform }} - {{ $cat->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('category_id')<p class="field-status">{{ $message }}</p>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div class="field">
                        <label for="price" class="field-label">Harga Jual (Rp) *</label>
                        <input type="number" id="price" name="price" min="1000" step="1000"
                               value="{{ old('price', $product->price) }}" placeholder="849000" required>
                        @error('price')<p class="field-status">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="old_price" class="field-label">Harga Coret / Asli (opsional)</label>
                        <input type="number" id="old_price" name="old_price" min="1000" step="1000"
                               value="{{ old('old_price', $product->old_price) }}" placeholder="Isi jika sedang promo">
                        @error('old_price')<p class="field-status">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                    <div class="field">
                        <label for="stock" class="field-label">Stok *</label>
                        <input type="number" id="stock" name="stock" min="0"
                               value="{{ old('stock', $product->stock ?? 1) }}" required>
                        @error('stock')<p class="field-status">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="weight" class="field-label">Berat (kg) *</label>
                        <input type="number" id="weight" name="weight" min="0.01" max="100" step="0.01"
                               value="{{ old('weight', $product->weight ?? 0.25) }}" required>
                        @error('weight')<p class="field-status">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="region" class="field-label">Region (opsional)</label>
                        <input type="text" id="region" name="region" value="{{ old('region', $product->region) }}"
                               placeholder="R3 / Asia / US">
                        @error('region')<p class="field-status">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="release_date" class="field-label">Tanggal Rilis (opsional)</label>
                    <input type="date" id="release_date" name="release_date"
                           value="{{ old('release_date', $product->release_date?->format('Y-m-d')) }}">
                    @error('release_date')<p class="field-status">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label for="description" class="field-label">Deskripsi Produk</label>
                    <textarea id="description" name="description" rows="4"
                              placeholder="Tulis spesifikasi, kondisi (BNIB/segel), atau detail garansi...">{{ old('description', $product->description) }}</textarea>
                    @error('description')<p class="field-status">{{ $message }}</p>@enderror
                </div>

                <div class="field" style="display:flex;gap:20px;flex-wrap:wrap;padding-top:6px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600">
                        <input type="checkbox" name="is_new_release" value="1"
                               @checked(old('is_new_release', $product->is_new_release)) style="width:18px;height:18px">
                        Rilis Baru
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600">
                        <input type="checkbox" name="is_preorder" value="1"
                               @checked(old('is_preorder', $product->is_preorder)) style="width:18px;height:18px">
                        Preorder
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600">
                        <input type="checkbox" name="is_promo" value="1"
                               @checked(old('is_promo', $product->is_promo)) style="width:18px;height:18px">
                        Tandai Promo
                    </label>
                </div>
            </div>

            <aside class="card">
                <h2 class="card-title">Gambar Produk</h2>
                <div style="background:var(--cloud);border:2px dashed var(--line);border-radius:10px;padding:18px;text-align:center;margin-bottom:18px">
                    <img id="imgPreview"
                         src="{{ $isEdit ? $product->imageUrl() : asset('images/products/placeholder.svg') }}"
                         alt="Preview gambar produk"
                         style="max-width:100%;max-height:240px;object-fit:contain;border-radius:6px">
                </div>

                <div class="field">
                    <label for="image" class="field-label">
                        {{ $isEdit ? 'Ganti Gambar (opsional)' : 'Upload Gambar *' }}
                    </label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                           {{ $isEdit ? '' : 'required' }}>
                    <p style="font-size:13px;color:var(--muted);margin:8px 0 0">
                        Format: JPG, PNG, atau WebP. Maksimal 4 MB.
                    </p>
                    @error('image')<p class="field-status" style="margin-top:8px">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn btn-red btn-block">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Upload & Publish Produk' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-block" style="margin-top:10px">
                    Batal
                </a>
                @if ($isEdit)
                    <button type="submit" form="deleteProductForm" class="btn btn-outline btn-block"
                            style="margin-top:10px;color:var(--signal-text);border-color:var(--signal-text)">
                        Hapus Produk
                    </button>
                @endif
            </aside>
        </div>
    </form>

    @if ($isEdit)
        <form id="deleteProductForm" method="POST" action="{{ route('admin.products.destroy', $product) }}"
              data-confirm-delete="{{ $product->name }}">
            @csrf
            @method('DELETE')
        </form>
    @endif

    <script>
        document.getElementById('image')?.addEventListener('change', function (e) {
            const file = e.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (ev) {
                document.getElementById('imgPreview').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
@endsection
