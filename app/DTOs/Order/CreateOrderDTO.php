<?php

namespace App\DTOs\Order;

readonly class CreateOrderDTO
{
    /**
     * @param string $customerId
     * @param array<int, array{product_id: string, quantity: int}> $items
     */
    public function __construct(
        public string $customerId,
        public array $items
    ) {
    }

    // Request'ten DTO üretmek için statik bir metot (Factory Method)
    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        // BValidate edilmiş veriyi al.
        return new self(
            customerId: $request->validated('customer_id'),
            items: $request->validated('items')
        );
    }
}