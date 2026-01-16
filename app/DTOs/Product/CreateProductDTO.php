<?php

namespace App\DTOs\Product;

readonly class CreateProductDTO
{
    public function __construct(
        public string $name,
        public int $price,
        public int $stockQuantity,
        public bool $isActive = true
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            name: $request->validated('name'),
            price: $request->validated('price'),
            stockQuantity: $request->validated('stock_quantity'),
            isActive: $request->validated('is_active', true)
        );
    }
}
