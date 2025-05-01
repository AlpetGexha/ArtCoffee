<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;

enum PaymentStatus: string implements HasColor
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::FAILED => 'danger',
            self::REFUNDED => 'info',
        };
    }
}
