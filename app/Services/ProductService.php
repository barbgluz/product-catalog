<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

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
        $products = ($category || $price)?
            $this->productRepository->getFiltered($price, $category)
            : $this->productRepository->get();

        $this->setFinalPrice($products);

        return $products;
    }

    private function setFinalPrice(Collection $products): void
    {
        foreach ($products as $product) {
            if($this->isInsurance($product)) {
                $product->price_final = $this->applyDiscount($product->price_original, self::INSURANCE_DISCOUNT_PERCENTAGE);
                $product->discount_percentage = self::INSURANCE_DISCOUNT_PERCENTAGE;
            }
            else if($this->isSku000003($product)) {
                $product->price_final = $this->applyDiscount($product->price_original, self::SKU_000003_DISCOUNT_PERCENTAGE);
                $product->discount_percentage = self::SKU_000003_DISCOUNT_PERCENTAGE;
            }
            else {
                $product->price_final = $product->price_original;
            }
        }

    }
    private function isInsurance(Product $product): bool
    {
        return $product->category == 'insurance';
    }

    private function isSku000003(Product $product): bool
    {
        return $product->sku == '000003';
    }

    private function applyDiscount(int $price, int $discountPercentage): int
    {
        return $price - ($price * $discountPercentage / 100);
    }
}
