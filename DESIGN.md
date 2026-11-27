# DESIGN.md: NextGen

Arah desain web toko game NextGen. Dibaca sebelum mengubah tampilan, lalu dicek dengan aturan antislop.

## Identitas

- **Apa ini:** toko game dan konsol PS5, PS4, Xbox, dan aksesoris. Pembeli datang untuk cek harga dan stok, lalu beli atau ambil di toko.
- **Kesan yang dituju:** rak toko game fisik yang ramai tapi rapi. Bukan e-commerce generik, bukan situs "gamer" gelap penuh neon.
- **Motif identitas:** stiker harga kuning yang miring sedikit, seperti label harga yang ditempel tangan di kotak game. Dipakai di harga produk, harga hero, tanggal rilis preorder, dan badge keranjang.
- **Tekstur:** titik halftone kecil, diambil dari grille ventilasi konsol dan touchpad DualSense. Hanya di panel biru, thumbnail produk, dan band gelap.

## Palet

| Nama | Hex | Peran |
|---|---|---|
| Cobalt | `#2340ff` | Warna utama: hero, header halaman, tombol utama. Diambil dari light-bar DualSense. |
| Ink | `#121633` | Teks utama, band gelap, footer. |
| Stiker | `#ffd400` | Satu-satunya aksen: stiker harga, tanggal rilis, focus ring. |
| Signal | `#f2384a` / teks `#cf1f36` | Diskon, hapus, error. Versi teks dipakai supaya kontras 4,5:1. |
| Cloud | `#eef1f8` | Latar thumbnail dan rak. |
| Muted | `#5d6382` | Teks sekunder (minimal 4,5:1 di atas putih dan cloud). |

Warna platform (PS4 `#0b2a8a`, Xbox `#107c10`, VR `#6b3df0`) **hanya** untuk garis atas rak platform di beranda, supaya tiap rak langsung dikenali. Jangan dipakai di elemen lain.

## Tipografi

- **Archivo** satu keluarga saja. Alasannya: punya sumbu lebar (width), jadi judul bisa dibuat expanded + miring yang senada dengan logo NextGen yang miring, sementara teks isi tetap lebar normal dan mudah dibaca.
- Judul: `font-stretch: 125%`, italic, 800. Teks isi: 17-19 px, lebar normal.
- Tidak ada huruf kapital semua untuk label, kecuali kata hiasan besar bergaris (ghost text) yang disembunyikan dari pembaca layar.

## Tata letak

- Beranda: hero biru (satu titik fokus: "PS5 ready.") → rak per platform → info pengiriman (pengiriman paling besar karena paling sering ditanya) → preorder sebagai daftar baris dengan tanggal rilis → grid produk baru → grid produk yang baru berubah harga/stok.
- Radius: 14 px panel besar, 10 px kartu, 6 px tombol/input, 4 px stiker. Tidak ada elemen berbentuk pil.
- Bayangan hanya untuk benda yang "menempel": stiker, dropdown, popup.

## Dial

`Dial: ENERGY 3 / RHYTHM 2 / MOTION 2`

- **ENERGY 3:** warna penuh, judul besar miring, stiker miring. Toko game boleh terasa ramai.
- **RHYTHM 2:** section konsisten (garis bawah tebal di judul section) dengan beberapa jeda: rak platform, band info biru/gelap, band preorder gelap.
- **MOTION 2:** satu momen saja: judul muncul per kata saat di-scroll (GSAP SplitText + ScrollTrigger) dan stiker hero menempel saat halaman dibuka. Hover hanya geser kecil. Semua mati saat "kurangi animasi" aktif.

## Bahasa

Bahasa Indonesia untuk semua teks antarmuka dan navigasi. Nama produk dan kategori mengikuti data toko.
