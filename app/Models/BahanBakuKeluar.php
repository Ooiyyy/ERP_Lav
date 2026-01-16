<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBakuKeluar extends Model
{
    protected $table = 'bahan_baku_keluar';
    protected $primaryKey = 'id';
    public $incrementing = false; 
    protected $keyType = 'string';
    public $timestamps = false; 

    protected $fillable = [
        'id',
        'id_karyawan_gudang',
        'tanggal_transaksi',
        'keterangan',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update'
    ];

    // Relasi: 1 transaksi keluar memiliki banyak detail
    public function detail()
    {
        return $this->hasMany(DetailTransaksiBahanBakuKeluar::class, 'id_bahan_baku_keluar');
    }

    // Relasi ke karyawan gudang (opsional, kalau ada modelnya)
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id');
    }
}
