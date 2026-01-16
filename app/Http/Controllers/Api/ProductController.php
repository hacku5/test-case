<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\DTOs\Product\CreateProductDTO;
use App\DTOs\Product\UpdateProductDTO;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Ürünler başarıyla getirildi.',
            'data' => ProductResource::collection($products),
            'meta' => [
                'total' => $products->count(),
            ],
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $dto = CreateProductDTO::fromRequest($request);
        $product = $this->productService->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Ürün başarıyla oluşturuldu.',
            'data' => new ProductResource($product),
        ], 201);
    }

    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $dto = UpdateProductDTO::fromRequest($request, $id);
        $product = $this->productService->update($dto);

        return response()->json([
            'success' => true,
            'message' => 'Ürün başarıyla güncellendi.',
            'data' => new ProductResource($product),
        ]);
    }
}
