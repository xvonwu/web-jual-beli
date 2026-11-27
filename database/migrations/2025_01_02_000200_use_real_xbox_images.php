<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Ganti gambar konsol XBOX dengan foto asli Series X / Series S. */
    public function up(): void
    {
        DB::table('products')->where('name', 'like', '%Series X%')
            ->update(['image' => 'images/products/xbox-series-x.png']);
        DB::table('products')->where('name', 'like', '%Series S%')
            ->update(['image' => 'images/products/xbox-series-s.png']);
    }

    public function down(): void
    {
        DB::table('products')->whereIn('image', ['images/products/xbox-series-x.png', 'images/products/xbox-series-s.png'])
            ->update(['image' => 'images/products/console-xbox.svg']);
    }
};
