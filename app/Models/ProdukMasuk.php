<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukMasuk extends Model
{
    protected $table = 'produk_masuk';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_karyawan_gudang',
        'id_karyawan_produksi',
        'tanggal_transaksi',
        'id_buat',
        'tanggal_buat'
    ];

    public function detail()
    {
        return $this->hasMany(DetailProdukMasuk::class, 'id_produk_masuk');
    }
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_karyawan_gudang');
    }
}
