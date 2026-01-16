<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AkunSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('akun')->insert([
            [
                'id_karyawan' => 'K001',
                'username' => 'superadmin',
                'password' => Hash::make('superadmin123'),
                'status_login' => '1',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,

            ],
            [
                'id_karyawan' => 'K002',
                'username' => 'sales',
                'password' => Hash::make('sales123'),
                'status_login' => '2',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
            [
                'id_karyawan' => 'K004',
                'username' => 'Dinday',
                'password' => Hash::make('sayangku'),
                'status_login' => '1',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
            [
                'id_karyawan' => 'K003',
                'username' => 'gudang',
                'password' => Hash::make('gudang123'),
                'status_login' => '1',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
        ]);
    }
}
