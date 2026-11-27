<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    // ID metode pembayaran mengikuti flowchart
    public const PAYMENT_TRANSFER = 42;
    public const PAYMENT_CASH = 54;

    public const PAYMENT_METHODS = [
        self::PAYMENT_TRANSFER => 'Transfer ke BCA',
        self::PAYMENT_CASH => 'Cash di kantor (khusus Ambil Langsung)',
    ];

    public const COURIERS = [
        'pickup' => 'Ambil Langsung',
        'gojek' => 'GOJEK',
        'grab' => 'Grab',
        'jne' => 'JNE',
        'jnt' => 'J&T',
    ];

    public const STATUSES = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'lunas' => 'Lunas',
        'dikirim' => 'Dikirim / Siap Diambil',
        'selesai' => 'Selesai',
        'batal' => 'Dibatalkan',
    ];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'payment_method_id' => 'integer', 'user_id' => 'integer'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function confirmations()
    {
        return $this->hasMany(PaymentConfirmation::class);
    }

    /** Deskripsi alamat, Kec., Kota, Provinsi Kode pos */
    public function fullAddress(): ?string
    {
        if (! $this->address) {
            return null;
        }

        $parts = array_filter([
            $this->address,
            $this->district,
            $this->city,
            trim(($this->province ?? '').' '.($this->zip ?? '')),
        ]);

        return implode(', ', $parts);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method_id] ?? '-';
    }

    public function courierLabel(): string
    {
        return self::COURIERS[$this->courier] ?? $this->courier;
    }

    public static function makeInvoice(): string
    {
        do {
            $inv = 'INV'.now()->format('ymd').strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (static::where('invoice', $inv)->exists());

        return $inv;
    }
}
