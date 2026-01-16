<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksiBahanBakuMasuk extends Model
{
    protected $table = 'detail_bahan_baku_masuk';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_bahan_baku_masuk',
        'id_bahan_baku',
        'jumlah',
        'nominal',
        'satuan',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update'
    ];

    // detail -> header transaksi
    public function transaksi()
    {
        return $this->belongsTo(BahanBakuMasuk::class, 'id_bahan_baku_masuk');
    }

    // detail -> bahan baku
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan_baku');
    }
}
