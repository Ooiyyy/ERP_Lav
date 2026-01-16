<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranVa extends Model
{
    protected $table = 'pembayaran_va';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_order',
        'nominal',
        'channel',
        'tanggal_transaksi',
        'payment_link',
        'nomor_ref',
        'status',              // 🔥 WAJIB
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update',
    ];

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'id_order', 'id');
    }
}
