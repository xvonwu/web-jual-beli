<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Rak platform: kategori + jumlah produk per platform
        $shelves = Category::withCount('products')->orderBy('sort')->get()->groupBy('platform');

        return view('home', [
            'newReleases' => Product::with('category')->where('is_new_release', true)->latest('release_date')->take(8)->get(),
            'latest' => Product::with('category')->latest('updated_at')->take(8)->get(),
            'preorders' => Product::with('category')->where('is_preorder', true)->take(4)->get(),
            'cat' => Category::all()->keyBy('id'),
            'shelves' => $shelves,
            'ps5From' => Product::where('category_id', 161)->where('stock', '>', 0)->min('price'),
        ]);
    }
}
