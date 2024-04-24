<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

final class ProductServiceTest extends TestCase
{
    use RefreshDatabase;
    private ProductService $productService;

    private ProductRepository|Mock $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->productService = new ProductService($this->productRepository);
    }

    public function testItMustGetAllProducts_withoutCache()
    {
        $cacheKey = 'products_all';
        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create();
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'product_id' => $product->id,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('get')
            ->andReturn($expectedProducts);

        $products = $this->productService->get();

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertNotNull($product->price);
        }
    }

    public function testItMustGetAllProducts_withCache()
    {
        $cacheKey = 'products_all';
        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create();
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'product_id' => $product->id,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with($cacheKey)
            ->andReturn($expectedProducts);

        $this->productRepository
            ->shouldReceive('get')
            ->andReturn($expectedProducts);

        $products = $this->productService->get();

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertNotNull($product->price);
        }
    }

    public function testItMustGetProductsFromACategory_withoutCache()
    {

        $category = 'insurance';
        $price = null;
        $cacheKey = 'products_filtered_' . $price . '_' . $category;

        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create(['category' => $category]);
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'product_id' => $product->id,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->andReturn($expectedProducts);

        $products = $this->productService->get($price, $category);

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertEquals($category, $product->category);
            $this->assertNotNull($product->price);
        }
    }

    public function testItMustGetProductsFromACategory_withCache()
    {

        $category = 'insurance';
        $price = null;
        $cacheKey = 'products_filtered_' . $price . '_' . $category;

        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create(['category' => $category]);
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'product_id' => $product->id,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with($cacheKey)
            ->andReturn($expectedProducts);

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->andReturn($expectedProducts);

        $products = $this->productService->get($price, $category);

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertEquals($category, $product->category);
            $this->assertNotNull($product->price);
        }
    }

    public function testItMustGetProductsFromPrice_withouCache()
    {
        $category = null;
        $price = 20000;
        $cacheKey = 'products_filtered_' . $price . '_' . $category;
        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create();
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'original' => $price,
                'product_id' => $product->id,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->andReturn($expectedProducts);

        $products = $this->productService->get($price, $category);

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertNotNull($product->price);
            $this->assertEquals($price, $product->price->original);
        }
    }

    public function testItMustGetProductsFromPrice_withCache()
    {
        $category = null;
        $price = 20000;
        $cacheKey = 'products_filtered_' . $price . '_' . $category;
        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create();
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'original' => $price,
                'product_id' => $product->id,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with($cacheKey)
            ->andReturn($expectedProducts);

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->andReturn($expectedProducts);

        $products = $this->productService->get($price, $category);

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertNotNull($product->price);
            $this->assertEquals($price, $product->price->original);
        }
    }

    public function testItMustGetProductsFromAPriceAndACategory_withoutCache()
    {
        $category = 'insurance';
        $price = 20000;
        $cacheKey = 'products_filtered_' . $price . '_' . $category;

        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create(['category' => $category]);
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'original' => $price,
                'product_id' => $product->id,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->andReturn($expectedProducts);

        $products = $this->productService->get($price, $category);

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertEquals($category, $product->category);
            $this->assertNotNull($product->price);
            $this->assertEquals($price, $product->price->original);
        }
    }

    public function testItMustGetProductsFromAPriceAndACategory_withCache()
    {
        $category = 'insurance';
        $price = 20000;
        $cacheKey = 'products_filtered_' . $price . '_' . $category;

        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create(['category' => $category]);
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'original' => $price,
                'product_id' => $product->id,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with($cacheKey)
            ->andReturn($expectedProducts);

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->andReturn($expectedProducts);

        $products = $this->productService->get($price, $category);

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertEquals($category, $product->category);
            $this->assertNotNull($product->price);
            $this->assertEquals($price, $product->price->original);
        }
    }

    public function testItMustApplyDiscountToInsuranceCategory()
    {
        $category = 'insurance';
        $price = 80000;
        $discountedPrice = 56000;
        $discountPercentage = "30%";
        $expectedProducts = \Database\Factories\ProductFactory::times(5)->create(['category' => $category]);
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'original' => $price,
                'product_id' => $product->id,
            ]);
        }

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->andReturn($expectedProducts);

        $products = $this->productService->get($price, $category);

        $this->assertEquals($expectedProducts, $products);
        $this->assertCount(count($expectedProducts), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertEquals($category, $product->category);
            $this->assertNotNull($product->price);
            $this->assertEquals($discountPercentage, $product->price->discount_percentage);
            $this->assertEquals($price, $product->price->original);
            $this->assertEquals($discountedPrice, $product->price->final);
        }
    }

    public function testItMustApplyDiscountToSku000003()
    {
        $sku = '000003';
        $price = 80000;
        $discountedPrice = 68000;
        $discountPercentage = "15%";

        $product = (new \Database\Factories\ProductFactory)->create(['sku' => $sku]);
        (new \Database\Factories\PriceFactory)->create([
            'original' => $price,
            'product_id' => $product->id,
        ]);

        $expectedProduct = new Collection([$product]);

        $this->productRepository
            ->shouldReceive('get')
            ->andReturn($expectedProduct);

        $products = $this->productService->get();

        $this->assertEquals($expectedProduct, $products);
        $this->assertCount(count($expectedProduct), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertNotNull($product->price);
            $this->assertEquals($discountPercentage, $product->price->discount_percentage);
            $this->assertEquals($price, $product->price->original);
            $this->assertEquals($discountedPrice, $product->price->final);
        }
        $this->refreshDatabase();
    }

    public function testItMustApplyOnlyInsuranceDiscount_ifSku000003BelongsToInsuranceCategory()
    {
        $category = 'insurance';
        $sku = '000003';
        $price = 80000;
        $discountedPrice = 56000;
        $discountPercentage = "30%";

        $product = (new \Database\Factories\ProductFactory)->create([
            'sku' => $sku,
            'category' => $category,
        ]);
        (new \Database\Factories\PriceFactory)->create([
            'original' => $price,
            'product_id' => $product->id,
        ]);

        $expectedProduct = new Collection([$product]);

        $this->productRepository
            ->shouldReceive('get')
            ->andReturn($expectedProduct);

        $products = $this->productService->get();

        $this->assertEquals($expectedProduct, $products);
        $this->assertCount(count($expectedProduct), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertNotNull($product->price);
            $this->assertEquals($discountPercentage, $product->price->discount_percentage);
            $this->assertEquals($price, $product->price->original);
            $this->assertEquals($discountedPrice, $product->price->final);
        }
        $this->refreshDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productService);
        unset($this->productRepository);
    }
}
