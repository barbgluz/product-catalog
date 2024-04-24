<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    public function test_it_returns_products_with_valid_parameters()
    {
        $productService = Mockery::mock(ProductService::class);
        $productService->shouldReceive('get')->once()->with(100, 'insurance')->andReturn(new Collection([
            new Product(['id' => 1, 'sku' => 'SKU001', 'name' => 'Product A', 'category' => 'insurance']),
            new Product(['id' => 2, 'sku' => 'SKU002', 'name' => 'Product B', 'category' => 'insurance']),
        ]));

        $this->app->instance(ProductService::class, $productService);

        $request = Request::create('/api/products', 'GET', ['price' => 100, 'category' => 'insurance']);

        $controller = new ProductController($productService);
        $response = $controller->getProducts($request);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertCount(2, $response->getData());
        $responseDataArray = array_map(function ($item) {
            return (array) $item;
        }, $response->getData());

        $this->assertEquals([
            ['sku' => 'SKU001', 'name' => 'Product A', 'category' => 'insurance'],
            ['sku' => 'SKU002', 'name' => 'Product B', 'category' => 'insurance'],
        ], $responseDataArray);
    }


    public function test_it_returns_404_when_no_products_found()
    {
        $productService = Mockery::mock(ProductService::class);
        $productService->shouldReceive('get')->once()->with(100, 'insurance')->andReturn(new Collection());

        $this->app->instance(ProductService::class, $productService);

        $request = Request::create('/api/products', 'GET', ['price' => 100, 'category' => 'insurance']);

        $controller = new ProductController($productService);
        $response = $controller->getProducts($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
