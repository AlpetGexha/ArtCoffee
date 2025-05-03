<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;

enum OrderStatus: string implements HasColor
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case READY = 'ready';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::READY => 'success',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
