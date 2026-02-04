<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// 1. Reset Saldo (Gajian) -> Tanggal 25 jam 00:01 WITA
Schedule::command('salary:reset')
    ->monthlyOn(25, '00:01')
    ->timezone('Asia/Makassar'); // Sesuaikan Timezone (WIB/WITA/WIT)

// 2. Potong Cicilan -> Tanggal 25 jam 00:05 WITA
// Dikasih jeda 4 menit biar saldo masuk dulu, baru dipotong
Schedule::command('installments:pay')
    ->monthlyOn(25, '00:05')
    ->timezone('Asia/Makassar');
