<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Nama produk => foto asli (folder public/images/products) */
    private array $photos = [
        'Corsair HS80 RGB Wireless Headset' => 'images/products/corsair-hs80-rgb-wireless-headset.png',
        'DualSense Wireless Controller White' => 'images/products/dualsense-wireless-controller-white.png',
        'DualShock 4 Controller Jet Black' => 'images/products/dualshock-4-controller-jet-black.png',
        'HyperX Cloud III Headset' => 'images/products/hyperx-cloud-iii-headset.png',
        'Logitech G Pro X Superlight Mouse' => 'images/products/logitech-g-pro-x-superlight-mouse.png',
        'Meta Quest 3 512GB VR Headset' => 'images/products/meta-quest-3-512gb-vr-headset.png',
        'Meta Quest 3S 128GB VR Headset' => 'images/products/meta-quest-3s-128gb-vr-headset.png',
        'Mobile Gaming Controller Telescopic' => 'images/products/mobile-gaming-controller-telescopic.png',
        'SteelSeries Apex Pro Keyboard' => 'images/products/steelseries-apex-pro-keyboard.png',
        'XBOX Wireless Controller Carbon Black' => 'images/products/xbox-wireless-controller-carbon-black.png',
        'PS5 007 First Light Game' => 'images/products/ps5-007-first-light-game.jpg',
        'PS5 Crimson Desert Game' => 'images/products/ps5-crimson-desert-game.jpg',
        'PS5 EA Sports FC 26 Game' => 'images/products/ps5-ea-sports-fc-26-game.jpg',
        'PS5 Ghost of Yotei Game' => 'images/products/ps5-ghost-of-yotei-game.jpg',
        "PS5 Marvel's Wolverine Game" => 'images/products/ps5-marvels-wolverine-game.jpg',
        'PS5 Resident Evil Requiem Game' => 'images/products/ps5-resident-evil-requiem-game.jpg',
        'XBOX Forza Horizon 5 Game' => 'images/products/xbox-forza-horizon-5-game.jpg',
        'XBOX Halo Infinite Game' => 'images/products/xbox-halo-infinite-game.jpg',
    ];

    public function up(): void
    {
        foreach ($this->photos as $name => $image) {
            DB::table('products')->where('name', $name)->update(['image' => $image, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // tidak dikembalikan ke ilustrasi
    }
};
