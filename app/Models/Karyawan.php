<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_jabatan',
        'nama',
        'alamat',
        'jenis_kelamin',
        'no_hp',
        'status',
        'tempat_lahir',
        'tanggal_lahir',
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

                // Ambil id paling besar, contoh: K001, K002
                $last = \App\Models\Karyawan::orderBy('id', 'desc')->first();

                if ($last) {
                    // Ambil angka saja: dari "K012" → 12
                    $num = (int)substr($last->id, 1) + 1;
                } else {
                    $num = 1;
                }

                // Buat ID baru dengan prefix K
                $model->id = 'K' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }

            // Isi timestamp
            $model->tanggal_buat = now();
            $model->tanggal_update = now();
        });

        static::updating(function ($model) {
            $model->tanggal_update = now();
        });
    }

    // Relasi ke jabatan utama
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id');
    }

    // Relasi ke akun (satu karyawan punya satu akun)
    public function akun()
    {
        return $this->hasOne(Akun::class, 'id_karyawan', 'id');
    }

    // Relasi ke akun pembuat data
    public function pembuat()
    {
        return $this->belongsTo(Akun::class, 'id_buat', 'id_karyawan');
    }

    // Relasi ke akun pengubah data
    public function pengubah()
    {
        return $this->belongsTo(Akun::class, 'id_update', 'id_karyawan');
    }

}
