<?php

namespace App\DTOs\Order;

use App\Enums\OrderStatus;

readonly class UpdateOrderStatusDTO
{
    public function __construct(
        public string $orderId,
        public OrderStatus $status
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request, string $orderId): self
    {
        return new self(
            orderId: $orderId,
            status: OrderStatus::from($request->validated('status'))
        );
    }
}
