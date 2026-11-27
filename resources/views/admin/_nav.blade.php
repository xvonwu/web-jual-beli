<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px;align-items:center">
    <a href="{{ route('admin.orders.index') }}"
       class="btn btn-sm {{ request()->routeIs('admin.orders.*') ? 'btn-red' : 'btn-outline' }}">
        Verifikasi Pesanan
    </a>
    <a href="{{ route('admin.products.index') }}"
       class="btn btn-sm {{ request()->routeIs('admin.products.index', 'admin.products.edit') ? 'btn-red' : 'btn-outline' }}">
        Kelola Produk
    </a>
    <a href="{{ route('admin.products.create') }}"
       class="btn btn-sm {{ request()->routeIs('admin.products.create') ? 'btn-yellow' : 'btn-outline' }}"
       style="margin-left:auto">
        + Upload Produk Baru
    </a>
</div>
