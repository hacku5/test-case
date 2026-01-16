<?php

namespace App\DTOs\Customer;

readonly class CreateCustomerDTO
{
    public function __construct(
        public string $name,
        public string $email
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email')
        );
    }
}
