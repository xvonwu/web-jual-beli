<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Ganti gambar semua produk konsol PS5 dengan foto asli. */
    public function up(): void
    {
        DB::table('products')
            ->where('image', 'images/products/console-ps5.svg')
            ->update(['image' => 'images/products/ps5-real.png']);
    }

    public function down(): void
    {
        DB::table('products')
            ->where('image', 'images/products/ps5-real.png')
            ->update(['image' => 'images/products/console-ps5.svg']);
    }
};
