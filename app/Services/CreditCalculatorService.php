<?php

namespace App\Services;

use App\Models\ProductRaditya;

class CreditCalculatorService
{
    const ADMIN_FEE_FIRST_MONTH = 20000;

    public function calculate(ProductRaditya $product, int $tenor, float $inputDp)
    {
        // 1. Tentukan HPP & Harga Jual
        // Jika HPP 0, gunakan Harga Jual agar tidak error/margin 0
        $hpp = ($product->hpp > 0) ? $product->hpp : $product->price;
        $sellingPrice = $product->price;

        // 2. Hitung Sisa Pokok
        $sisaPokok = $sellingPrice - $inputDp;

        // 3. Tentukan Persentase Up Price (Dari HPP)
        $upPricePercent = $this->getUpPricePercent($hpp);

        // 4. Hitung Harga Retail (Sisa Pokok + Up Price)
        // Rumus Tabel: 1.759.000 + 17.5% = 2.066.825
        $hargaRetail = $sisaPokok * (1 + ($upPricePercent / 100));

        // 5. Hitung Total Pinjaman dengan Bunga
        // Rumus Tabel: 2.066.825 + 9% (3 bulan) = 2.252.839,25
        $interestPercent = $this->getInterestRate($tenor);
        $totalLoan = $hargaRetail * (1 + ($interestPercent / 100));

        // 6. Hitung Angsuran Bulanan
        // 2.252.839,25 / 3 = 750.946
        $rawMonthly = $totalLoan / $tenor;

        // 7. Pembulatan ke Ribuan Terdekat (751.000)
        $monthlyInstallment = ceil($rawMonthly / 1000) * 1000;

        return [
            'price' => $sellingPrice,
            'hpp' => $hpp,
            'dp_amount' => $inputDp,
            'tenor' => $tenor,
            'principal' => $sisaPokok,
            'up_price_percent' => $upPricePercent,
            'retail_price' => $hargaRetail,
            'interest_percent' => $interestPercent,
            'monthly_installment' => $monthlyInstallment,
        ];
    }

    public function generateSchedule(array $calc)
    {
        $schedules = [];
        $monthly = $calc['monthly_installment'];

        for ($i = 1; $i <= $calc['tenor']; $i++) {
            // Tambah Admin Fee 20rb hanya di bulan pertama
            $amount = ($i === 1) ? ($monthly + self::ADMIN_FEE_FIRST_MONTH) : $monthly;

            $schedules[] = [
                'month_sequence' => $i,
                'amount' => $amount,
            ];
        }
        return $schedules;
    }

    // --- LOGIC SESUAI TABEL MANUAL ---

    private function getUpPricePercent($hpp)
    {
        // 0 - 3.000.000 -> 17.5%
        if ($hpp <= 3000000) return 17.5;

        // 3.000.001 - 8.000.000 -> 22.5%
        if ($hpp <= 8000000) return 22.5;

        // > 8.000.000 -> 27.5%
        return 27.5;
    }

    private function getInterestRate($tenor)
    {
        return match ($tenor) {
            3 => 9,
            6 => 15,
            9 => 23,
            12 => 24,
            default => 0
        };
    }

    // Helper untuk validasi DP (Opsional)
    private function getMinDpPercent($price)
    {
        if ($price <= 2000000) return 0;
        if ($price <= 3000000) return 20;
        if ($price <= 8000000) return 25;
        return 30;
    }
}
