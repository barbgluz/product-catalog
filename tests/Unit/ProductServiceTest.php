<?php

namespace Tests\Unit;

use App\DTO\PriceDTO;
use App\DTO\ProductDTO;
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

        $expectedProductsDTO = $this->convertToProductDTOs($expectedProducts);

        $this->assertEquals($expectedProductsDTO->toArray(), $products->toArray());
        $this->assertCount($expectedProductsDTO->count(), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(ProductDTO::class, $product);
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

        $expectedProductsDTO = $this->convertToProductDTOs($expectedProducts);

        $this->assertEquals($expectedProductsDTO, $products);
        $this->assertCount(count($expectedProductsDTO), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(ProductDTO::class, $product);
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

    public function testItMustGetProductsFromPrice_withoutCache()
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

        $expectedProductsDTO = $this->convertToProductDTOs($expectedProducts);

        $this->assertEquals($expectedProductsDTO, $products);
        $this->assertCount(count($expectedProductsDTO), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(ProductDTO::class, $product);
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

        $expectedProductsDTO = $this->convertToProductDTOs($expectedProducts);

        $this->assertEquals($expectedProductsDTO, $products);
        $this->assertCount(count($expectedProductsDTO), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(ProductDTO::class, $product);
            $this->assertEquals($category, $product->category);
            $this->assertNotNull($product->price);
            $this->assertEquals($price, $product->price->original);
        }
    }

    public function testItMustGetProductsFromAPriceAndACategory_withCache()
    {
        $price = 100;
        $category = 'insurance';

        $cacheKey = 'products_filtered_' . $price . '_' . $category;

        $expectedProducts = \Database\Factories\ProductFactory::times(3)->create(['category' => $category]);
        foreach ($expectedProducts as $product) {
            (new \Database\Factories\PriceFactory)->create([
                'product_id' => $product->id,
                'original' => $price,
            ]);
        }

        Cache::shouldReceive('has')
            ->with($cacheKey)
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('getFiltered')
            ->with($price, $category)
            ->andReturn($expectedProducts);

        Cache::shouldReceive('get')
            ->with($cacheKey)
            ->andReturn($expectedProducts);

        $products = $this->productService->get($price, $category);

        $expectedProductsDTO = $this->convertToProductDTOs($expectedProducts);

        $this->assertEquals($expectedProductsDTO->toArray(), $products->toArray());
        $this->assertCount($expectedProductsDTO->count(), $products);
        foreach ($products as $product) {
            $this->assertInstanceOf(ProductDTO::class, $product);
            $this->assertNotNull($product->price);
        }
    }

    public function testItMustApplyDiscountToInsuranceCategory()
    {
        $product = \App\Models\Product::factory()->create(['category' => 'insurance']);
        $originalPrice = 200;

        (new \Database\Factories\PriceFactory)->create([
            'product_id' => $product->id,
            'original' => $originalPrice,
        ]);

        $expectedDiscountedPrice = $originalPrice - ($originalPrice * ProductService::INSURANCE_DISCOUNT_PERCENTAGE / 100);

        Cache::shouldReceive('has')
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('get')
            ->andReturn(new Collection([$product]));

        $products = $this->productService->get();

        $this->assertInstanceOf(ProductDTO::class, $products->first());
        $this->assertEquals('insurance', $products->first()->category);
        $this->assertEquals($expectedDiscountedPrice, $products->first()->price->final);
        $this->assertEquals(ProductService::INSURANCE_DISCOUNT_PERCENTAGE . '%', $products->first()->price->discount_percentage);
    }

    public function testItMustApplyDiscountToSku000003()
    {
        $product = \App\Models\Product::factory()->create(['sku' => '000003']);
        $originalPrice = 150;

        \App\Models\Price::factory()->create([
            'product_id' => $product->id,
            'original' => $originalPrice,
        ]);

        $expectedDiscountedPrice = floor($originalPrice - ($originalPrice * ProductService::SKU_000003_DISCOUNT_PERCENTAGE / 100));

        Cache::shouldReceive('has')
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('get')
            ->andReturn(new Collection([$product]));

        $products = $this->productService->get();

        $this->assertInstanceOf(ProductDTO::class, $products->first());
        $this->assertEquals('000003', $products->first()->sku);
        $this->assertEquals($expectedDiscountedPrice, $products->first()->price->final);
        $this->assertEquals(ProductService::SKU_000003_DISCOUNT_PERCENTAGE . '%', $products->first()->price->discount_percentage);
    }

    public function testItMustApplyOnlyInsuranceDiscount_ifSku000003BelongsToInsuranceCategory()
    {
        $product = \App\Models\Product::factory()->create([
            'sku' => '000003',
            'category' => 'insurance',
        ]);
        $originalPrice = 150;

        \App\Models\Price::factory()->create([
            'product_id' => $product->id,
            'original' => $originalPrice,
        ]);

        $expectedDiscountedPrice = $originalPrice - ($originalPrice * ProductService::INSURANCE_DISCOUNT_PERCENTAGE / 100);

        Cache::shouldReceive('has')
            ->andReturn(false);

        $this->productRepository
            ->shouldReceive('get')
            ->andReturn(new Collection([$product]));

        $products = $this->productService->get();

        $this->assertInstanceOf(ProductDTO::class, $products->first());
        $this->assertEquals('000003', $products->first()->sku);
        $this->assertEquals('insurance', $products->first()->category);
        $this->assertEquals($expectedDiscountedPrice, $products->first()->price->final);
        $this->assertEquals(ProductService::INSURANCE_DISCOUNT_PERCENTAGE . '%', $products->first()->price->discount_percentage);
    }


    private function convertToProductDTOs(Collection $products): Collection
    {
        $productsDTO = new Collection();
        foreach ($products as $product) {
            $priceDTO = new PriceDTO(
                $product->price->original,
                $product->price->final,
                $product->price->discount_percentage,
                $product->price->currency
            );

            $productsDTO->push(new ProductDTO(
                $product->sku,
                $product->name,
                $product->category,
                $priceDTO
            ));
        }
        return $productsDTO;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productService);
        unset($this->productRepository);
    }
}
