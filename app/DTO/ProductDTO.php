<?php

namespace App\DTO;

class ProductDTO
{
    public string $sku;
    public string $name;
    public string $category;
    public PriceDTO $price;

    public function __construct(int $sku, string $name, string $category, PriceDTO $price)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->category = $category;
        $this->price = $price;
    }
}
