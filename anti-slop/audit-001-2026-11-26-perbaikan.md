# Laporan perbaikan audit 001 (2026-11-26)

Yang disetujui: semua nomor (1-16). Semua sudah diperbaiki.

## Yang diubah

| No | Temuan | Perbaikan |
|---|---|---|
| 1 | Halaman melebar ke samping di HP | Header HP dipadatkan (logo 56 px, ikon 44 px). Tabel keranjang dan Pesanan Saya berubah jadi kartu per baris di HP. Info kontak jadi satu kolom. Judul halaman memakai ukuran fluid (`clamp`). Tabel admin dan invoice digeser di dalam kartunya sendiri. |
| 2 | Tombol terlalu kecil | Ikon header 48 px (44 px di HP), tombol kecil minimal 44 px, link footer, mega menu, "Semua produk", dan breadcrumb diberi area sentuh 44 px. |
| 3 | Kontras kurang | `--muted` jadi #5d6382 (5,9:1 di putih, 5,2:1 di cloud). Teks/label merah memakai `--signal-text` #cf1f36 (5,4:1). Breadcrumb di panel biru jadi 92% putih. |
| 4 | Menu Belanja tidak bisa lewat keyboard | "Shop" jadi tombol "Belanja" dengan `aria-expanded`. Enter membuka, Tab masuk ke daftar kategori, Esc menutup dan fokus kembali ke tombol. |
| 5 | Tombol menu tanpa nama | Burger: `aria-label="Menu"`, `aria-expanded`, `aria-controls`. Tombol Akun, Cari, dan Keranjang juga diberi nama. Semua ikon SVG `aria-hidden`. |
| 6 | Input tanpa label | Pencarian header, pencarian di halaman hasil, dan newsletter punya label yang terlihat. Semua label form login, daftar, keranjang, dan konfirmasi bayar sekarang terhubung ke kolomnya (`for`/`id`). |
| 7 | Data toko contoh terlihat asli | Alamat, email, dan jam pindah ke `.env` (`STORE_ADDRESS`, `STORE_EMAIL`, `STORE_HOURS`). Selama `STORE_SAMPLE_DATA=true`, label kuning "Data contoh" muncul di footer, Kontak, keranjang, konfirmasi bayar, dan invoice. |
| 8 | Klaim garansi resmi | Diganti jadi "Original, masih segel" dan "garansi sesuai ketentuan distributor" (sama dengan Syarat & Ketentuan). |
| 9 | Tanda pisah panjang | Judul tab memakai "|", daftar invoice memakai kurung, detail konfirmasi memakai koma, judul admin memakai titik dua. |
| 10 | State kosong/gagal | Home kosong: pesan untuk pembeli dan tombol "Lihat semua produk". Pencarian kosong: saran kata kunci. Pesanan kosong: penjelasan dan tombol "Mulai belanja". Dropdown wilayah: pesan gagal dan tombol "coba lagi". |
| 11 | Gambar tanpa ukuran | Semua `<img>` punya `width`/`height` dari ukuran file asli (helper `App\Support\Img`, `Product::imageAttrs()`). Gambar hero dan gambar detail produk memakai `fetchpriority="high"`. |
| 12 | Belum ada DESIGN.md | `DESIGN.md` dibuat: identitas, palet, huruf, tata letak, bahasa, dan dial ENERGY 3 / RHYTHM 2 / MOTION 2. |
| 13 | Empat kotak info identik | Pengiriman jadi blok biru besar dengan daftar kurir (pertanyaan paling sering). Tiga info lain jadi daftar teks pendek tanpa ikon. |
| 14 | Tiga section produk sama | Preorder jadi band gelap berisi daftar baris dengan stiker tanggal rilis di depan. Dua section lain tetap grid produk. |
| 15 | Bahasa campur | Navigasi dan breadcrumb jadi Bahasa Indonesia: Beranda, Belanja, Kontak, Keranjang, Konfirmasi Pembayaran, "Hak cipta dilindungi". Kata hiasan besar di header halaman disembunyikan dari pembaca layar. Nama kategori dari database tidak diubah. |
| 16 | Palet 6 warna | Dicek: warna PS4, Xbox, dan VR hanya dipakai di garis atas rak. Aturannya dicatat di CSS dan DESIGN.md. |

Tambahan kecil yang ditemukan saat memperbaiki: level judul yang melompat (h1 langsung ke h3/h5/h6) dirapikan jadi h2, kursor tangan dipasang sekali untuk semua kontrol, dan judul langkah di keranjang tidak lagi kehilangan spasi antar kata.

## Delivery Gate

### Block 1: Hard Gate

- R-02 PASS: tidak ada `—` di teks antarmuka (grep views, JS, controller, config).
- R-03 PASS: 7 halaman di 375, 768, dan 1280 px tidak bisa digeser ke samping (`scrollWidth - innerWidth = 0`). Semua ikon dan tombol minimal 44 px. Sisa target di bawah 44 px hanya link nama produk di kartu, yang tujuannya sama dengan foto dan tombol Detail di kartu yang sama.
- R-17, R-18, R-28 PASS: tidak ada statistik, testimoni, atau FAQ buatan.
- R-23, R-38 PASS: data toko contoh diberi label "Data contoh". Logo dan foto produk dari user.
- R-24, R-26 PASS: semua link nav punya halaman. Belanja, burger, Cari, dan Akun dicek bisa membuka dan menutup.
- R-25 PASS: tidak ada teks di bawah 4,5:1 (3:1 untuk teks besar) di 21 kombinasi halaman dan lebar. Kuning di atas cobalt 4,52:1 hanya dipakai untuk teks besar.
- R-27 PASS: state kosong (home, pencarian, keranjang, pesanan, konfirmasi bayar) dan state gagal (wilayah) menyebut penyebab dan langkah berikutnya. File wilayah sengaja diblokir saat tes, dan pesan "Daftar kota gagal dimuat... coba lagi" muncul.
- R-32 PASS: urutan Tab adalah logo, Beranda, Belanja, Promo, dan seterusnya. Enter membuka Belanja, Tab masuk ke "Console PS5", Esc menutup dan fokus kembali. Focus ring kuning 3 px dengan bingkai gelap.
- R-33 PASS: semua perubahan langsung di source.
- R-34 PASS: tidak ada pengganti tema (satu tema terang).
- R-35 PASS: Blade dikompilasi, E2E "ALL PASS" dan "ALL PASS 2", tanpa error JS di console (satu-satunya error adalah Google Fonts yang tidak bisa dimuat di lingkungan uji tanpa internet). Klik-tes: Belanja → mega terbuka; Esc → tertutup; Cari → kolom cari dan label tampil; burger HP → menu terbuka; Belanja di HP → kategori tampil.
- R-36 PASS: klaim garansi disesuaikan dengan Syarat & Ketentuan.
- R-37 PASS: DESIGN.md ada beserta dial.
- R-39 PASS: kontrol punya nama, label terlihat dan terhubung, `lang="id"`, landmark, satu h1, level judul berurutan, animasi mati saat "kurangi animasi".
- R-40 PASS: semua gambar punya ukuran, hero `fetchpriority="high"` dan tidak lazy, tidak ada script pihak ketiga di `<head>`. Skor performa tidak diukur.

### Block 2: Purpose-Gate

- R-01 PASS: satu gradient (panel biru) untuk membedakan header halaman, alasan tertulis di DESIGN.md.
- R-04 PASS: ikon hanya untuk aksi (akun, cari, keranjang, hapus) dan blok pengiriman (truk).
- R-06 PASS: Archivo, alasan tertulis (sumbu lebar senada logo miring).
- R-07 PASS: halftone, alasan tertulis.
- R-08, R-09, R-10, R-13 PASS: tidak ada panah dekorasi, badge AI, glassmorphism, atau glow.
- R-12 PASS: bayangan hanya untuk stiker, dropdown, dan popup.
- R-14 PASS: info toko punya hierarki (pengiriman menonjol).
- R-19 PASS: satu momen animasi (judul per kata dan stiker hero), sesuai MOTION 2.
- R-22 PASS: tidak ada ilustrasi generik.

### Block 3: Liveliness

- Dial dinyatakan: ENERGY 3 / RHYTHM 2 / MOTION 2. Section punya jeda (rak, blok biru/gelap, band preorder), sesuai RHYTHM 2.
- Titik fokus: hero "PS5 ready." di beranda, judul di halaman lain.
- Satu aksen: kuning stiker. Motif: stiker harga miring, kini juga di tanggal rilis.

### Block 4: Craftsmanship & Quality Locks

- C-1 sampai C-5 PASS.
- R-05 PASS: section beranda tidak lagi berpola sama.
- R-11 PASS: radius 14/10/6/4, tanpa bentuk pil.
- R-15, R-16 PASS: CTA spesifik, tanpa buzzword.
- R-20, R-29, R-30, R-31 PASS: palet 3 inti dan 1 aksen, warna platform terbatas di rak, alasan tiap keputusan ada di DESIGN.md.
- R-21 PASS: tema terang dipilih karena toko ritel berbasis foto produk berlatar terang.

## Yang perlu dilakukan pemilik toko

- Isi `STORE_ADDRESS`, `STORE_WHATSAPP`, `STORE_EMAIL`, `STORE_BANK`, dan `STORE_HOURS` di `.env` dengan data asli, set `STORE_SAMPLE_DATA=false`, lalu jalankan `php artisan config:clear`.
- Kalau toko memang penjual resmi Sony, kalimat garansi boleh dikembalikan ke "garansi resmi Sony Indonesia".
