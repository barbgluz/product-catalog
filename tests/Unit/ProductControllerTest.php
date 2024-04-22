<?php

namespace Tests\Unit;

use App\Http\Controllers\ProductController;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

final class ProductControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->productService = Mockery::mock(ProductService::class);
        $this->productController = new ProductController($this->productService);
        $this->app->instance(ProductController::class, $this->productController);
        $this->request = Mockery::mock(Request::class);
    }

    public function testItMustReturnAllProducts()
    {
        $jsonData = file_get_contents('tests/Unit/jsonData/allProducts.json');
        $data = new Collection(json_decode($jsonData, true));

        $this->request
            ->shouldReceive('query')
            ->with('price')
            ->once()
            ->andReturnNull();

        $this->request
            ->shouldReceive('query')
            ->with('category')
            ->once()
            ->andReturnNull();

        $this->productService
            ->shouldReceive('get')
            ->once()
            ->andReturn($data);

        $response = $this->productController->getProducts($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(5, $response->getData());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productService);
        unset($this->productController);
    }
}
