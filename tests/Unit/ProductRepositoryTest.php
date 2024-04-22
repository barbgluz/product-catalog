<?php

namespace Tests\Unit;

use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

final class ProductRepositoryTest extends TestCase
{
    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = new ProductRepository();
    }

    public function testGetAllProducts(): void
    {
        $jsonData = file_get_contents('tests/Unit/jsonData/allProducts.json');
        $data = json_decode($jsonData, true);

        $repository = new ProductRepository();

        $products = $repository->get();

        $this->assertCount(count($data), $products);
        $this->assertInstanceOf(Collection::class, $products);
        $this->assertCount(5, $products);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productRepository);
    }
}
