<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_the_products_endpoint_and_returns_a_successful_response(): void
    {
        $jsonData = file_get_contents('tests/Unit/jsonData/allProducts.json');
        $data = new Collection(json_decode($jsonData, true));

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertJson($data->toArray());
    }

    public function test_the_products_endpoint_with_category_and_returns_a_successful_response(): void
    {
        $jsonData = file_get_contents('tests/Unit/jsonData/insuranceCategoryProducts.json');
        $data = new Collection(json_decode($jsonData, true));
        $category = 'insurance';

        $response = $this->get('/products?category='.$category);

        $response->assertStatus(200);
        $response->assertJson($data->toArray());
    }

}
