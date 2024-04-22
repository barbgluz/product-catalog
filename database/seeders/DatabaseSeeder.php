<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            [
                'sku' => '000001',
                'name' => 'Full coverage insurance',
                'category' => 'insurance',
                'price_original' => 89000,
            ],
            [
                'sku' => '000002',
                'name' => 'Compact Car X3',
                'category' => 'vehicle',
                'price_original' => 99000,
            ],
            [
                'sku' => '000003',
                'name' => 'SUV Vehicle, high end',
                'category' => 'vehicle',
                'price_original' => 150000,
            ],
            [
                'sku' => '000004',
                'name' => 'Basic coverage',
                'category' => 'insurance',
                'price_original' => 20000,
            ],
            [
                'sku' => '000005',
                'name' => 'Convertible X2, Electric',
                'category' => 'vehicle',
                'price_original' => 250000,
            ],
        ];

        foreach ($data as $productData) {
            Product::create($productData);
        }
    }
}
