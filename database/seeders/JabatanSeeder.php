<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jabatan')->insert([
            [
                'id' => '001',
                'nama' => 'superadmin',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
            [
                'id' => '002',
                'nama' => 'admin',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
            [
                'id' => '003',
                'nama' => 'gudang',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
            [
                'id' => '004',
                'nama' => 'sales',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
        ]);
    }
}
