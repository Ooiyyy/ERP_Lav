<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    // Nama tabel
    protected $table = 'bahan_baku';

    // Primary key pakai string (BB-001 dst)
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Laravel timestamps dimatikan karena pakai kolom custom
    public $timestamps = false;

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'id',
        'nama',
        'stok_bahan_baku',
        'satuan',
        'deskripsi',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update'
    ];

    // Relasi ke karyawan (pembuat)
    public function dibuatOleh()
    {
        return $this->belongsTo(Karyawan::class, 'id_buat', 'id');
    }

    // Relasi ke karyawan (pengupdate)
    public function diupdateOleh()
    {
        return $this->belongsTo(Karyawan::class, 'id_update', 'id');
    }
    public function bahanMasuk()
    {
        return $this->hasMany(DetailTransaksiBahanBakuMasuk::class, 'id_bahan_baku', 'id');
    }
    public function bahanKeluar()
    {
        return $this->hasMany(DetailTransaksiBahanBakuKeluar::class, 'id_bahan_baku', 'id');
    }
}
