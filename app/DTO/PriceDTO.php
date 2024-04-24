<?php

namespace App\DTO;

class PriceDTO
{
    public int $original;
    public int $final;
    public ?string $discount_percentage;
    public string $currency;

    public function __construct(int $original, int $final, ?string $discount_percentage, string $currency)
    {
        $this->original = $original;
        $this->final = $final;
        $this->discount_percentage = $discount_percentage;
        $this->currency = $currency;
    }
}
