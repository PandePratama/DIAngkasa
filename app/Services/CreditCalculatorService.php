<?php

namespace App\Services;

// use App\Models\Product;
use App\Models\ProductRaditya;
use Carbon\Carbon;

class CreditCalculatorService
{
    const ADMIN_FEE_FIRST_MONTH = 20000;

    /**
     * 1. Fungsi Utama: Menghitung Angka-angka (Retail, Cicilan Murni)
     */
    public function calculate(ProductRaditya $product, int $tenor, float $inputDp)
    {
        // A. Validasi Min DP
        $minDpPercent = $this->getMinDpPercent($product->selling_price);
        $minDpNominal = $product->selling_price * ($minDpPercent / 100);

        if ($inputDp < $minDpNominal) {
            throw new \Exception("DP kurang! Minimal Rp " . number_format($minDpNominal));
        }

        // B. Tentukan Variabel Rumus
        $upPricePercent = $this->getUpPriceRange($product->hpp);
        $interestPercent = $this->getInterestRate($tenor);

        // C. Hitung Harga Retail (Principal)
        // Rumus: (Harga Jual - DP) + (Up Price %)
        $sisaPokok = $product->selling_price - $inputDp;
        if ($sisaPokok < 0) {
            // Artinya DP lebih mahal dari harga barang (Aneh kan?)
            $sisaPokok = 0;
        }
        $retailPrice = $sisaPokok * (1 + ($upPricePercent / 100));

        // D. Hitung Cicilan Dasar (Sebelum Admin Fee)
        // Rumus: (Retail * (1 + Bunga)) / Tenor
        $grossInstallment = ($retailPrice * (1 + ($interestPercent / 100))) / $tenor;

        // E. Pembulatan ke 1.000 ke atas
        $monthlyInstallment = ceil($grossInstallment / 1000) * 1000;

        return [
            'product_hpp_snapshot' => $product->hpp,
            'product_price_snapshot' => $product->selling_price,
            'dp_amount' => $inputDp,
            'tenor' => $tenor,
            'up_price_percent' => $upPricePercent,
            'interest_percent' => $interestPercent,
            'retail_price' => $retailPrice,
            'monthly_installment' => $monthlyInstallment, // Cicilan murni
        ];
    }

    /**
     * 2. Fungsi Generate Jadwal (Array untuk insert ke table Installments)
     * Disinilah Admin Fee 20rb ditambahkan khusus bulan ke-1
     */
    public function generateSchedule(array $calculationResult)
    {
        $schedules = [];
        $baseAmount = $calculationResult['monthly_installment'];
        $startDate = now(); // Atau request input date

        for ($i = 1; $i <= $calculationResult['tenor']; $i++) {

            // LOGIC ADMIN FEE: Jika bulan 1, tambah 20rb
            $amount = ($i === 1) ? ($baseAmount + self::ADMIN_FEE_FIRST_MONTH) : $baseAmount;

            $schedules[] = [
                'month_sequence' => $i,
                'due_date' => $startDate->copy()->addMonths($i)->format('Y-m-d'),
                'amount' => $amount,
                'note' => ($i === 1) ? 'Include Admin Fee' : 'Regular'
            ];
        }

        return $schedules;
    }

    // --- Private Helpers (Rumus Statis) ---

    private function getUpPriceRange($hpp)
    {
        if ($hpp <= 3000000) return 17.5;
        if ($hpp <= 8000000) return 22.5;
        return 27.5;
    }

    private function getMinDpPercent($price)
    {
        if ($price <= 2000000) return 0;
        if ($price <= 3000000) return 20;
        if ($price <= 8000000) return 25;
        return 30;
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
}
