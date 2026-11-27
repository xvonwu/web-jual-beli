<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** tgl_transfer sekarang diisi otomatis (tanggal + jam) saat konfirmasi dikirim. */
    public function up(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $table) {
            $table->dateTime('tgl_transfer')->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $table) {
            $table->date('tgl_transfer')->change();
        });
    }
};
