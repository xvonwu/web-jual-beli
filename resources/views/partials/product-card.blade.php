<article @class(['p-card', 'is-sold' => $p->isSoldOut()])>
    <a href="{{ $p->url() }}" class="thumb">
        @if ($p->is_preorder)
            <span class="tag tag-pre">Preorder</span>
        @elseif ($p->isSoldOut())
            <span class="tag tag-sold">Habis</span>
        @elseif ($p->old_price)
            <span class="tag tag-promo">Hemat {{ round(100 - $p->price / $p->old_price * 100) }}%</span>
        @elseif ($p->is_new_release)
            <span class="tag tag-new">Baru</span>
        @endif
        <img src="{{ $p->imageUrl() }}" {!! $p->imageAttrs() !!} alt="{{ $p->name }}" loading="lazy">
    </a>
    <div class="sticker">
        <span class="price">@rupiah($p->price)</span>
        @if ($p->old_price)<s class="old-price">@rupiah($p->old_price)</s>@endif
    </div>
    <div class="info">
        <span class="plat">{{ $p->category?->name }}</span>
        <a href="{{ $p->url() }}" class="name">{{ $p->name }}</a>
        <div class="actions">
            @if ($p->isSoldOut())
                <a href="{{ $p->url() }}" class="btn btn-outline btn-sm">Lihat detail</a>
            @else
                {{-- Kalau belum login, middleware "auth" mengarahkan ke /login lalu kembali ke sini --}}
                <a href="{{ route('cart.add', $p->id) }}" class="btn btn-red btn-sm">Beli</a>
                <a href="{{ $p->url() }}" class="btn btn-outline btn-sm">Detail</a>
            @endif
        </div>
    </div>
</article>
