<?php

namespace App\Enums;

enum SubscriptionMethod: string
{
    case Manual = 'manual';
    case Gateway = 'gateway';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manual',
            self::Gateway => 'Gateway',
        };
    }
}
