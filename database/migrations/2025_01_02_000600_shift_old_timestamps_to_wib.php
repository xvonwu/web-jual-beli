<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sebelumnya aplikasi memakai zona waktu UTC. Sekarang Asia/Jakarta (WIB),
     * jadi waktu pesanan lama digeser +7 jam supaya tampil sesuai jam WIB.
     */
    public function up(): void
    {
        $tables = [
            'orders' => ['created_at', 'updated_at', 'paid_at'],
            'order_items' => ['created_at', 'updated_at'],
            'payment_confirmations' => ['created_at', 'updated_at'],
        ];

        foreach ($tables as $table => $columns) {
            foreach (DB::table($table)->get() as $row) {
                $update = [];
                foreach ($columns as $col) {
                    if ($row->$col) {
                        $update[$col] = Carbon::parse($row->$col)->addHours(7)->format('Y-m-d H:i:s');
                    }
                }
                if ($update) {
                    DB::table($table)->where('id', $row->id)->update($update);
                }
            }
        }
    }

    public function down(): void
    {
        //
    }
};
