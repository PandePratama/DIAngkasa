<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $units = [
            'Airport Operation Air Side Section',
            'Airport Operation Land Side and Terminal Section',
            'Airport Service Improvement Section',
            'Airport Rescue and Fire Fighting Section',
            'Airport Mechanical Section',
            'Airport Electrical Section',
            'General Services Section',
            'Stakeholder Relation Section',
            'Airport Aeronautical Section',
            'Cargo and Business Development Section',
            'Food and Beverage, Lounge and Services Section',
            'Airport Security Department',
            'Airport Security Protection Section',
            'Airport Security Screening Section',
            'Advertising and Land Side Service Section',
            'Retail and Concession Section',
        ];

        foreach ($units as $index => $unit) {
            DB::table('unit_kerja')->insert([
                'unit_name' => $unit,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
