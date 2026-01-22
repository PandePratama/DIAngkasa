<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_diraditya', function (Blueprint $table) {
            // Menambahkan kolom SKU setelah ID
            $table->string('sku')->after('id')->unique();
        });
    }

    public function down(): void
    {
        Schema::table('product_diraditya', function (Blueprint $table) {
            $table->dropColumn('sku');
        });
    }
};
