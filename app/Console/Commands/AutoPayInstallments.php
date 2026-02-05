<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CreditInstallment;
use App\Models\CreditTransaction; // Pastikan Model Parent di-import
use App\Models\BalanceMutation;
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

        // 1. Ambil Cicilan yang Jatuh Tempo & Belum Bayar
        // Status cicilan 'unpaid'
        $installments = CreditInstallment::with(['creditTransaction.user', 'creditTransaction.product'])
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<=', $today)
            ->get();

        $count = $installments->count();
        $this->info("Ditemukan {$count} tagihan yang harus diproses.");

        foreach ($installments as $item) {
            $trx = $item->creditTransaction; // Ini adalah Parent (CreditTransaction)
            $user = $trx->user ?? null;
            $productName = $trx->product->name ?? 'Barang';

            // Info Tenor & Bulan Ke
            $tenorTotal = $trx->tenor;
            $bulanKe = $item->installment_month;

            if (!$user) {
                Log::error("AUTODEBET ERROR: Cicilan ID {$item->id} tidak memiliki user valid.");
                continue;
            }

            $tagihan = $item->amount;

            DB::beginTransaction();
            try {
                // 2. Cek Saldo User
                if ($user->saldo >= $tagihan) {

                    $saldoAwal = $user->saldo;
                    $saldoAkhir = $saldoAwal - $tagihan;

                    // Potong Saldo
                    $user->decrement('saldo', $tagihan);

                    // 3. Catat Mutasi
                    $deskripsiLengkap = "Autodebet Cicilan ({$bulanKe}/{$tenorTotal}): {$productName}";

                    BalanceMutation::create([
                        'user_id' => $user->id,
                        'type'    => 'debit',
                        'amount'  => $tagihan,
                        'current_balance' => $saldoAkhir,
                        'description' => $deskripsiLengkap,
                        'reference_id' => 'INST-' . $item->id
                    ]);

                    // 4. Update Status Cicilan -> 'paid'
                    $item->update([
                        'status' => 'paid',
                        'updated_at' => now()
                    ]);

                    // ==========================================================
                    // 2. CEK STATUS LUNAS (UPDATE TABEL PARENT)
                    // ==========================================================

                    // Hitung sisa cicilan yang masih 'unpaid' untuk ID Transaksi ini
                    $sisaTagihan = CreditInstallment::where('id_credit_transaction', $trx->id)
                        ->where('status', 'unpaid') // Sesuai enum tabel credit_installment
                        ->count();

                    // Jika sisa 0, berarti LUNAS
                    if ($sisaTagihan == 0) {
                        // UPDATE STATUS PARENT SESUAI ENUM DATABASE ANDA
                        // Enum Anda: 'progress', 'paid', 'complete'

                        $trx->update([
                            'status' => 'paid', // Kita pakai 'paid' untuk menandakan Lunas
                            'updated_at' => now()
                        ]);

                        Log::info("KREDIT LUNAS: Transaksi #{$trx->id} status berubah menjadi 'paid'.");
                        $this->info("   >>> KREDIT #{$trx->id} LUNAS! Status updated to 'paid'.");
                        // ==========================================================
                    } else {
                        // Jika belum lunas, pastikan statusnya 'progress'
                        // (Opsional, untuk menjaga konsistensi)
                        CreditTransaction::where('id', $trx->id)->update(['status' => 'progress']);
                    }
                    // ==========================================================

                    Log::info("AUTODEBET SUKSES: {$user->name} - {$deskripsiLengkap}");
                    $this->info("✓ Sukses: {$user->name} | Cicilan {$bulanKe}/{$tenorTotal} | Sisa Saldo: Rp " . number_format($saldoAkhir));
                } else {
                    Log::warning("AUTODEBET GAGAL: Saldo {$user->name} kurang.");
                    $this->error("✗ Gagal: {$user->name} (Saldo Kurang: Rp " . number_format($user->saldo) . ")");
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
