<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'expires_at' => 'date'];
    }

    /** Kembalikan coupon valid atau null. */
    public static function findValid(?string $code, int $subtotal): ?self
    {
        if (! $code) {
            return null;
        }

        $coupon = static::where('code', strtoupper(trim($code)))->where('is_active', true)->first();

        if (
            ! $coupon
            || ($coupon->expires_at && $coupon->expires_at->isPast())
            || $subtotal < $coupon->min_total
            || ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses)
        ) {
            return null;
        }

        return $coupon;
    }

    public function discountFor(int $subtotal): int
    {
        $d = $this->type === 'percent' ? intdiv($subtotal * $this->value, 100) : $this->value;

        return min($d, $subtotal);
    }
}
