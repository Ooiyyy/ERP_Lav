<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Akun;

class AuthWebController
{
    public function showLoginForm()
    {
        if (auth('web')->check()) {
            return redirect()->route('master::beranda');
        }

        return view('login');
    }
    // public function showLoginForm()
    // {
    //     return view('login'); // sesuaikan path view jika perlu
    // }

    public function authenticate(Request $request)
    {
        // validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // cari akun berdasarkan username (case-insensitive bila perlu)
        $akun = Akun::where('username', $username)->first();

        // jangan spill informasi apakah username ada/tidak — kembalikan message generik
        if (! $akun) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }

        // cek password dulu (lebih aman daripada cek role terlebih dahulu)
        if (! Hash::check($password, $akun->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }

        // cek apakah akun diizinkan login via web berdasarkan kolom status_login
        // catatan: sesuaikan pengecekan dengan tipe field (string / integer)
        // misal: 1 = web, 2 = mobile
        $statusLogin = $akun->status_login; // ini sudah benar (bukan $akun->akun)
        if ((string)$statusLogin !== '1') {
            // Bisa juga gunakan integer check: if ((int)$statusLogin !== 1) { ... }
            return back()->withErrors(['username' => 'Tidak diizinkan login via web.'])->onlyInput('username');
        }

        // pastikan karyawan terhubung dan status aktif (jika relevan)
        if (! $akun->karyawan || ($akun->karyawan->status ?? null) !== 'Y') {
            return back()->withErrors(['username' => 'Akun tidak aktif '])->onlyInput('username');
        }


        // login
        Auth::guard('web')->login($akun);
        $request->session()->regenerate();

        // ambil id_jabatan
        $jabatan = $akun->karyawan->id_jabatan;

        // arahkan berdasarkan jabatan
        if ($jabatan === '001') {
            return redirect('/beranda-admin');      // admin
        }

        if ($jabatan === '002') {
            return redirect('/gudang');       // gudang
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
