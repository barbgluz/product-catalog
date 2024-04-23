<?php

namespace Database\Factories;

use App\Models\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'original' => $this->faker->randomDigitNotNull(),
            'final' => $this->faker->randomDigitNotNull(),
            'currency' => 'USD'
        ];
    }
}
