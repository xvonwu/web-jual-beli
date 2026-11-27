<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Aman untuk MySQL/MariaDB versi lama di XAMPP
        Schema::defaultStringLength(191);

        // Nama hari/bulan dalam bahasa Indonesia (Senin, November, ...)
        \Carbon\Carbon::setLocale(config('app.locale'));

        // @rupiah($angka) -> Rp 1.250.000
        Blade::directive('rupiah', fn ($e) => "<?php echo 'Rp '.number_format((int) ($e), 0, ',', '.'); ?>");

        // Data menu & badge keranjang untuk semua halaman yang memakai layout
        View::composer('layouts.app', function ($view) {
            $grouped = Category::orderBy('sort')->get()->groupBy('platform');
            // urutan kolom menu: PS5, PS4, XBOX, Others
            $menu = collect(Category::PLATFORMS)->filter(fn ($p) => $grouped->has($p))->mapWithKeys(fn ($p) => [$p => $grouped[$p]]);
            $cartCount = 0;
            $cartTotal = 0;

            if ($user = auth()->user()) {
                $items = Cart::with('product')->where('user_id', $user->id)->get();
                $cartCount = (int) $items->sum('qty');
                $cartTotal = (int) $items->sum(fn ($c) => $c->subtotal());
            }

            $view->with(compact('menu', 'cartCount', 'cartTotal'));
        });
    }
}
