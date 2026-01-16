<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nama',
        'foto',
        'harga',
        'satuan',
        'deskripsi',
        'stok',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update'
    ];

    public function dibuatOleh()
    {
        return $this->belongsTo(\App\Models\Karyawan::class, 'id_buat', 'id');
    }

    public function diupdateOleh()
    {
        return $this->belongsTo(\App\Models\Karyawan::class, 'id_update', 'id');
    }

    public function pesanan()
    {
        return $this->hasMany(\App\Models\DetailOrder::class, 'id_produk', 'id');
    }
}
