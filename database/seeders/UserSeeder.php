<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $unitKerja = DB::table('unit_kerja')
            ->where('unit_name', '=', 'Airport Operation Air Side Section')
            ->first();

        if (! $unitKerja) {
            throw new \Exception('Unit Kerja "IT Support" belum ada.');
        }

        DB::table('users')->insert([
            [
                'nip'            => '1283014-C',
                'id_unit_kerja'  => $unitKerja->id,
                'name'           => 'Super Administrator',
                'email'          => 'superadmin@example.com',
                'password'       => Hash::make('12345678'),
                'role'           => 'super_admin',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'nip'            => '1289115-R',
                'id_unit_kerja'  => $unitKerja->id,
                'name'           => 'Admin System',
                'email'          => 'admin@example.com',
                'password'       => Hash::make('12345678'),
                'role'           => 'admin',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'nip'            => '9981169-EMP',
                'id_unit_kerja'  => $unitKerja->id,
                'name'           => 'Employee Demo',
                'email'          => 'employee@example.com',
                'password'       => Hash::make('12345678'),
                'role'           => 'employee',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
        ]);
    }
}
