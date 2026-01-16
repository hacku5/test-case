<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->whenLoaded('product', fn() => $this->product->name),
            'quantity' => $this->quantity,
            'unit_price' => Money::fromInt($this->unit_price)->format(),
            'subtotal' => Money::fromInt($this->quantity * $this->unit_price)->format(),
        ];
    }
}
