# PakHar Game — Toko Game PS (Laravel 12 + MySQL XAMPP)

Web toko game yang disusun mengikuti flowchart **rajagame-struktur-web.drawio**
(Sitemap + Alur Pembelian dengan Login), dengan desain home bergaya referensi PHLOX.

## Kebutuhan
- **XAMPP** dengan PHP **8.2 atau lebih baru** (Apache + MySQL)
- **Composer** — https://getcomposer.org/Composer-Setup.exe
- Di `C:\xampp\php\php.ini` pastikan baris ini **tidak** diawali `;` :
  `extension=zip`, `extension=fileinfo`, `extension=pdo_mysql`, `extension=mbstring`, `extension=openssl`

## Cara menjalankan (otomatis)
1. Buka **XAMPP Control Panel** → **Start** Apache dan MySQL.
2. Klik dua kali **`setup.bat`** di folder ini.
   Script akan: `composer install` → buat database `web_ps` → `migrate --seed` → `php artisan serve`.
3. Buka **http://127.0.0.1:8000**

## Cara menjalankan (manual)
```bat
composer install
copy .env.example .env        (lewati kalau .env sudah ada)
php artisan key:generate      (lewati kalau APP_KEY sudah terisi)
```
Buat database **`web_ps`** di http://localhost/phpmyadmin (collation `utf8mb4_unicode_ci`), lalu:
```bat
php artisan migrate --seed
php artisan serve
```

## Koneksi ke phpMyAdmin
Pengaturan ada di file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=web_ps
DB_USERNAME=root
DB_PASSWORD=
```
Semua tabel muncul di phpMyAdmin → database `web_ps`. Produk, stok, kategori, kupon,
dan tarif ongkir bisa diedit langsung dari sana (kolom `image` bisa diisi path gambar
di folder `public/`, mis. `images/products/foto.jpg`, atau URL).

## Akun demo
| Role  | Email               | Password |
|-------|---------------------|----------|
| Admin | admin@pakhar.test   | password |
| User  | user@pakhar.test    | password |

Kupon contoh: `HEMAT10` (10%, min. Rp300.000), `DISKON50K` (min. Rp500.000).

## Peta halaman (sesuai flowchart)
| Halaman | URL | Login? |
|---|---|---|
| Home | `/` | – |
| Kategori | `/harga/{platform}/{id}/{page}/{seo}` (PS5 161/162/163, PS4 43/45/44, XBOX 165/166/167, Others 35/157/158/172/173/32) | – |
| Detail produk | `/jual/{id}/{slug}` | – |
| Cari / Promo / Preorder | `/search?q=`, `/promo`, `/preorder/{platform}` | – |
| Login / Daftar / Logout | `/login`, `/register`, `POST /logout` | – |
| Tambah ke keranjang | `/beli/{id}/sekarang` | **Ya** |
| Keranjang | `/belanja` | **Ya** |
| Hitung ongkir | `POST /hitung_ongkir` (pickup, gojek, grab, jne, jnt) | **Ya** |
| Checkout | `POST /checkout_proses` (payment 42 = BCA, 54 = Cash) | **Ya** |
| Invoice / Pesanan Saya | `/invoice/{invoice}`, `/pesanan` | **Ya** |
| Konfirmasi pembayaran | `/payment_confirm` → `POST /payment_confirm_save` | **Ya** |
| Panel admin (verifikasi → Lunas → Dikirim) | `/admin` | **Admin** |
| Contact / Privasi / Syarat | `/contact`, `/privasi`, `/syarat` | – |
| Newsletter | `POST /subscribe` | – |

## Logika login
Middleware `auth` di `routes/web.php` = kotak **"Sudah login?"** dan **"Sesi login masih aktif?"**
di flowchart. Kalau belum login saat klik **Beli** atau **Bayar Sekarang**, pembeli diarahkan ke
`/login`, lalu otomatis kembali ke halaman asal (`redirect()->intended()`).

## Struktur kode penting
- `routes/web.php` — semua route
- `app/Http/Controllers/` — Home, Catalog, Auth, Cart (beli/ongkir/checkout), Order (invoice/pelunasan), Admin/Order
- `app/Models/` — Category, Product, Cart, Order, OrderItem, PaymentConfirmation, Coupon, ShippingRate, Subscriber
- `database/migrations/`, `database/seeders/DatabaseSeeder.php`
- `resources/views/` — Blade (layout, home, catalog, cart, auth, orders, admin, pages)
- `public/css/app.css`, `public/js/app.js`, `public/images/`

## Ganti nama toko / WhatsApp / rekening
Edit `.env`: `APP_NAME`, `STORE_WHATSAPP`, `STORE_BANK`, `STORE_ADDRESS`, `STORE_EMAIL`, `STORE_HOURS`.

Selama `STORE_SAMPLE_DATA=true` (bawaan), web memasang label kuning **Data contoh** di sebelah alamat, WhatsApp, email, dan rekening, supaya pembeli tidak mengira data contoh itu asli. Setelah semua `STORE_*` diisi data toko asli, set `STORE_SAMPLE_DATA=false`, lalu jalankan `php artisan config:clear`.

Arah desain (palet, huruf, dial animasi) dicatat di `DESIGN.md`. Hasil audit antislop ada di folder `anti-slop/`.
Setelah mengubah `.env`, jalankan `php artisan config:clear`.
