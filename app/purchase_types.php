<?php

namespace App;

enum purchase_types: string
{
    case CASH = 'cash';
    case KREDIT = 'kredit';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::KREDIT => 'Kredit',
        };
    }
}
