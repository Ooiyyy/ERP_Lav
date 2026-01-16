<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksiBahanBakuKeluar extends Model
{
    protected $table = 'detail_bahan_baku_keluar';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_bahan_baku_keluar',
        'id_bahan_baku',
        'jumlah',
        'satuan',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update'
    ];

    // detail → header transaksi
    public function transaksi()
    {
        return $this->belongsTo(BahanBakuKeluar::class, 'id_bahan_baku_keluar');
    }

    // detail → bahan baku
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan_baku');
    }
}
