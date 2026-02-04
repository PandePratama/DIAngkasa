<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RegenerateUserBalance extends Command
{
    // Nama command yang akan dipanggil scheduler
    protected $signature = 'salary:reset';
    protected $description = 'Generate ulang saldo user (Gajian)';

    public function handle()
    {
        $this->info('Memulai proses top-up saldo...');

        // Ambil semua user secara bertahap (chunk) agar memori aman
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                // LOGIC: Reset saldo ke limit bulanan (misal 3 Juta)
                // Atau bisa ambil dari kolom 'limit_saldo' jika ada di tabel user
                $limit = 3000000;

                $user->saldo = $limit;
                $user->save();
            }
        });

        $this->info('Saldo berhasil di-reset.');
        Log::info('SCHEDULER: Saldo user telah di-reset ke Rp 3.000.000');
    }
}
