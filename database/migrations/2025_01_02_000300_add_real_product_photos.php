<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /** Nama produk => foto asli (folder public/images/products) */
    private array $photos = [
        'DualSense Edge Pro Controller' => 'images/products/dualsense-edge.png',
        'PULSE Elite Wireless Headset' => 'images/products/pulse-elite.png',
        'PS4 Slim 1TB Console' => 'images/products/ps4-slim.png',
        'PS4 Gran Turismo Sport Game' => 'images/products/ps4-gran-turismo-sport.jpg',
        'PS4 Spider-Man Game of The Year' => 'images/products/ps4-spider-man-goty.jpg',
        'PS4 The Last of Us Remastered Game' => 'images/products/ps4-the-last-of-us-remastered.jpg',
    ];

    public function up(): void
    {
        foreach ($this->photos as $name => $image) {
            DB::table('products')->where('name', $name)->update(['image' => $image, 'updated_at' => now()]);
        }

        // Produk baru: DualSense Black (Accessories PS5, kategori 163)
        $name = 'DualSense Wireless Controller Black';
        // Lewati saat instalasi baru (kategori belum ada) - produk ini dibuat oleh seeder
        if (DB::table('categories')->where('id', 163)->exists()
            && ! DB::table('products')->where('name', $name)->exists()) {
            DB::table('products')->insert([
                'category_id' => 163,
                'name' => $name,
                'slug' => Str::slug($name),
                'price' => 1149000,
                'stock' => 10,
                'weight' => 0.25,
                'release_date' => '2024-05-01',
                'image' => 'images/products/dualsense-black.png',
                'is_new_release' => true,
                'is_preorder' => false,
                'is_promo' => false,
                'description' => "{$name} original dan bergaransi resmi. Barang dijamin baru, segel, dan siap dikirim dari gudang kami. "
                    .'Hubungi admin via WhatsApp untuk informasi stok dan bundling.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('products')->where('name', 'DualSense Wireless Controller Black')->delete();
    }
};
