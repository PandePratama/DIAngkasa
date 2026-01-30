<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('id_product')
                ->constrained('product_diraditya')
                ->cascadeOnDelete();
            $table->integer('tenor'); // 3, 6, 9, 12 (bulan)
            $table->decimal('up_price', 15, 2);       // harga setelah markup
            $table->decimal('monthly_amount', 15, 2);
            $table->decimal('admin_fee', 15, 2)->default(0);
            $table->integer('total_paid_month')->default(0);

            $table->enum('status', ['progress', 'paid', 'complete'])
                ->default('progress');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
