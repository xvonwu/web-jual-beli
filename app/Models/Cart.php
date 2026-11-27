<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['user_id' => 'integer', 'qty' => 'integer'];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subtotal(): int
    {
        return $this->qty * $this->product->price;
    }
}
