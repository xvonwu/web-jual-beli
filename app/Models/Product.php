<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'is_new_release' => 'boolean',
            'is_preorder' => 'boolean',
            'is_promo' => 'boolean',
            'weight' => 'float',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isSoldOut(): bool
    {
        return $this->stock < 1 && ! $this->is_preorder;
    }

    public function statusLabel(): string
    {
        if ($this->is_preorder) {
            return 'Preorder';
        }

        return $this->stock > 0 ? 'Ready Stock' : 'Sold Out';
    }

    /** URL pola flowchart: /jual/{id}/{slug} */
    public function url(): string
    {
        return route('product.show', ['product' => $this->id, 'slug' => $this->slug]);
    }

    /** width="…" height="…" dari ukuran asli gambar produk */
    public function imageAttrs(): string
    {
        return \App\Support\Img::attrs($this->image ?: 'images/products/placeholder.svg');
    }

    public function imageUrl(): string
    {
        if (! $this->image) {
            return asset('images/products/placeholder.svg');
        }

        return str_starts_with($this->image, 'http') ? $this->image : asset($this->image);
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        return $term ? $q->where('name', 'like', '%'.$term.'%') : $q;
    }
}
