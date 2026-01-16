<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('karyawan')->insert([
            [
                'id' => 'K001',
                'id_jabatan' => '001',
                'nama' => 'Admin Utama',
                'alamat' => 'Jombang',
                'jenis_kelamin' => 'Laki-laki',
                'no_hp' => '081234567890',
                'status' => 'Y',
                'tempat_lahir' => 'Jombang',
                'tanggal_lahir' => '2000-01-01',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
            [
                'id' => 'K002',
                'id_jabatan' => '002',
                'nama' => 'Karyawan Sales',
                'alamat' => 'Surabaya',
                'jenis_kelamin' => 'Perempuan',
                'no_hp' => '081234567891',
                'status' => 'N',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '2001-02-02',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
            [
                'id' => 'K004',
                'id_jabatan' => '001',
                'nama' => 'Dindayyy',
                'alamat' => 'Jombang',
                'jenis_kelamin' => 'Perempuan',
                'no_hp' => '081234567891',
                'status' => 'Y',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '2007-02-21',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
            [
                'id' => 'K003',
                'id_jabatan' => '003',
                'nama' => 'Karyawan Gudang',
                'alamat' => 'Mojokerto',
                'jenis_kelamin' => 'Laki-laki',
                'no_hp' => '081234567892',
                'status' => 'Y',
                'tempat_lahir' => 'Mojokerto',
                'tanggal_lahir' => '2002-03-03',
                'tanggal_buat' => now(),
                'id_buat'   => null,
                'id_update' => null,
            ],
        ]);
    }
}
