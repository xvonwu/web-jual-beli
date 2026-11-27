<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik (Sitemap di flowchart)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman kategori: /harga/{platform}/{id}/{page}/{seo}
Route::get('/harga/{platform}/{category}/{page?}/{seo?}', [CatalogController::class, 'category'])
    ->whereNumber(['category', 'page'])->name('category.show');

// Detail produk: /jual/{id}/{slug}
Route::get('/jual/{product}/{slug?}', [CatalogController::class, 'product'])
    ->whereNumber('product')->name('product.show');

Route::get('/search', [CatalogController::class, 'search'])->name('search');
Route::get('/promo', [CatalogController::class, 'promo'])->name('promo');
Route::get('/preorder/{platform?}', [CatalogController::class, 'preorder'])->name('preorder');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privasi', [PageController::class, 'privacy'])->name('privacy');
Route::get('/syarat', [PageController::class, 'terms'])->name('terms');
Route::post('/subscribe', [PageController::class, 'subscribe'])->name('subscribe');

/*
|--------------------------------------------------------------------------
| Akun (BARU): Login / Daftar / Logout
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Wajib login: beli, keranjang, bayar, pelunasan
| Middleware "auth" = kotak "Sudah login?" & "Sesi login masih aktif?" di flowchart.
| Kalau belum login -> diarahkan ke /login lalu kembali ke halaman asal.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/beli/{product}/sekarang', [CartController::class, 'add'])
        ->whereNumber('product')->name('cart.add');

    Route::get('/belanja', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/belanja/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/belanja/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/belanja/kosongkan', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/belanja/kembalikan', [CartController::class, 'restore'])->name('cart.restore');

    Route::post('/hitung_ongkir', [CartController::class, 'shipping'])->name('cart.shipping');
    Route::post('/checkout_proses', [CartController::class, 'checkout'])->name('checkout');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/invoice/{invoice}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/payment_confirm', [OrderController::class, 'paymentForm'])->name('payment.form');
    Route::post('/payment_confirm_save', [OrderController::class, 'paymentSave'])->name('payment.save');
});

/*
|--------------------------------------------------------------------------
| Admin: verifikasi pembayaran -> LUNAS -> dikirim
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
});
