<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CreditInstallment;
use App\Models\BalanceMutation;
use App\Enums\InstallmentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoPayInstallments extends Command
{
    protected $signature = 'installments:pay';
    protected $description = 'Potong saldo user untuk cicilan jatuh tempo';

    public function handle()
    {
        $this->info('Memulai proses autodebet...');

        $today = Carbon::now()->format('Y-m-d');

        $installments = CreditInstallment::with(['creditTransaction.user', 'creditTransaction.product'])
            ->where('status', InstallmentStatus::UNPAID)
            ->whereDate('due_date', '<=', $today)
            ->get();

        $count = $installments->count();
        $this->info("Ditemukan {$count} tagihan yang harus diproses.");

        foreach ($installments as $item) {
            $trx = $item->creditTransaction;
            $user = $trx->user ?? null;
            $productName = $trx->product->name ?? 'Barang';

            // --- TAMBAHAN: AMBIL INFO TENOR ---
            $tenorTotal = $trx->tenor;
            $bulanKe = $item->installment_month;
            // ----------------------------------

            if (!$user) {
                Log::error("AUTODEBET ERROR: Cicilan ID {$item->id} tidak memiliki user valid.");
                continue;
            }

            $tagihan = $item->amount;

            DB::beginTransaction();
            try {
                if ($user->saldo >= $tagihan) {

                    $saldoAwal = $user->saldo;
                    $saldoAkhir = $saldoAwal - $tagihan;

                    $user->decrement('saldo', $tagihan);

                    // --- UPDATE: DESKRIPSI LEBIH LENGKAP ---
                    // Format: "Autodebet Cicilan (1/3): OPPO A5X"
                    $deskripsiLengkap = "Autodebet Cicilan ({$bulanKe}/{$tenorTotal}): {$productName}";

                    BalanceMutation::create([
                        'user_id' => $user->id,
                        'type'    => 'debit',
                        'amount'  => $tagihan,
                        'current_balance' => $saldoAkhir,
                        'description' => $deskripsiLengkap, // <--- Pakai deskripsi baru
                        'reference_id' => 'INST-' . $item->id
                    ]);

                    $item->update([
                        'status' => InstallmentStatus::PAID,
                        'updated_at' => now()
                    ]);

                    // Cek Lunas
                    $sisaCicilan = CreditInstallment::where('id_credit_transaction', $trx->id)
                        ->where('status', InstallmentStatus::UNPAID)
                        ->count();

                    if ($sisaCicilan == 0) {
                        $trx->update(['status' => 'paid_off']);
                        Log::info("KREDIT LUNAS: Transaksi ID {$trx->id} selesai.");
                    }

                    Log::info("AUTODEBET SUKSES: {$user->name} - {$deskripsiLengkap}");

                    // --- UPDATE: INFO DI TERMINAL ---
                    $this->info("✓ Sukses: {$user->name} | Cicilan {$bulanKe} dari {$tenorTotal} | Sisa Saldo: Rp " . number_format($saldoAkhir));
                } else {
                    Log::warning("AUTODEBET GAGAL: Saldo {$user->name} kurang.");
                    $this->error("✗ Gagal: {$user->name} (Saldo Kurang)");
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("AUTODEBET ERROR (ID: {$item->id}): " . $e->getMessage());
                $this->error("Error System pada ID: {$item->id}");
            }
        }

        $this->info('Autodebet selesai.');
    }
}
