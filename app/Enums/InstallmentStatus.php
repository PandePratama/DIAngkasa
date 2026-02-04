<?php

namespace App\Enums;

enum InstallmentStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case OVERDUE = 'overdue'; // Opsional: jika telat bayar
    case FAILED = 'failed';   // Opsional: jika autodebet gagal total

    // Helper untuk label yang cantik di View/Blade
    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Belum Lunas',
            self::PAID => 'Lunas',
            self::OVERDUE => 'Jatuh Tempo',
            self::FAILED => 'Gagal',
        };
    }

    // Helper untuk warna Badge Bootstrap/Tailwind
    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'warning', // Kuning
            self::PAID => 'success',   // Hijau
            self::OVERDUE => 'danger', // Merah
            self::FAILED => 'secondary', // Abu-abu
        };
    }
}
