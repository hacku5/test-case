<?php

namespace App\Support;

class Money
{
    public function __construct(
        private int $value
    ) {
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public static function fromDecimal(float|int $amount): self
    {
        return new self((int) round($amount * 100));
    }

    public function raw(): int
    {
        return $this->value;
    }

    public function decimal(): float
    {
        return $this->value / 100;
    }

    public function format(string $symbol = '₺'): string
    {
        return number_format($this->decimal(), 2, ',', '.') . ' ' . $symbol;
    }

    public function add(Money $other): self
    {
        return new self($this->value + $other->value);
    }

    public function multiply(int $quantity): self
    {
        return new self($this->value * $quantity);
    }
}
