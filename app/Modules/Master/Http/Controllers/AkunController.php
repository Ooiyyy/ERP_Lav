<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Karyawan;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class AkunController
{
    public function index()
    {
        $allowedJabatan = ['admin', 'gudang', 'sales'];
        $karyawanAktif = ['Y'];

        $karyawanTanpaAkun = Karyawan::whereDoesntHave('akun')
            ->whereIn('status', $karyawanAktif)   // filter aktif di tabel karyawan
            ->whereHas('jabatan', function ($q) use ($allowedJabatan) {
                $q->whereIn('nama', $allowedJabatan);   // filter jabatan
            })
            ->orderBy('nama')
            ->get();

        return view('master::DaftarAkun', compact('karyawanTanpaAkun'));
    }


    /**
     * Return JSON data for DataTable (client-side).
     * status_akun berasal dari karyawan.status (Y/N).
     */
    public function data()
    {
        $rows = Akun::with('karyawan')->get();
        $data = $rows->map(function ($r) {
            return [
                'id_karyawan'   => $r->id_karyawan,
                'username'      => $r->username,
                'password'      => $r->password,
                'status_login'  => $r->status_login,                  // 1 atau 2
                'status_akun'   => optional($r->karyawan)->status ?? 'N', // Y/N dari karyawan
                'tanggal_buat'  => $r->tanggal_buat ? (string) $r->tanggal_buat : null,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function karyawanTanpaAkun()
    {
        $allowedJabatan = ['superadmin', 'admin', 'gudang', 'sales'];

        $karyawanTanpaAkun = Karyawan::whereDoesntHave('akun')
            ->whereHas('jabatan', function ($q) use ($allowedJabatan) {
                $q->whereIn('nama', $allowedJabatan);
            })
            ->orderBy('nama')
            ->get();

        $data = $karyawanTanpaAkun->map(function ($kta) {
            return [
                'id_karyawan'   => $kta->id,
                'nama'          => $kta->nama,
                'jabatan'       => $kta->jabatan,
            ];
        });
        return response()->json(['data' => $data]);
    }

    /**
     * Create akun (AJAX)
     * expects: id_karyawan, username, password, status_login (1/2), optional status_akun (Y/N but we ignore storing to akun)
     */
    public function simpanAkun(Request $req)
    {
        $v = Validator::make($req->all(), [
            'id_karyawan'  => 'required|string|exists:karyawan,id',
            'username'     => 'required|string|max:100|unique:akun,username',
            'password'     => 'required|string|min:6',
            'status_login' => 'nullable|in:1,2',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }
        $karyawan = Karyawan::with('jabatan')->findOrFail($req->id_karyawan);
        $statusLogin = ($karyawan->id_jabatan === '003') ? '2' : '1';

        $akun = Akun::create([
            'id_karyawan'    => $req->id_karyawan,
            'username'       => $req->username,
            'password'       => Hash::make($req->password),
            'status_login'   => $statusLogin,
            'id_buat'        => auth('web')->id(),
            'tanggal_buat'   => now(),
            'tanggal_update' => now(),
        ]);

        // NOTE: status karyawan (Y/N) tetap di tabel karyawan; tidak kita ubah di sini
        return response()->json(['message' => 'Akun tersimpan.', 'data' => $akun], 201);
    }

    /**
     * Update akun (AJAX)
     */
    public function updateAkun(Request $req, $id)
    {
        $akun = Akun::find($id);
        if (! $akun) {
            return response()->json(['message' => 'Akun tidak ditemukan.'], 404);
        }

        $v = Validator::make($req->all(), [
            'username'     => 'required|string|max:100|unique:akun,username,' . $akun->id_karyawan . ',id_karyawan',
            'password'     => 'nullable|string|min:6',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = [
            'username'       => $req->username,
            'id_update'      => auth('web')->id(),
            'tanggal_update' => now(),
        ];

        if ($req->filled('password')) {
            $data['password'] = Hash::make($req->password);
        }

        $akun->update($data);

        return response()->json(['message' => 'Akun diperbarui.', 'data' => $akun], 200);
    }

    /**
     * Toggle status on Karyawan (Y/N).
     * $id is id_karyawan.
     */
    public function toggleStatus(Request $req, $id)
    {
        $karyawan = Karyawan::find($id);
        if (! $karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan.'], 404);
        }

        // optional: mencegah menonaktifkan diri sendiri
        $current = auth('web')->id();
        if ($current && strval($current) === strval($karyawan->id)) {
            return response()->json(['message' => 'Tidak boleh mengubah status karyawan sendiri.'], 422);
        }

        $v = Validator::make($req->all(), [
            'status' => 'required|in:Y,N'
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        // update kolom status di tabel karyawan
        $karyawan->update([
            'status' => $req->status,
            // jika ada kolom id_update/tanggal_update di karyawan, set juga
            'id_update' => auth('web')->id() ?? $karyawan->id_update,
            'tanggal_update' => now(),
        ]);

        return response()->json(['message' => 'Status karyawan diperbarui.', 'data' => $karyawan], 200);
    }

    /**
     * Delete akun (AJAX). Prevent deleting yourself.
     * NOTE: $id is id_karyawan in akun.id_karyawan
     */
    public function hapusAkun(Request $req, $id)
    {
        $akun = Akun::find($id);
        if (! $akun) {
            return response()->json(['message' => 'Akun tidak ditemukan.'], 404);
        }

        $cekToko = Toko::where('id_karyawan', $id)->exists();
        if ($cekToko) {
            if ($req->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Tidak dapat menghapus karena karyawan ini masih memiliki toko.'], 422);
            }
        }

        $current = auth('web')->id();
        if ($current && strval($current) === strval($akun->id_karyawan)) {
            return response()->json(['message' => 'Tidak boleh menghapus akun sendiri.'], 422);
        }

        $akun->delete();

        return response()->json(['message' => 'Akun berhasil dihapus.'], 200);
    }
}
