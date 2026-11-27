<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Alamat dipecah: provinsi, kota, kabupaten/kecamatan, kode pos + deskripsi lengkap (kolom address). */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('province')->nullable()->after('address');
            $table->string('district')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['province', 'district']);
        });
    }
};
