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
        DB::table('users')->insert([
            [
                'nip'            => '1283014-C',
                'unit_kerja'     => 'Airport Operation Air Side Section',
                'name'           => 'Super Administrator',
                'username'       => 'superadmin',
                'email'          => 'superadmin@example.com',
                'password'       => Hash::make('12345678'),
                'role'           => 'super_admin',
                'is_active'      => true,
                'last_login_at'  => null,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'nip'            => '1289115-R',
                'unit_kerja'     => 'Airport Operation Land Side and Terminal Section',
                'name'           => 'Admin System',
                'username'       => 'admin',
                'email'          => 'admin@example.com',
                'password'       => Hash::make('12345678'),
                'role'           => 'admin',
                'is_active'      => true,
                'last_login_at'  => null,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'nip'            => '9981169-EMP',
                'unit_kerja'     => 'Airport Rescue and Fire Fighting Section',
                'name'           => 'Employee Demo',
                'username'       => 'employee',
                'email'          => 'employee@example.com',
                'password'       => Hash::make('12345678'),
                'role'           => 'employee',
                'is_active'      => true,
                'last_login_at'  => null,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
        ]);
    }
}
