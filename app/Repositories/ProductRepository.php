<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function get(): Collection
    {
        return Product::select([
            'id',
            'sku',
            'name',
            'category',
            'price_original',
            'price_final',
            'discount_percentage',
            'currency'
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

        return $query->select([
            'id',
            'sku',
            'name',
            'category',
            'price_original',
            'price_final',
            'discount_percentage',
            'currency'
        ])->get();
    }
}
