<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambah kolom ID
            $table->unsignedBigInteger('purchase_type_id')->nullable()->after('id_user');

            // (Opsional) Hapus kolom lama jika sudah tidak dipakai nanti
            // $table->dropColumn('payment_method');

            // Buat Foreign Key
            $table->foreign('purchase_type_id')->references('id')->on('purchase_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
