<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\InstallmentStatus;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('credit_installment', function (Blueprint $table) {
            // Ambil semua value dari Enum ('unpaid', 'paid', dll)
            $values = array_column(InstallmentStatus::cases(), 'value');

            $table->enum('status', $values)
                ->default(InstallmentStatus::UNPAID->value) // Default 'unpaid'
                ->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_installment', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
