<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');

            // Relasi ke Produk Diamart (Nullable / Boleh Kosong)
            $table->foreignId('id_product_diamart')
                ->nullable()
                ->constrained('product_diamart')
                ->onDelete('cascade');

            // Relasi ke Produk Diraditya (Nullable / Boleh Kosong)
            // Ini yang sedang dicari oleh error Anda
            $table->foreignId('id_product_diraditya')
                ->nullable()
                ->constrained('product_diraditya')
                ->onDelete('cascade');

            $table->boolean('is_primary')->default(false); // Opsional: untuk thumbnail utama
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
