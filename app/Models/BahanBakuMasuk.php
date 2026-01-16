<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBakuMasuk extends Model
{
    protected $table = 'bahan_baku_masuk';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_karyawan_gudang',
        'tanggal_transaksi',
        'id_toko',
        'no_nota',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update'
    ];

    // Relasi: 1 transaksi punya banyak detail
    public function detail()
    {
        return $this->hasMany(DetailTransaksiBahanBakuMasuk::class, 'id_bahan_baku_masuk');
    }
    // public function toko()
    // {
    //     return $this->belongsTo(Toko::class, 'id_toko', 'id');
    //     // id_toko FK -> toko.id
    // }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_toko', 'id');
        // id_toko FK -> toko.id
    }

    // Relasi ke karyawan gudang (jika ada modelnya)
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id');
    }
}
