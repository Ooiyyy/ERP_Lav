<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'id';
    public $incrementing = false; // karena id bukan auto increment
    protected $keyType = 'string';
    public $timestamps = false; // karena pakai tanggal_buat dan tanggal_update manual

    protected $fillable = [
        'id',
        'nama',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update',
    ];

    // Relasi ke karyawan (1 jabatan punya banyak karyawan)
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_jabatan', 'id');
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
