<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

final class ProductServiceTest extends TestCase
{
    private ProductService $productService;

    private ProductRepository|Mock $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->productService = new ProductService($this->productRepository);
    }

    public function testItMustGetAllProducts()
    {
        $jsonData = file_get_contents('tests/Unit/jsonData/allProducts.json');
        $data = new Collection(json_decode($jsonData, true));

        $products = collect($data)->map(function ($productData) {
            return new Product($productData);
        });

        $this->productRepository
            ->shouldReceive('get')
            ->once()
            ->andReturn(new Collection($products));

        $result = $this->productService->get();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testItMustGetProductsFromACategory()
    {
        $category = 'insurance';
        $price = null;
        $jsonData = file_get_contents('tests/Unit/jsonData/insuranceCategoryProducts.json');
        $data = new Collection(json_decode($jsonData, true));

        $products = collect($data)->map(function ($productData) {
            return new Product($productData);
        });

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->once()
            ->andReturn(new Collection($products));

        $result = $this->productService->get($price, $category);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productService);
        unset($this->productRepository);
    }
}
