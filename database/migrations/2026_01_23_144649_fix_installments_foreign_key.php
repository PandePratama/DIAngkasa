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
        Schema::table('installments', function (Blueprint $table) {
            $table->dropForeign(['id_order']);

            // 2. Pasang Foreign Key baru yang nyambung ke tabel 'transactions'
            $table->foreign('id_order')
                ->references('id')
                ->on('transactions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            // Kembalikan ke settingan awal (jika rollback)
            $table->dropForeign(['id_order']);

            $table->foreign('id_order')
                ->references('id')
                ->on('orders') // Kembali ke orders
                ->onDelete('cascade');
        });
    }
};
