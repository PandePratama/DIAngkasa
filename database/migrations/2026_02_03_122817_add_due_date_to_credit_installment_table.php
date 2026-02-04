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
        Schema::table('credit_installment', function (Blueprint $table) {
            // Menambahkan kolom due_date (Boleh kosong dulu untuk data lama)
            $table->date('due_date')->nullable()->after('amount');
        });
    }

    public function down()
    {
        Schema::table('credit_installment', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
