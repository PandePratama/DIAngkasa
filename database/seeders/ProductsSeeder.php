<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'product_code' => 'PRD-0001',
                'name' => 'Laptop ASUS X441',
                'category_id' => 6,
                'brand_id' => 1,
                'price' => 8500000,
                'price_3_months' => 900000,
                'price_6_months' => 1600000,
                'price_9_months' => 2200000,
                'price_12_months' => 2800000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product_code' => 'PRD-0003',
                'name' => 'Laptop Lenovo Ideapad',
                'category_id' => 6,
                'brand_id' => 3,
                'price' => 9200000,
                'price_3_months' => 1000000,
                'price_6_months' => 1700000,
                'price_9_months' => 2400000,
                'price_12_months' => 3000000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
