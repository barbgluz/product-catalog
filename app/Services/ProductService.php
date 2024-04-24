<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    const INSURANCE_DISCOUNT_PERCENTAGE = 30;
    const SKU_000003_DISCOUNT_PERCENTAGE = 15;

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function get(int $price = null, string $category = null): Collection
    {
        if ($category || $price) {
            $cacheKey = 'products_filtered_' . $price . '_' . $category;

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            $products = $this->productRepository->getFiltered($price, $category);
        } else {
            $cacheKey = 'products_all';

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            $products = $this->productRepository->get();
        }
        $this->setFinalPrice($products);

        return $products;
    }

    private function setFinalPrice(Collection $products): void
    {
        foreach ($products as $product) {
            if($this->isInsurance($product)) {
                $product->price->final = $this->applyDiscount($product->price->original, self::INSURANCE_DISCOUNT_PERCENTAGE);
                $product->price->discount_percentage = self::INSURANCE_DISCOUNT_PERCENTAGE . '%';
            }
            else if($this->isSku000003($product)) {
                $product->price->final = $this->applyDiscount($product->price->original, self::SKU_000003_DISCOUNT_PERCENTAGE);
                $product->price->discount_percentage = self::SKU_000003_DISCOUNT_PERCENTAGE . '%';
            }
            else {
                $product->price->final = $product->price->original;
            }
        }
    }
    private function isInsurance(Product $product): bool
    {
        return $product->category === 'insurance';
    }

    private function isSku000003(Product $product): bool
    {
        return $product->sku === '000003';
    }

    private function applyDiscount(int $price, int $discountPercentage): int
    {
        return $price - ($price * $discountPercentage / 100);
    }
}
