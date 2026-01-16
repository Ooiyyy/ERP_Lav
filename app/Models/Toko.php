<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // WAJIB karena pakai kolom custom

    protected $fillable = [
        'id',
        'id_karyawan',
        'id_pelanggan',
        'nama_toko',
        'alamat',
        'lintang',
        'bujur',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update',
    ];

    public function salesPembawa()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }

    public function CPtoko()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id');
    }
}
