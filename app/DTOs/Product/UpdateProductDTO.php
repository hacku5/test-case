<?php

namespace App\DTOs\Product;

readonly class UpdateProductDTO
{
    public function __construct(
        public string $id,
        public ?string $name,
        public ?int $price,
        public ?int $stockQuantity,
        public ?bool $isActive
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request, string $id): self
    {
        return new self(
            id: $id,
            name: $request->validated('name'),
            price: $request->validated('price'),
            stockQuantity: $request->validated('stock_quantity'),
            isActive: $request->validated('is_active')
        );
    }
}
