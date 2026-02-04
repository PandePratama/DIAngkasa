<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_installment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_credit_transaction')
                ->constrained('credit_transactions')
                ->cascadeOnDelete();
            $table->foreignId('id_user')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->integer('installment_month'); // bulan ke-
            $table->decimal('amount', 15, 2);
            $table->decimal('admin_fee', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_installment');
    }
};
