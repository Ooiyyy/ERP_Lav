<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Toko;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function Symfony\Component\Clock\now;

class PelangganController
{
    public function index()
    {
        $pelanggan = Pelanggan::all();

        return view('master::DaftarPelanggan', compact('pelanggan'));
    }

    public function data()
    {
        $rows = Pelanggan::query()
            ->leftJoin('karyawan as pembuat', 'pembuat.id', '=', 'pelanggan.id_buat')
            ->leftJoin('karyawan as pengubah', 'pengubah.id', '=', 'pelanggan.id_update')
            ->select([
                'pelanggan.*',
                'pembuat.nama as nama_pembuat',
                'pengubah.nama as nama_pengubah',
            ])
            ->get();

        $data = $rows->map(function ($r) {
            // pelanggan.* sudah kebawa; tambahkan dua field ini saja
            $r->nama_pembuat = $r->nama_pembuat ?? '-';
            $r->nama_pengubah = $r->nama_pengubah ?? '-';
            return $r;
        });

        return response()->json(['data' => $data]);
    }
    public function simpanPelanggan(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'nama' => 'required|max:100',
            'no_hp' => 'required|max:13',
            'alamat' => 'required',
            'id_buat'
        ]);

        $idpelanggan = $validated['id'];

        $pelanggan = Pelanggan::create([
            'id' => $idpelanggan,
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            'id_buat' => auth('web')->id(),
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Pelanggan berhasil ditambahkan.', 'data' => $pelanggan], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function updatePelanggan(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $request->validate([
            'nama' => 'required|max:100',
            'no_hp' => 'required|max:13',
            'alamat' => 'required',
            'id_update',
            'tanggal_update'
        ]);

        $pelanggan->update([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'id_update' => auth('web')->id(),
            'tanggal_update' => now()
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Karyawan berhasil diupdate.', 'data' => $pelanggan], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil diupdate.');
    }

    public function hapusPelanggan(Request $request, $id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Data karyawan tidak ditemukan.'], 404);
            }
            return redirect()->route('akun.karyawan')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $cekToko = Toko::where('id_pelanggan', $id)->exists();
        if ($cekToko) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Tidak dapat menghapus karena pelanggan ini masih memiliki toko.'], 422);
            }
            // return redirect()->route('akun.karyawan')->with('error', 'Tidak dapat menghapus karena karyawan ini masih memiliki akun.');
        }

        $pelanggan->delete();

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Pelanggan berhasil dihapus.'], 200);
        }

        // return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil dihapus.');
    }
}
