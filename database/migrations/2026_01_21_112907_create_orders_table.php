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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('id_user')->constrained('users');
            $table->foreignId('id_purchase_type')->constrained('purchase_types');
            $table->enum('business_unit', ['diamart', 'raditya']);

            // --- KOLOM KEUANGAN ---
            $table->decimal('total_amount', 15, 2); // Harga Barang Total
            $table->decimal('admin_fee', 15, 2)->default(0); // Biaya Admin (1% atau 20rb)
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2); // Total Bayar

            $table->enum('status', ['pending', 'paid', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
