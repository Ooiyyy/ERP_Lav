<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JabatanController
{
    // tampilkan halaman
    public function index()
    {
        return view('master::DaftarJabatan');
    }

    public function data(Request $req)
    {
        $rows = Jabatan::query()
            ->leftJoin('karyawan as pembuat', 'pembuat.id', '=', 'jabatan.id_buat')
            ->leftJoin('karyawan as pengubah', 'pengubah.id', '=', 'jabatan.id_update')
            ->select([
                'jabatan.id',
                'jabatan.nama',
                'jabatan.tanggal_buat',
                'pembuat.nama as nama_pembuat',
                'pengubah.nama as nama_pengubah',
            ])
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'nama' => $r->nama,
                    'tanggal_buat' => $r->tanggal_buat ? (string) $r->tanggal_buat : null,
                    'nama_pembuat' => $r->nama_pembuat ?? '-',
                    'nama_pengubah' => $r->nama_pengubah ?? '-',
                ];
            });

        return response()->json(['data' => $rows]);
    }

    // simpan (AJAX)
    public function simpanJabatan(Request $req)
    {
        $v = Validator::make($req->all(), [
            'id' => 'nullable|string|max:25|unique:jabatan,id',
            'nama' => 'required|string|max:191',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        // generate id jika kosong
        $id = $req->input('id');
        if (!$id) {
            $last = Jabatan::orderBy('id', 'desc')->first();
            if ($last && preg_match('/(\d+)$/', $last->id, $m)) {
                $num = intval($m[1]) + 1;
            } else {
                $num = 1;
            }
            $id = str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        $jab = Jabatan::create([
            'id' => $id,
            'nama' => $req->input('nama'),
            'id_buat' => auth('web')->id(),
            'tanggal_buat' => now(),
            'tanggal_update' => now(),
        ]);

        return response()->json(['message' => 'Jabatan tersimpan.', 'data' => $jab], 201);
    }

    // update (AJAX)
    public function updateJabatan(Request $req, $id)
    {
        $jab = Jabatan::find($id);
        if (!$jab) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $v = Validator::make($req->all(), [
            'nama' => 'required|string|max:191',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $jab->update([
            'nama' => $req->input('nama'),
            'id_update' => auth('web')->id(),
            'tanggal_update' => now(),
        ]);

        return response()->json(['message' => 'Jabatan diperbarui.', 'data' => $jab], 200);
    }

    // hapus (AJAX) — cek relasi karyawan & akun
    public function hapusJabatan(Request $req, $id)
    {
        $jab = Jabatan::find($id);
        if (!$jab) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        if ($jab->karyawan()->exists()) {
            return response()->json(['message' => 'Tidak dapat menghapus: masih ada karyawan pada jabatan ini.'], 422);
        }

        $jab->delete();

        return response()->json(['message' => 'Jabatan berhasil dihapus.'], 200);
    }
}
