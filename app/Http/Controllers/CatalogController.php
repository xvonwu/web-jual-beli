<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public const PER_PAGE = 12;

    /** /harga/{platform}/{category}/{page}/{seo} */
    public function category(string $platform, Category $category, int $page = 1)
    {
        $products = $category->products()->with('category')
            ->orderByRaw('stock = 0 and is_preorder = 0') // yang ada stok di atas
            ->latest('release_date')
            ->paginate(self::PER_PAGE, ['*'], 'page', max($page, 1));

        return view('catalog.list', [
            'title' => $category->platform.' / '.$category->name,
            'breadcrumb' => [$category->platform, $category->name],
            'products' => $products,
            'pageUrl' => fn (int $p) => $category->url($p),
        ]);
    }

    /** /jual/{id}/{slug} */
    public function product(Product $product)
    {
        $related = Product::with('category')->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)->inRandomOrder()->take(4)->get();

        return view('catalog.show', compact('product', 'related'));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $products = Product::with('category')->search($q)->latest('release_date')
            ->paginate(self::PER_PAGE)->withQueryString();

        return view('catalog.list', [
            'title' => $q !== '' ? 'Hasil pencarian "'.$q.'"' : 'Semua Produk',
            'breadcrumb' => ['Pencarian'],
            'products' => $products,
            'pageUrl' => fn (int $p) => $products->url($p),
            'q' => $q,
        ]);
    }

    public function promo()
    {
        $products = Product::with('category')->where('is_promo', true)->latest('release_date')->paginate(self::PER_PAGE);

        return view('catalog.list', [
            'title' => 'Promo Super Murah',
            'breadcrumb' => ['Promo'],
            'products' => $products,
            'pageUrl' => fn (int $p) => $products->url($p),
        ]);
    }

    /** Preorder memakai flag is_preorder di database (bukan kata kunci pencarian). */
    public function preorder(?string $platform = null)
    {
        $products = Product::with('category')->where('is_preorder', true)
            ->when($platform, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('platform', strtoupper($platform))))
            ->paginate(self::PER_PAGE);

        return view('catalog.list', [
            'title' => 'Preorder '.strtoupper((string) $platform),
            'breadcrumb' => ['Preorder'],
            'products' => $products,
            'pageUrl' => fn (int $p) => $products->url($p),
        ]);
    }
}
