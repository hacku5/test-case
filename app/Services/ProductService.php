<?php

namespace App\Services;

use App\DTOs\Product\CreateProductDTO;
use App\DTOs\Product\UpdateProductDTO;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        protected ProductRepository $productRepository
    ) {
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->productRepository->get();
    }

    public function create(CreateProductDTO $dto): Product
    {
        return $this->productRepository->create([
            'name' => $dto->name,
            'price' => $dto->price,
            'stock_quantity' => $dto->stockQuantity,
            'is_active' => $dto->isActive,
        ]);
    }

    public function update(UpdateProductDTO $dto): Product
    {
        $data = array_filter([
            'name' => $dto->name,
            'price' => $dto->price,
            'stock_quantity' => $dto->stockQuantity,
            'is_active' => $dto->isActive,
        ], fn($value) => $value !== null);

        return $this->productRepository->update($dto->id, $data);
    }
}
