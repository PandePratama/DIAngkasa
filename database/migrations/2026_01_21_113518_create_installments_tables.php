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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order')->constrained('orders')->onDelete('cascade');

            $table->integer('installment_number'); // Bulan ke-1, ke-2, dst
            $table->decimal('amount', 15, 2); // Jumlah potong gaji bulan ini
            $table->date('due_date'); // Tanggal jadwal potong

            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->text('notes')->nullable(); // Catatan jika ada denda/dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments_tables');
    }
};
