<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Beklemede',
            self::COMPLETED => 'Tamamlandı',
            self::CANCELLED => 'İptal Edildi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::COMPLETED, self::CANCELLED],
            self::COMPLETED, self::CANCELLED => [],
        };
    }

    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions());
    }
}