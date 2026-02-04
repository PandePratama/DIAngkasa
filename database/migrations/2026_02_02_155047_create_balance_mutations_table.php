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
        Schema::create('balance_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']); // Credit=Masuk, Debit=Keluar
            $table->decimal('amount', 15, 2);
            $table->decimal('current_balance', 15, 2); // Saldo setelah transaksi
            $table->string('description'); // Contoh: "Bayar DP Kulkas", "Topup Saldo"
            $table->string('reference_id')->nullable(); // ID Transaksi terkait
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_mutations');
    }
};
