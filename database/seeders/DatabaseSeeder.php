<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Price;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $productsData = [
            [
                "sku" => "000001",
                "name" => "Full coverage insurance",
                "category" => "insurance",
                "price" => 89000
            ],
            [
                "sku" => "000002",
                "name" => "Compact Car X3",
                "category" => "vehicle",
                "price" => 99000
            ],
            [
                "sku" => "000003",
                "name" => "SUV Vehicle, high end",
                "category" => "vehicle",
                "price" => 150000
            ],
            [
                "sku" => "000004",
                "name" => "Basic coverage",
                "category" => "insurance",
                "price" => 20000
            ],
            [
                "sku" => "000005",
                "name" => "Convertible X2, Electric",
                "category" => "vehicle",
                "price" => 250000
            ]
        ];

        foreach ($productsData as $productData) {
            $product = Product::create([
                'sku' => $productData['sku'],
                'name' => $productData['name'],
                'category' => $productData['category'],
            ]);

            Price::create([
                'original' => $productData['price'],
                'product_id' => $product->id,
            ]);
        }
    }
}
