<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // WAJIB karena pakai kolom custom

    protected $fillable = [
        'id',
        'nama_toko',
        'alamat',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update',
    ];

    public function bahanMasuk()
    {
        return $this->hasMany(BahanBakuMasuk::class, 'id_toko', 'id');
    }
}
