<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public const PLATFORMS = ['PS5', 'PS4', 'XBOX', 'Others'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /** URL pola flowchart: /harga/{platform}/{id}/{page}/{seo} */
    public function url(int $page = 1): string
    {
        return route('category.show', [
            'platform' => strtolower($this->platform),
            'category' => $this->id,
            'page' => $page,
            'seo' => $this->seo_slug ?: $this->slug,
        ]);
    }
}
