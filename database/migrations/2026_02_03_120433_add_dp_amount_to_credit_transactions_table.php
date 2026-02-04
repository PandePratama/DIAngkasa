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
        Schema::table('credit_transactions', function (Blueprint $table) {
            // Tambahkan kolom dp_amount jika belum ada
            if (!Schema::hasColumn('credit_transactions', 'dp_amount')) {
                $table->decimal('dp_amount', 15, 2)->default(0)->after('monthly_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropColumn('dp_amount');
        });
    }
};
