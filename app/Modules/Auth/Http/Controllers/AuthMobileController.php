<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Akun;

class AuthMobileController
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'device_info' => 'nullable|string',
        ]);

        $akun = Akun::where('username', $request->username)->first();
        if (!$akun || !Hash::check($request->password, $akun->password)) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        // Pastikan hanya role sales yang boleh login mobile (sesuaikan jika kamu pakai numeric)
        // Jika status_login kamu bertipe integer di DB, cast ke int. Jika string, gunakan string.
        // Contoh: status_login '2' artinya mobile allowed.
        if ((string) $akun->status_login !== '2') {
            return response()->json(['message' => 'Tidak diizinkan login via mobile'], 403);
        }
        // Pastikan akun punya relasi karyawan & aktif
        if (! $akun->karyawan || ($akun->karyawan->status ?? null) !== 'Y') {
            return response()->json(['message' => 'Akun tidak aktif'], 403);
        }



        $token = $akun->createToken('mobile-token', ['mobile'])->plainTextToken;

        $akun->update([
            'last_login_at' => now(),
            'device_info' => $request->device_info ?? $akun->device_info,
        ]);

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $akun->id_karyawan,
                'nama' => $akun->karyawan->nama ?? null,
                'jabatan' => $akun->karyawan->jabatan->nama ?? null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}
