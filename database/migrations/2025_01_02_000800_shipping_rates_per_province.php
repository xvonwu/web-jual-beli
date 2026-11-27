<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tarif JNE / J&T sekarang per provinsi (semua kota di Indonesia bisa dihitung). */
    public function up(): void
    {
        DB::table('shipping_rates')->delete();

        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->renameColumn('city', 'province');
        });

        $now = now();
        foreach (config('store.shipping_rates') as $province => $cost) {
            DB::table('shipping_rates')->insert([
                ['courier' => 'jne', 'province' => $province, 'cost' => $cost, 'created_at' => $now, 'updated_at' => $now],
                ['courier' => 'jnt', 'province' => $province, 'cost' => $cost + 1000, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->renameColumn('province', 'city');
        });
    }
};
