{{-- Pop-up notifikasi di tengah layar: session('popup') = [type: login|logout|payment, title, message, action?] --}}
@php($pop = session('popup'))
@if (is_array($pop))
    <div class="popup-backdrop" id="popup" data-popup>
        <div class="popup popup-{{ $pop['type'] }}" role="dialog" aria-modal="true" aria-labelledby="popupTitle" aria-describedby="popupMsg">
            <div class="popup-head">
                <button type="button" class="popup-close" data-popup-close aria-label="Tutup">&times;</button>
            </div>
            @if ($pop['type'] === 'cart')
                <div class="popup-product"><img src="{{ $pop['image'] }}" {!! \App\Support\Img::attrs($pop['image']) !!} alt=""></div>
            @elseif ($pop['type'] === 'remove')
                <div class="popup-product popup-product-removed">
                    @if (! empty($pop['image']))<img src="{{ $pop['image'] }}" {!! \App\Support\Img::attrs($pop['image']) !!} alt="">@endif
                    <span class="popup-trash" aria-hidden="true">@include('partials.icon', ['n' => 'trash'])</span>
                </div>
            @else
            <div class="popup-icon" aria-hidden="true">
                @if ($pop['type'] === 'logout')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path class="popup-check" d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                @endif
            </div>
            @endif
            <div class="popup-body">
                <h2 id="popupTitle">{{ $pop['title'] }}</h2>
                <p id="popupMsg">{{ $pop['message'] }}</p>
                @if ($pop['type'] === 'cart')
                    <div class="popup-cartline">
                        <span class="popup-sticker">@rupiah($pop['price'])</span>
                        <span>Jumlah di keranjang: <b>{{ $pop['qty'] }}</b></span>
                    </div>
                    <p class="popup-summary">Total keranjang {{ $pop['cart_count'] }} barang, <b>@rupiah($pop['cart_total'])</b></p>
                @endif
                @if ($pop['type'] === 'remove')
                    <p class="popup-summary">{{ $pop['summary'] }}</p>
                @endif
                <div class="popup-actions">
                    @if (! empty($pop['undo']))
                        <form method="POST" action="{{ route('cart.restore') }}" class="popup-undo">
                            @csrf
                            @foreach ($pop['undo'] as $i => $row)
                                <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $row['product_id'] }}">
                                <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $row['qty'] }}">
                            @endforeach
                            <button type="submit" class="btn btn-outline">Batalkan</button>
                        </form>
                        <button type="button" class="btn btn-red" data-popup-close>Oke</button>
                    @elseif (! empty($pop['action']))
                        <a href="{{ $pop['action']['url'] }}" class="btn btn-red">{{ $pop['action']['label'] }}</a>
                        <button type="button" class="btn btn-outline" data-popup-close>{{ $pop['dismiss'] ?? 'Nanti saja' }}</button>
                    @else
                        <button type="button" class="btn btn-red" data-popup-close>Oke</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
