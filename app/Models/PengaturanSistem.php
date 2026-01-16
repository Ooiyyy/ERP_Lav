<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    protected $table = 'pengaturan_sistem';

    protected $primaryKey = 'id';

    protected $keyType = 'string';
    public $timestamps = false;
    // karena pakai tanggal_buat & tanggal_update manual

    protected $fillable = [
        'id',
        'ketentuan',
        'keterangan',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update',
    ];

    /*
     |--------------------------------------------------------------------------
     | RELATIONS
     |--------------------------------------------------------------------------
     */

    // pembuat (akun)
    public function pembuat()
    {
        return $this->belongsTo(
            \App\Models\Akun::class,
            'id_buat',
            'id_karyawan'
        );
    }

    // pengubah terakhir (akun)
    public function pengubah()
    {
        return $this->belongsTo(
            \App\Models\Akun::class,
            'id_update',
            'id_karyawan'
        );
    }
}
