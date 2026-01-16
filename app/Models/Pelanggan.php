<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'id';
    public $incrementing = false; // karena id bukan auto increment
    protected $keyType = 'string';
    public $timestamps = false; // WAJIB karena pakai kolom custom

    protected $fillable = [
        'id',
        'nama',
        'no_hp',
        'alamat',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // Jika ID belum diisi, generate otomatis
            if (!$model->id) {

                $last = \App\Models\Pelanggan::orderBy('id', 'desc')->first();

                if ($last) {
                    $num = (int)substr($last->id, 4) + 1;
                } else {
                    $num = 1;
                }

                $model->id = 'CUST' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }

            // Isi timestamp
            $model->tanggal_buat = now();
        });

        static::updating(function ($model) {
            $model->tanggal_update = now();
        });
    }

    // Relasi ke tabel toko (1 pelanggan bisa punya banyak toko)
    public function daftarToko()
    {
        return $this->hasMany(Toko::class, 'id_pelanggan', 'id');
    }
}
