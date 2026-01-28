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
        Schema::table('product_diamart', function (Blueprint $table) {
            // 1. Drop foreign key dulu
            $table->dropForeign('product_diamart_id_brand_foreign');

            // 2. Baru drop kolom
            $table->dropColumn('id_brand');
        });
    }

    public function down()
    {
        Schema::table('product_diamart', function (Blueprint $table) {
            $table->unsignedBigInteger('id_brand')->nullable();

            $table->foreign('id_brand')
                ->references('id')
                ->on('brands')
                ->nullOnDelete();
        });
    }
};
