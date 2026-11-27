<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Support\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Alur flowchart: /beli -> /belanja -> /hitung_ongkir -> /checkout_proses
 * Semua method di sini sudah dilindungi middleware "auth" (wajib login).
 */
class CartController extends Controller
{
    /** GET/POST /beli/{id}/sekarang : tambah ke keranjang milik user yang login */
    public function add(Request $request, Product $product)
    {
        if ($product->isSoldOut()) {
            return redirect($product->url())->with('error', 'Maaf, produk ini sedang Sold Out.');
        }

        $qty = max(1, (int) $request->input('qty', 1));
        $cart = Cart::firstOrNew(['user_id' => $request->user()->id, 'product_id' => $product->id]);
        $newQty = ($cart->exists ? $cart->qty : 0) + $qty;

        if (! $product->is_preorder) {
            $newQty = min($newQty, $product->stock);
        }

        $added = $newQty - ($cart->exists ? $cart->qty : 0);
        $cart->qty = $newQty;
        $cart->save();

        $items = $this->items($request);

        // Kembali ke halaman tempat tombol Beli diklik (bukan ke login/daftar), lalu tampilkan pop-up keranjang
        $back = url()->previous();
        $path = parse_url($back, PHP_URL_PATH) ?? '';
        if ($back === $request->fullUrl() || preg_match('#/(login|register|beli/)#', $path)) {
            $back = $product->url();
        }

        return redirect()->to($back)->with('popup', [
            'type' => 'cart',
            'title' => $added > 0 ? 'Masuk keranjang!' : 'Sudah di keranjang',
            'message' => $added > 0 ? $product->name : $product->name.' (stok tersisa sudah ada di keranjang)',
            'image' => $product->imageUrl(),
            'price' => $product->price,
            'qty' => $cart->qty,
            'cart_count' => (int) $items->sum('qty'),
            'cart_total' => (int) $items->sum(fn ($c) => $c->subtotal()),
            'action' => ['label' => 'Lihat keranjang', 'url' => route('cart.index')],
            'dismiss' => 'Lanjut belanja',
        ]);
    }

    /** GET /belanja */
    public function index(Request $request)
    {
        $items = $this->items($request);
        $subtotal = $items->sum(fn ($c) => $c->subtotal());
        $shipping = $this->shippingQuote($request, $items);

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'provinces' => Wilayah::provinces(),
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request, Cart $cart)
    {
        abort_unless($cart->user_id === $request->user()->id, 403);
        $qty = max(1, (int) $request->input('qty', 1));
        $product = $cart->product;

        if (! $product->is_preorder && $qty > $product->stock) {
            return back()->with('error', 'Stok '.$product->name.' hanya tersisa '.$product->stock.'.');
        }

        $cart->update(['qty' => $qty]);

        return back();
    }

    public function remove(Request $request, Cart $cart)
    {
        abort_unless($cart->user_id === $request->user()->id, 403);
        $product = $cart->product;
        $undo = [['product_id' => $cart->product_id, 'qty' => $cart->qty]];
        $cart->delete();

        return back()->with('popup', $this->removedPopup(
            $request,
            'Dihapus dari keranjang',
            $product->name.($undo[0]['qty'] > 1 ? ' ('.$undo[0]['qty'].' pcs)' : ''),
            $product->imageUrl(),
            $undo
        ));
    }

    public function clear(Request $request)
    {
        $items = $this->items($request);
        $undo = $items->map(fn ($c) => ['product_id' => $c->product_id, 'qty' => $c->qty])->values()->all();

        Cart::where('user_id', $request->user()->id)->delete();
        $request->session()->forget('shipping');

        return redirect()->route('cart.index')->with('popup', $this->removedPopup(
            $request,
            'Keranjang dikosongkan',
            $items->count().' produk ('.$items->sum('qty').' barang) dihapus dari keranjang.',
            optional($items->first()?->product)->imageUrl(),
            $undo
        ));
    }

    /** POST /belanja/kembalikan : batalkan hapus, masukkan lagi produk ke keranjang */
    public function restore(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'max:100'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $count = 0;
        foreach ($data['items'] as $row) {
            $product = Product::find($row['product_id']);
            if ($product->isSoldOut()) {
                continue;
            }
            $cart = Cart::firstOrNew(['user_id' => $request->user()->id, 'product_id' => $product->id]);
            $qty = ($cart->exists ? $cart->qty : 0) + (int) $row['qty'];
            $cart->qty = $product->is_preorder ? $qty : min($qty, $product->stock);
            $cart->save();
            $count++;
        }

        return redirect()->route('cart.index')->with('success', $count
            ? 'Produk dikembalikan ke keranjang.'
            : 'Produk tidak bisa dikembalikan karena stoknya habis.');
    }

    /** Data pop-up "dihapus" (dengan tombol Batalkan) */
    private function removedPopup(Request $request, string $title, string $message, ?string $image, array $undo): array
    {
        $left = $this->items($request);

        return [
            'type' => 'remove',
            'title' => $title,
            'message' => $message,
            'image' => $image,
            'summary' => $left->isEmpty()
                ? 'Keranjang kamu sekarang kosong.'
                : 'Sisa keranjang '.$left->sum('qty').' barang, Rp '.number_format($left->sum(fn ($c) => $c->subtotal()), 0, ',', '.'),
            'undo' => $undo,
        ];
    }

    /** POST /hitung_ongkir : simpan data penerima + jenis pengiriman ke session */
    public function shipping(Request $request)
    {
        $kirim = $request->jenisongkir !== 'pickup';
        $prov = $request->input('provinsitemp');
        $data = $request->validate([
            'jenisongkir' => ['required', Rule::in(array_keys(Order::COURIERS))],
            'namatemp' => ['required', 'string', 'max:100'],
            'hptemp' => ['required', 'string', 'max:20'],
            'emailtemp' => ['required', 'email'],
            'provinsitemp' => [Rule::requiredIf($kirim), 'nullable', Rule::in(Wilayah::provinces())],
            'kotatemp' => [Rule::requiredIf($kirim), 'nullable', Rule::in(Wilayah::cities($prov))],
            'kecamatantemp' => [Rule::requiredIf($kirim), 'nullable', Rule::in(Wilayah::districts($prov, $request->input('kotatemp')))],
            'ziptemp' => [Rule::requiredIf($kirim), 'nullable', 'digits:5'],
            'alamattemp' => [Rule::requiredIf($kirim), 'nullable', 'string', 'max:500'],
        ], [
            'provinsitemp.required' => 'Pilih provinsi tujuan.',
            'provinsitemp.in' => 'Pilih provinsi dari daftar.',
            'kotatemp.required' => 'Pilih kota / kabupaten.',
            'kotatemp.in' => 'Kota / kabupaten ini tidak ada di provinsi '.($prov ?: 'yang dipilih').'.',
            'kecamatantemp.required' => 'Pilih kecamatan.',
            'kecamatantemp.in' => 'Kecamatan ini tidak ada di kota / kabupaten yang dipilih.',
            'ziptemp.required' => 'Isi kode pos.',
            'ziptemp.digits' => 'Kode pos harus 5 angka.',
            'alamattemp.required' => 'Isi deskripsi lengkap alamat (nama jalan, nomor rumah, RT/RW, patokan).',
        ]);

        if (in_array($data['jenisongkir'], ['jne', 'jnt'])
            && ! ShippingRate::where('courier', $data['jenisongkir'])->where('province', $data['provinsitemp'])->exists()) {
            return back()->withErrors(['provinsitemp' => 'Tarif '.Order::COURIERS[$data['jenisongkir']].' ke provinsi ini belum diatur.'])->withInput();
        }

        if (! $kirim) {
            foreach (['provinsitemp', 'kotatemp', 'kecamatantemp', 'ziptemp', 'alamattemp'] as $k) {
                $data[$k] = null;
            }
        }

        $request->session()->put('shipping', $data);

        return redirect()->to(route('cart.index').'#pembayaran')->with('success', 'Ongkos kirim sudah dihitung. Silakan pilih cara pembayaran.');
    }

    /** POST /checkout_proses : buat invoice */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'payment_method_id' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'kupon' => ['nullable', 'string', 'max:50'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $items = $this->items($request);
        $ship = $request->session()->get('shipping');

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }
        if (! $ship) {
            return redirect()->route('cart.index')->with('error', 'Pilih cara pengiriman dan klik "Hitung Ongkir" dulu.');
        }
        if ((int) $data['payment_method_id'] === Order::PAYMENT_CASH && $ship['jenisongkir'] !== 'pickup') {
            return back()->with('error', 'Pembayaran cash hanya untuk Ambil Langsung di kantor.');
        }

        $subtotal = $items->sum(fn ($c) => $c->subtotal());
        $coupon = null;
        if (! empty($data['kupon'])) {
            $coupon = Coupon::findValid($data['kupon'], $subtotal);
            if (! $coupon) {
                return back()->withInput()->with('error', 'Kupon tidak valid, kedaluwarsa, atau belum memenuhi minimal belanja.');
            }
        }

        $discount = $coupon ? $coupon->discountFor($subtotal) : 0;
        $shippingCost = $this->shippingQuote($request, $items)['cost'];

        try {
            $order = DB::transaction(function () use ($request, $items, $ship, $data, $subtotal, $discount, $coupon, $shippingCost) {
                // Kunci baris produk agar stok tidak dobel terjual
                foreach ($items as $item) {
                    $p = Product::lockForUpdate()->find($item->product_id);
                    if (! $p->is_preorder && $p->stock < $item->qty) {
                        throw ValidationException::withMessages(['stok' => "Stok {$p->name} tidak cukup (sisa {$p->stock})."]);
                    }
                    if ($p->stock > 0) {
                        $p->decrement('stock', min($item->qty, $p->stock));
                    }
                }

                $order = Order::create([
                    'invoice' => Order::makeInvoice(),
                    'user_id' => $request->user()->id,
                    'customer_name' => $ship['namatemp'],
                    'phone' => $ship['hptemp'],
                    'email' => $ship['emailtemp'],
                    'address' => $ship['alamattemp'] ?? null,
                    'province' => $ship['provinsitemp'] ?? null,
                    'city' => $ship['kotatemp'] ?? null,
                    'district' => $ship['kecamatantemp'] ?? null,
                    'zip' => $ship['ziptemp'] ?? null,
                    'courier' => $ship['jenisongkir'],
                    'shipping_cost' => $shippingCost,
                    'payment_method_id' => $data['payment_method_id'],
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'coupon_code' => $coupon?->code,
                    'grand_total' => $subtotal - $discount + $shippingCost,
                    'note' => $data['comment'] ?? null,
                ]);

                foreach ($items as $item) {
                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'price' => $item->product->price,
                        'qty' => $item->qty,
                        'subtotal' => $item->subtotal(),
                    ]);
                }

                Cart::where('user_id', $request->user()->id)->delete();

                if ($coupon) {
                    Coupon::whereKey($coupon->id)->increment('used_count');
                }

                return $order;
            });
        } catch (ValidationException $e) {
            return redirect()->route('cart.index')->with('error', $e->validator->errors()->first());
        }

        $request->session()->forget('shipping');

        if ($order->payment_method_id === Order::PAYMENT_AUTO) {
            return redirect()->route('payment.auto', $order->invoice);
        }

        return redirect()->route('orders.show', $order->invoice)
            ->with('popup', [
                'type' => 'payment',
                'title' => 'Pesanan berhasil dibuat!',
                'message' => $order->payment_method_id === Order::PAYMENT_TRANSFER
                    ? 'Invoice '.$order->invoice.'. Transfer Rp '.number_format($order->grand_total, 0, ',', '.').' ke '.config('store.bank').', lalu kirim konfirmasi pembayaran.'
                    : 'Invoice '.$order->invoice.'. Bayar cash Rp '.number_format($order->grand_total, 0, ',', '.').' saat mengambil barang di toko.',
                'action' => $order->payment_method_id === Order::PAYMENT_TRANSFER
                    ? ['label' => 'Konfirmasi pembayaran', 'url' => route('payment.form', ['invoice' => $order->invoice])]
                    : null,
            ]);
    }

    // ----------------------------------------------------------------

    private function items(Request $request)
    {
        return Cart::with('product')->where('user_id', $request->user()->id)->get();
    }

    /** Hitung ongkir dari data di session. JNE/J&T = tarif kota x berat (dibulatkan ke atas, min 1 kg). */
    private function shippingQuote(Request $request, $items): array
    {
        $ship = $request->session()->get('shipping');
        if (! $ship) {
            return ['data' => null, 'cost' => 0, 'note' => null];
        }

        $weight = max(1, (int) ceil($items->sum(fn ($c) => $c->qty * $c->product->weight)));
        $cost = 0;
        $note = null;

        switch ($ship['jenisongkir']) {
            case 'jne':
            case 'jnt':
                $rate = ShippingRate::where('courier', $ship['jenisongkir'])->where('province', $ship['provinsitemp'] ?? null)->value('cost') ?? 0;
                $cost = $rate * $weight;
                $note = "{$weight} kg x Rp ".number_format($rate, 0, ',', '.');
                break;
            case 'gojek':
            case 'grab':
                $note = 'Ongkir dibayar langsung ke driver';
                break;
            default:
                $note = 'Ambil di kantor ('.config('store.hours').')';
        }

        return ['data' => $ship, 'cost' => $cost, 'note' => $note, 'weight' => $weight];
    }
}
