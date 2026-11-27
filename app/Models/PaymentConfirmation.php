<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfirmation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['tgl_transfer' => 'datetime'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
