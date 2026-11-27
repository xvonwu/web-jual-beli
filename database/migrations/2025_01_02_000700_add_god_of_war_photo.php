<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')->where('name', 'PS5 God of War Ragnarok Game')
            ->update(['image' => 'images/products/ps5-god-of-war-ragnarok-game.jpg', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('products')->where('name', 'PS5 God of War Ragnarok Game')
            ->update(['image' => 'images/covers/ps5-god-of-war-ragnarok-game.svg']);
    }
};
