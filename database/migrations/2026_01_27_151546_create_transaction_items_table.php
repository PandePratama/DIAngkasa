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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            // Hubungkan ke tabel transactions
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');

            // Simpan ID Produk (Bisa Nullable karena ada 2 tipe produk: Diamart/Raditya)
            $table->foreignId('id_product_diamart')->nullable();
            $table->foreignId('id_product_diraditya')->nullable(); // Sesuaikan nama tabel produk gadget Anda

            // SNAPSHOT DATA (Penting!)
            $table->string('product_name'); // Simpan nama saat beli
            $table->decimal('price', 15, 2); // Simpan harga saat beli
            $table->integer('qty');
            $table->decimal('subtotal', 15, 2); // price * qty

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
