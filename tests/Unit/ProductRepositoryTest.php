<?php

namespace Tests\Unit;

use App\Models\Price;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = new ProductRepository();
    }

    public function testGetAllProducts(): void
    {
        $products = \Database\Factories\ProductFactory::times(3)->create();
        foreach ($products as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'product_id' => $product->id,
            ]);
        }
        $retrievedProducts = $this->productRepository->get();

        $this->assertInstanceOf(Collection::class, $retrievedProducts);
        foreach ($retrievedProducts as $retrievedProduct) {
            $this->assertNotNull($retrievedProduct->id);
            $this->assertNotNull($retrievedProduct->sku);
            $this->assertNotNull($retrievedProduct->name);
            $this->assertNotNull($retrievedProduct->category);
            $this->assertNotNull($retrievedProduct->price);
            $this->assertInstanceOf(Price::class, $retrievedProduct->price);
            $this->assertNotNull($retrievedProduct->price->first()->id);
            $this->assertNotNull($retrievedProduct->price->first()->original);
            $this->assertNotNull($retrievedProduct->price->first()->final);
            $this->assertNotNull($retrievedProduct->price->first()->currency);
        }
    }

    public function testGetByCategory(): void
    {
        $price = null;
        $category = 'insurance';
        $products = \Database\Factories\ProductFactory::times(3)->create(['category' => $category]);
        foreach ($products as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'product_id' => $product->id,
            ]);
        }
        $retrievedProducts = $this->productRepository->getFiltered($price, $category);

        $this->assertCount(count($products), $retrievedProducts);
        $this->assertInstanceOf(Collection::class, $retrievedProducts);
        foreach ($retrievedProducts as $product) {
            $this->assertEquals($category, $product->category);
            $this->assertNotNull($product->price);
        }
    }

    public function testGetByPrice(): void
    {
        $product = (new \Database\Factories\ProductFactory)->create();
        $price = (new \Database\Factories\PriceFactory)->create([
            'product_id' => $product->id,
            'original' => 20000
        ]);
        $priceOriginal = 20000;

        $products = $this->productRepository->getFiltered($priceOriginal, $product->category);

        $this->assertNotEmpty($products);
        foreach ($products as $product) {
            $this->assertNotNull($product->price);
            $this->assertInstanceOf(Price::class, $product->price);
            $this->assertEquals($priceOriginal, $product->price->original);
        }
    }

    public function testGetByPriceAndCategory(): void
    {
        $category = 'insurance';
        $product = (new \Database\Factories\ProductFactory)->create(['category' => $category]);
        $price = (new \Database\Factories\PriceFactory)->create([
            'product_id' => $product->id,
            'original' => 20000
        ]);
        $priceOriginal = 20000;

        $products = $this->productRepository->getFiltered($priceOriginal, $product->category);

        $this->assertNotEmpty($products);
        foreach ($products as $product) {
            $this->assertNotNull($product->price);
            $this->assertInstanceOf(Price::class, $product->price);
            $this->assertEquals($priceOriginal, $product->price->original);
            $this->assertEquals($category, $product->category);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productRepository);
    }
}
