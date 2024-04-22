<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function get(): Collection
    {
        return Product::with(['price' => function ($query) {
            $query->select('id', 'original', 'final', 'discount_percentage', 'currency', 'product_id');
        }])->select([
            'id',
            'sku',
            'name',
            'category'
        ])->get();
    }

    public function getFiltered(?int $price, ?string $category): Collection
    {
        $query = Product::query();

        $query->when($price, function (Builder $query, $price) {
            $query->where('price_original', '<=', $price);
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
    }
}
