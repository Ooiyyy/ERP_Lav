<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Akun extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'akun';
    protected $primaryKey = 'id_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_karyawan',
        'username',
        'password',
        'status_login',
        'id_buat',
        'id_update',
        'tanggal_buat',
        'tanggal_update',
    ];

    // Hidden fields agar password tidak ikut ke JSON response
    protected $hidden = [
        'password',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */
        public function sales()
    {
        return $this->hasMany(Order::class, 'id_sales', 'id');
    }

    // Akun -> Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
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

    /*
    |--------------------------------------------------------------------------
    | HELPER TAMBAHAN
    |--------------------------------------------------------------------------
    */

    // Ambil nama role user dari relasi jabatan
    public function role(): ?string
    {
        return strtolower(optional($this->karyawan->jabatan)->id);
    }

    // Cek apakah user punya role tertentu
    public function hasRole(string $role): bool
    {
        return $this->role() === strtolower($role);
    }

    // Shortcut: cek apakah user aktif
    public function isActive(): bool
    {
        return $this->karyawan && $this->karyawan->status === 'Y';
    }
}

