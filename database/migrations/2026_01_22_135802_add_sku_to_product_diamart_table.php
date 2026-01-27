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
        Schema::table('product_diamart', function (Blueprint $table) {
            $table->string('sku')->after('id')->unique()->nullable()->before('id_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_diamart', function (Blueprint $table) {
            //
        });
    }
};
