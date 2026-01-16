<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    protected $table = 'detail_order';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    // hapus 'subtotal' karena kolom sudah di-drop dari DB
    protected $fillable = [
        'id',
        'id_transaksi',
        'id_produk',
        'jumlah',
        'harga',
        'satuan',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga'  => 'integer',
    ];

    /**
     * Append virtual attribute 'subtotal' sehingga $detail->subtotal tetap dapat dipanggil
     * meski kolom fisiknya sudah dihapus.
     */
    protected $appends = ['subtotal'];

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'id_transaksi', 'id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Produk::class, 'id_produk', 'id');
    }

    /**
     * Accessor: hitung subtotal secara dinamis (jumlah * harga).
     * Dipanggil saat mengakses $detail->subtotal.
     */
    public function getSubtotalAttribute()
    {
        $jumlah = (int) ($this->attributes['jumlah'] ?? 0);
        $harga  = (int) ($this->attributes['harga'] ?? 0);
        return $jumlah * $harga;
    }
}
