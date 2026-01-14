<?php

namespace App\Support;

use Carbon\Carbon;

class CreditPeriod
{
    public static function current()
    {
        $today = Carbon::now();

        if ($today->day <= 15) {
            $start = $today->copy()->subMonth()->day(16)->startOfDay();
            $end   = $today->copy()->day(15)->endOfDay();
        } else {
            $start = $today->copy()->day(16)->startOfDay();
            $end   = $today->copy()->addMonth()->day(15)->endOfDay();
        }

        return [$start, $end];
    }
}
