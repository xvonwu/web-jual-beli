<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category');

        $products = Product::with('category')
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%'.$q.'%'))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('platform')->orderBy('sort')->get()->groupBy('platform');

        return view('admin.products.index', compact('products', 'categories', 'q', 'categoryId'));
    }

    public function create()
    {
        $categories = Category::orderBy('platform')->orderBy('sort')->get()->groupBy('platform');

        return view('admin.products.form', [
            'product' => new Product(['weight' => 0.25, 'stock' => 1]),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request, isCreate: true);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['image'] = $this->storeImage($request->file('image'), $data['slug']);

        $product = Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', "Produk \"{$product->name}\" berhasil diupload.");
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('platform')->orderBy('sort')->get()->groupBy('platform');

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, isCreate: false);

        if ($product->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request->file('image'), $product->slug ?: Str::slug($data['name']));
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', "Produk \"{$product->name}\" berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $image = $product->image;

        $product->delete();

        if ($image && ! str_starts_with($image, 'http') && $image !== 'images/products/placeholder.svg') {
            $stillUsed = Product::where('image', $image)->exists();
            $file = public_path($image);
            if (! $stillUsed && is_file($file)) {
                @unlink($file);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('popup', [
                'type' => 'deleted',
                'title' => 'Produk berhasil dihapus!',
                'message' => "Produk \"{$name}\" telah dihapus dari daftar toko.",
            ]);
    }

    private function validateProduct(Request $request, bool $isCreate): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:180'],
            'price' => ['required', 'integer', 'min:1000'],
            'old_price' => ['nullable', 'integer', 'gt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'region' => ['nullable', 'string', 'max:20'],
            'release_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:3000'],
            'image' => [$isCreate ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'category_id.required' => 'Pilih kategori produk.',
            'name.required' => 'Isi nama produk.',
            'price.required' => 'Isi harga jual produk.',
            'price.min' => 'Harga minimal Rp 1.000.',
            'old_price.gt' => 'Harga coret harus lebih besar dari harga jual.',
            'stock.required' => 'Isi jumlah stok (isi 0 jika Sold Out / Preorder).',
            'weight.required' => 'Isi berat produk dalam kg.',
            'image.required' => 'Upload gambar produk terlebih dahulu.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung: JPG, JPEG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 4 MB.',
        ]);

        $validated['is_new_release'] = $request->boolean('is_new_release');
        $validated['is_preorder'] = $request->boolean('is_preorder');
        $validated['is_promo'] = $request->boolean('is_promo') || ! empty($validated['old_price']);

        unset($validated['image']);

        return $validated;
    }

    private function storeImage(\Illuminate\Http\UploadedFile $file, string $slug): string
    {
        $dir = public_path('images/products');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = $slug.'-'.time().'-'.Str::lower(Str::random(4)).'.'.$ext;
        $file->move($dir, $filename);

        return 'images/products/'.$filename;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $i = 2;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
