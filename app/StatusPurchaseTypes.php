<?php

namespace App\Enums;

enum StatusPurchaseTypes: string
{
    case BALANCE = 'balance';
    case CASH = 'cash';
    case CREDIT = 'credit';

    public function label(): string
    {
        return match ($this) {
            self::BALANCE => 'Potong Saldo',
            self::CASH => 'Cash / Tunai',
            self::CREDIT => 'Kredit',
        };
    }
}
