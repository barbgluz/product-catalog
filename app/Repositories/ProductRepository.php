<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository
{
    private int $minutes = 2;

    public function get(): Collection
    {
        $cacheKey = 'products_all';

        return Cache::remember($cacheKey, $this->minutes, function () {
            return Product::with(['price' => function ($query) {
                $query->select('id', 'original', 'final', 'discount_percentage', 'currency', 'product_id');
            }])->select([
                'id',
                'sku',
                'name',
                'category'
            ])->get();
        });
    }

    public function getFiltered(?int $price, ?string $category): Collection
    {
        $cacheKey = 'products_filtered_' . $price . '_' . $category;

        return Cache::remember($cacheKey, $this->minutes, function () use ($category, $price) {
            $query = Product::query();

            $query->when($price, function ($query, $price) {
                $query->whereHas('price', function ($query) use ($price) {
                    $query->where('original', $price);
                });
            });

            $query->when($category, function (Builder $query, $category) {
                $query->where('category', $category);
            });

            return $query->with(['price' => function ($query) {
                $query->select('id', 'original', 'final', 'discount_percentage', 'currency', 'product_id');
            }])->select([
                'id',
                'sku',
                'name',
                'category'
            ])->get();
        });
    }
}
