<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = 'order';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = []; // fleksibel, controller proteksi input

    // -------------------------
    // Relasi
    // -------------------------

    public function pembayaranCash()
    {
        return $this->hasMany(\App\Models\PembayaranCash::class, 'id_order', 'id');
    }

    public function pembayaranVa()
    {
        return $this->hasMany(\App\Models\PembayaranVa::class, 'id_order', 'id');
    }

    public function detailOrders()
    {
        return $this->hasMany(\App\Models\DetailOrder::class, 'id_transaksi', 'id');
    }

    /**
     * Relasi ke sales / karyawan
     *
     * Pilih salah satu implementasi di bawah sesuai struktur DB-mu:
     *
     * 1) Jika kolom order.id_sales menyimpan langsung nilai karyawan.id (mis. "K001"),
     *    pakai belongsTo Karyawan (default di-provide oleh kamu).
     *
     * 2) Jika kolom order.id_sales menyimpan id akun (di tabel akun) dan di tabel akun ada kolom id_karyawan,
     *    gunakan belongsTo Akun dengan ownerKey id_karyawan.
     *
     * Uncomment sesuai kebutuhan, lalu hapus/komentari yang lain.
     */

    // ---------- Opsi A: id_sales -> karyawan.id (rekomendasi jika id_sales berformat "Kxxx") ----------
    public function sales()
    {
        return $this->belongsTo(\App\Models\Karyawan::class, 'id_sales', 'id');
    }

    /* ---------- Opsi B: id_sales -> akun.id_karyawan (pakai jika id_sales menyimpan akun id) ----------
    public function sales()
    {
        return $this->belongsTo(\App\Models\Akun::class, 'id_sales', 'id_karyawan');
    }
    */

    public function toko()
    {
        return $this->belongsTo(\App\Models\Toko::class, 'id_toko', 'id');
    }

    // -------------------------
    // Helper perhitungan
    // -------------------------

    /**
     * Hitung total runtime dari detail_orders (prioritaskan subtotal jika ada)
     * Mengembalikan integer
     */
    public function totalAmount()
    {
        if ($this->relationLoaded('detailOrders')) {
            return (int) $this->detailOrders->sum(function ($d) {
                return (int)($d->subtotal ?? (((int)($d->jumlah ?? 0)) * ((int)($d->harga ?? 0))));
            });
        }

        return (int) DB::table('detail_order')
            ->where('id_transaksi', $this->id)
            ->select(DB::raw('COALESCE(SUM(COALESCE(subtotal, jumlah * harga)),0) as tot'))
            ->value('tot') ?: 0;
    }

    /**
     * Jumlah pembayaran yang sudah masuk (cash + va)
     */
    public function paidAmount()
    {
        $paidCash = (int) DB::table('pembayaran_cash')->where('id_order', $this->id)->sum('nominal');
        $paidVa = (int) DB::table('pembayaran_va')->where('id_order', $this->id)->sum('nominal');
        return $paidCash + $paidVa;
    }

    /**
     * Sisa tagihan = total - paid
     */
    public function remainingAmount()
    {
        return max(0, $this->totalAmount() - $this->paidAmount());
    }
}
