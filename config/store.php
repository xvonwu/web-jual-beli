<?php

// STORE_BANK ditulis "BANK NOMOR a.n. NAMA", contoh: "BCA 1234567890 a.n. Pak Har".
// Dipecah supaya nomor rekening bisa ditampilkan & disalin terpisah.
$bank = env('STORE_BANK', 'BCA 1234567890 a.n. Pak Har');
preg_match('/^\s*(\S+)\s+([\d][\d\s.-]*?)\s+a\.?\s*n\.?\s+(.+)$/i', $bank, $m);

return [
    'whatsapp' => env('STORE_WHATSAPP', '6281234567890'),
    'bank' => $bank,
    'bank_name' => $m[1] ?? null,
    'bank_account' => isset($m[2]) ? preg_replace('/\D/', '', $m[2]) : null,
    'bank_holder' => $m[3] ?? null,
    'address' => env('STORE_ADDRESS', 'Jl. Contoh No. 1, Jakarta'),
    'email' => env('STORE_EMAIL', 'cs@pakhar-game.test'),
    'hours' => env('STORE_HOURS', 'Senin - Jumat, 09.00 - 17.00'),

    // true = alamat, WhatsApp, email, dan rekening di atas masih data contoh.
    // Web menampilkan label "Data contoh" di sebelahnya supaya pembeli tidak mengira itu data asli.
    // Isi STORE_* di .env dengan data toko asli, lalu set STORE_SAMPLE_DATA=false.
    'sample' => (bool) env('STORE_SAMPLE_DATA', true),


    // Pilihan "Transfer dari Bank" di form konfirmasi pembayaran
    'banks' => [
        'Bank' => [
            'BCA', 'BRI', 'BNI', 'Mandiri', 'BSI (Bank Syariah Indonesia)', 'CIMB Niaga', 'Permata', 'Danamon',
            'BTN', 'OCBC', 'Maybank', 'Panin', 'Bank Mega', 'Bank DKI', 'Bank BJB', 'Bank Jatim', 'Bank Jateng',
        ],
        'Bank digital' => [
            'SeaBank', 'Bank Jago', 'blu by BCA Digital', 'Jenius', 'Allo Bank', 'Superbank', 'Bank Neo Commerce', 'Krom',
        ],
        'E-wallet' => ['DANA', 'OVO', 'GoPay', 'ShopeePay', 'LinkAja'],
    ],

    // Tarif awal JNE per kg per provinsi tujuan (J&T = JNE + 1.000).
    // Dipakai migration/seeder untuk mengisi tabel shipping_rates; ubah tarif lewat phpMyAdmin.
    'shipping_rates' => [
        'Daerah Khusus Ibukota Jakarta' => 9000,
        'Jawa Barat' => 11000,
        'Banten' => 11000,
        'Jawa Tengah' => 16000,
        'Daerah Istimewa Yogyakarta' => 17000,
        'Jawa Timur' => 18000,
        'Bali' => 24000,
        'Nusa Tenggara Barat' => 26000,
        'Nusa Tenggara Timur' => 34000,
        'Lampung' => 18000,
        'Sumatera Selatan' => 22000,
        'Bengkulu' => 24000,
        'Jambi' => 24000,
        'Kepulauan Bangka Belitung' => 26000,
        'Riau' => 26000,
        'Kepulauan Riau' => 28000,
        'Sumatera Barat' => 26000,
        'Sumatera Utara' => 28000,
        'Aceh' => 32000,
        'Kalimantan Barat' => 30000,
        'Kalimantan Tengah' => 32000,
        'Kalimantan Selatan' => 30000,
        'Kalimantan Timur' => 32000,
        'Kalimantan Utara' => 38000,
        'Sulawesi Selatan' => 32000,
        'Sulawesi Barat' => 36000,
        'Sulawesi Tengah' => 38000,
        'Sulawesi Tenggara' => 38000,
        'Sulawesi Utara' => 40000,
        'Gorontalo' => 40000,
        'Maluku' => 45000,
        'Maluku Utara' => 48000,
        'Papua' => 60000,
        'Papua Barat' => 58000,
        'Papua Barat Daya' => 58000,
        'Papua Selatan' => 65000,
        'Papua Tengah' => 65000,
        'Papua Pegunungan' => 70000,
    ],
];
