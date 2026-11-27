<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Akun =====
        User::updateOrCreate(['email' => 'admin@pakhar.test'], [
            'name' => 'Admin Pak Har', 'phone' => '081234567890', 'password' => 'password', 'is_admin' => true,
        ]);
        User::updateOrCreate(['email' => 'user@pakhar.test'], [
            'name' => 'Budi Pembeli', 'phone' => '081298765432', 'address' => 'Jl. Melati No. 10', 'password' => 'password',
        ]);

        // ===== Kategori (ID mengikuti flowchart) =====
        $cats = [
            [161, 'PS5', 'Console PS5', 'console-ps5', 'game-termurah-dan-bergaransi-original', 1],
            [162, 'PS5', 'Games PS5', 'games-ps5', 'games-termurah-dan-original', 2],
            [163, 'PS5', 'Accessories PS5', 'accessories-ps5', 'aksesories-games-termurah', 3],
            [43, 'PS4', 'Console PS4', 'console-ps4', 'game-termurah-dan-bergaransi-original', 1],
            [45, 'PS4', 'Games PS4', 'games-ps4', 'games-termurah-dan-original', 2],
            [44, 'PS4', 'Accessories PS4', 'accessories-ps4', 'aksesories-games-termurah', 3],
            [165, 'XBOX', 'Console XBOX', 'console-xbox', 'harga-konsol-games-xbox-termurah-terbaru', 1],
            [166, 'XBOX', 'Games XBOX', 'games-xbox', 'harga-games-xbox-termurah-terbaru', 2],
            [167, 'XBOX', 'Accessories XBOX', 'accessories-xbox', 'harga-aksesories-game-xbox-terbaru-murah', 3],
            [35, 'Others', 'VR Oculus', 'vr-oculus', 'harga-oculus-vr-murah-terbaru', 1],
            [157, 'Others', 'Logitech', 'logitech', 'harga-logitech-gaming-murah-terbaru', 2],
            [158, 'Others', 'SteelSeries', 'steelseries', 'harga-steel-series-gaming-murah-terbaru', 3],
            [172, 'Others', 'Corsair', 'corsair', 'harga-corsair-gaming-murah-terbaru', 4],
            [173, 'Others', 'HyperX', 'hyperx', 'harga-hyper-x-gaming-murah-terbaru', 5],
            [32, 'Others', 'Mobile Gaming', 'mobile-gaming', 'harga-aksesories-mobile-gaming-terbaru', 6],
        ];
        foreach ($cats as [$id, $platform, $name, $slug, $seo, $sort]) {
            Category::updateOrCreate(['id' => $id], compact('platform', 'name', 'slug', 'sort') + ['seo_slug' => $seo]);
        }

        // ===== Produk =====
        // [kategori, nama, harga, harga_coret, stok, region, gambar, new, preorder, promo, rilis]
        $products = [
            [161, 'PS5 Slim Disc Edition Console', 8499000, 8999000, 5, 'R3', 'images/products/ps5-real.png', 1, 0, 1, '2023-11-10'],
            [161, 'PS5 Slim Digital Edition Console', 7299000, null, 3, 'R3', 'images/products/ps5-real.png', 0, 0, 0, '2023-11-10'],
            [161, 'PS5 Pro Console 2TB', 12999000, null, 0, 'R3', 'images/products/ps5-real.png', 1, 1, 0, '2024-11-07'],
            [162, 'PS5 Marvel\'s Wolverine Game', 999000, null, 0, 'R3', 'cover', 1, 1, 0, '2026-09-15'],
            [162, 'PS5 007 First Light Game', 899000, null, 12, 'R3', 'cover', 1, 0, 0, '2026-03-27'],
            [162, 'PS5 Resident Evil Requiem Game', 879000, 929000, 8, 'R3', 'cover', 1, 0, 1, '2026-02-27'],
            [162, 'PS5 God of War Ragnarok Game', 549000, 799000, 10, 'R3', 'cover', 0, 0, 1, '2022-11-09'],
            [162, 'PS5 EA Sports FC 26 Game', 749000, null, 15, 'R3', 'cover', 0, 0, 0, '2025-09-26'],
            [162, 'PS5 Crimson Desert Game', 879000, null, 0, 'R3', 'cover', 0, 0, 0, '2026-03-19'],
            [162, 'PS5 Ghost of Yotei Game', 899000, null, 6, 'R3', 'cover', 1, 0, 0, '2025-10-02'],
            [163, 'DualSense Wireless Controller White', 1049000, 1149000, 20, null, 'images/products/controller-white.svg', 0, 0, 1, '2020-11-12'],
            [163, 'DualSense Edge Pro Controller', 3299000, null, 4, null, 'images/products/dualsense-edge.png', 1, 0, 0, '2023-01-26'],
            [163, 'PULSE Elite Wireless Headset', 2199000, null, 7, null, 'images/products/pulse-elite.png', 0, 0, 0, '2024-02-21'],
            [163, 'DualSense Wireless Controller Black', 1149000, null, 10, null, 'images/products/dualsense-black.png', 1, 0, 0, '2024-05-01'],
            [43, 'PS4 Slim 1TB Console', 4599000, null, 2, 'R3', 'images/products/ps4-slim.png', 0, 0, 0, '2016-09-15'],
            [45, 'PS4 The Last of Us Remastered Game', 249000, 399000, 9, 'R3', 'images/products/ps4-the-last-of-us-remastered.jpg', 0, 0, 1, '2014-07-29'],
            [45, 'PS4 Gran Turismo Sport Game', 199000, 299000, 0, 'R3', 'images/products/ps4-gran-turismo-sport.jpg', 0, 0, 1, '2017-10-17'],
            [45, 'PS4 Spider-Man Game of The Year', 349000, null, 5, 'R3', 'images/products/ps4-spider-man-goty.jpg', 0, 0, 0, '2019-09-17'],
            [44, 'DualShock 4 Controller Jet Black', 749000, null, 11, null, 'images/products/controller-black.svg', 0, 0, 0, '2016-09-15'],
            [165, 'XBOX Series X 1TB Console', 8999000, null, 3, 'Asia', 'images/products/xbox-series-x.png', 0, 0, 0, '2020-11-10'],
            [165, 'XBOX Series S 512GB Console', 4999000, 5499000, 6, 'Asia', 'images/products/xbox-series-s.png', 0, 0, 1, '2020-11-10'],
            [166, 'XBOX Forza Horizon 5 Game', 499000, null, 8, 'Asia', 'cover', 0, 0, 0, '2021-11-09'],
            [166, 'XBOX Halo Infinite Game', 399000, 599000, 4, 'Asia', 'cover', 0, 0, 1, '2021-12-08'],
            [167, 'XBOX Wireless Controller Carbon Black', 899000, null, 10, null, 'images/products/controller-xbox.svg', 0, 0, 0, '2020-11-10'],
            [35, 'Meta Quest 3 512GB VR Headset', 9499000, null, 3, null, 'images/products/vr.svg', 1, 0, 0, '2023-10-10'],
            [35, 'Meta Quest 3S 128GB VR Headset', 5499000, 5999000, 5, null, 'images/products/vr.svg', 0, 0, 1, '2024-10-15'],
            [157, 'Logitech G Pro X Superlight Mouse', 1899000, null, 9, null, 'images/products/mouse.svg', 0, 0, 0, '2023-09-20'],
            [158, 'SteelSeries Apex Pro Keyboard', 3199000, null, 2, null, 'images/products/keyboard.svg', 0, 0, 0, '2023-03-01'],
            [172, 'Corsair HS80 RGB Wireless Headset', 1999000, 2299000, 6, null, 'images/products/headset.svg', 0, 0, 1, '2021-06-01'],
            [173, 'HyperX Cloud III Headset', 1399000, null, 14, null, 'images/products/headset.svg', 0, 0, 0, '2023-08-01'],
            [32, 'Mobile Gaming Controller Telescopic', 459000, null, 18, null, 'images/products/mobile.svg', 0, 0, 0, '2024-05-01'],
        ];

        // Foto asli produk (public/images/products)
        $photos = [
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
            'PS5 God of War Ragnarok Game' => 'images/products/ps5-god-of-war-ragnarok-game.jpg',
            'XBOX Forza Horizon 5 Game' => 'images/products/xbox-forza-horizon-5-game.jpg',
            'XBOX Halo Infinite Game' => 'images/products/xbox-halo-infinite-game.jpg',
        ];

        foreach ($products as $i => [$cat, $name, $price, $old, $stock, $region, $img, $new, $pre, $promo, $rilis]) {
            $slug = Str::slug($name);
            if (isset($photos[$name])) {
                $img = $photos[$name];
            }
            Product::updateOrCreate(['slug' => $slug], [
                'category_id' => $cat,
                'name' => $name,
                'price' => $price,
                'old_price' => $old,
                'stock' => $stock,
                'region' => $region,
                'weight' => str_contains($name, 'Console') ? 4.5 : 0.25,
                'release_date' => $rilis,
                'image' => $img === 'cover' ? 'images/covers/'.$slug.'.svg' : $img,
                'is_new_release' => (bool) $new,
                'is_preorder' => (bool) $pre,
                'is_promo' => (bool) $promo,
                'description' => "{$name} original dan bergaransi resmi. Barang dijamin baru, segel, dan siap dikirim dari gudang kami. "
                    .'Hubungi admin via WhatsApp untuk informasi stok dan bundling.',
            ]);
        }

        // ===== Tarif ongkir JNE / J&T per provinsi (per kg) =====
        foreach (config('store.shipping_rates') as $province => $cost) {
            ShippingRate::updateOrCreate(['courier' => 'jne', 'province' => $province], ['cost' => $cost]);
            ShippingRate::updateOrCreate(['courier' => 'jnt', 'province' => $province], ['cost' => $cost + 1000]);
        }

        // ===== Kupon =====
        Coupon::updateOrCreate(['code' => 'HEMAT10'], ['type' => 'percent', 'value' => 10, 'min_total' => 300000]);
        Coupon::updateOrCreate(['code' => 'DISKON50K'], ['type' => 'fixed', 'value' => 50000, 'min_total' => 500000]);
    }
}
