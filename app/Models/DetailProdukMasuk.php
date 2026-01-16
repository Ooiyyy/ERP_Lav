<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProdukMasuk extends Model
{
    protected $table = 'detail_produk_masuk';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_produk_masuk',
        'id_produk',
        'jumlah',
        'satuan',
        'nominal',
    ];

    public function produk(){
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
