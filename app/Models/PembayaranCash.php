<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranCash extends Model
{
    protected $table = 'pembayaran_cash';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false; // table uses tanggal_pembayaran / tanggal_buat if needed

    protected $fillable = [
        'id',
        'id_order',
        'nominal',
        'petugas_pembayaran',
        'tanggal_pembayaran',
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
