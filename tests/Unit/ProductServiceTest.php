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

    public function testItMustGetProductsFromPrice()
    {
        $category = null;
        $price = 20000;
        $jsonData = file_get_contents('tests/Unit/jsonData/insuranceCategoryAndPrice20000Products.json');
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
        $this->assertCount(1, $result);
        foreach ($products as $product) {
            $this->assertEquals($price, $product->price_original);
        }
    }
    public function testItMustGetProductsFromAPriceAndACategory()
    {
        $category = 'insurance';
        $price = 20000;
        $jsonData = file_get_contents('tests/Unit/jsonData/insuranceCategoryAndPrice20000Products.json');
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
        $this->assertCount(1, $result);
        foreach ($products as $product) {
            $this->assertEquals($price, $product->price_original);
        }
    }

    public function testItMustApplyDiscountToInsuranceCategory()
    {
        $category = 'insurance';
        $price = null;

        $products = collect([
            new Product([
                "id" => 1,
                "sku" => "000001",
                "name" => "Full coverage insurance",
                "category" => "insurance",
                "price_original" => 89000,
                "price_final" => null,
                "discount_percentage" => null,
                "currency" => "USD",
                "created_at" => null,
                "updated_at" => null
            ])
        ]);

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->once()
            ->andReturn(new Collection($products));

        $result = $this->productService->get($price, $category);

        $this->assertEquals(30, $result[0]->discount_percentage);
        $this->assertEquals(62300, $result[0]->price_final);
    }

    public function testItMustApplyDiscountToSku000003()
    {
        $products = collect([
            new Product([
                "id" => 1,
                "sku" => "000003",
                "name" => "Full coverage insurance",
                "category" => "insurance",
                "price_original" => 150000,
                "price_final" => null,
                "discount_percentage" => null,
                "currency" => "USD",
                "created_at" => null,
                "updated_at" => null
            ])
        ]);

        $this->productRepository
            ->shouldReceive('get')
            ->once()
            ->andReturn(new Collection($products));

        $result = $this->productService->get();

        $this->assertEquals(15, $result[0]->discount_percentage);
        $this->assertEquals(127500, $result[0]->price_final);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productService);
        unset($this->productRepository);
    }
}
