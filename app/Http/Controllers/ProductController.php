<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getProducts(Request $request): JsonResponse
    {
        $price = $request->query('price');
        $category = $request->query('category');

        $result = $this->productService->get($price, $category);

        if ($result->isEmpty()) {
            return response()->json($result, 404);
        }
        return response()->json($result, 200);
    }
}
