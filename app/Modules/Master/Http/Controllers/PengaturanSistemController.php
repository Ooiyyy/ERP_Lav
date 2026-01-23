<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Toko;
use App\Models\PengaturanSistem;
use App\Models\Supplier;
use App\Models\Pelanggan;
use App\Models\BahanBakuMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function Symfony\Component\Clock\now;

class PengaturanSistemController
{
    public function index()
    {
        $Toko = Supplier::all();

        return view('master::PengaturanSistem', compact('Toko'));
    }

    public function dt()
    {
        $rows = PengaturanSistem::query()
            ->leftJoin('karyawan as pembuat', 'pembuat.id', '=', 'pengaturan_sistem.id_buat')
            ->leftJoin('karyawan as pengubah', 'pengubah.id', '=', 'pengaturan_sistem.id_update')
            ->select([
                'pengaturan_sistem.id',
                'pengaturan_sistem.ketentuan',
                'pengaturan_sistem.keterangan',
                'pembuat.nama as nama_pembuat',
                'pengubah.nama as nama_pengubah',
            ])
            ->get();

        $data = $rows->map(function ($t) {
            return [
                'id' => $t->id,
                'ketentuan' => $t->ketentuan,
                'keterangan' => $t->keterangan,
                'nama_pembuat' => $t->nama_pembuat ?? '-',
                'nama_pengubah' => $t->nama_pengubah ?? '-',
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    public function simpanPengaturanSistem(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'ketentuan' => 'required|max:100',
            'keterangan' => 'required',
        ]);

        $idKonfigurasi = $validated['id'];

        $Konfigurasi = PengaturanSistem::create([
            'id' => $idKonfigurasi,
            'ketentuan' => $validated['ketentuan'],
            'keterangan' => $validated['keterangan'],
            'id_buat' => auth('web')->id(),
            'tanggal_buat' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Pengaturan Sistem berhasil ditambahkan.', 'data' => $Konfigurasi], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function updatePengaturanSistem(Request $request, $id)
    {
        $Konfigurasi = PengaturanSistem::findOrFail($id);

        $request->validate([
            'ketentuan' => 'required|max:100',
            'keterangan' => 'required',
        ]);

        $Konfigurasi->update([

            'ketentuan' => $request->ketentuan,
            'keterangan' => $request->keterangan,
            'id_update' => auth('web')->id(),
            'tanggal_update' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Pengaturan Sistem berhasil diupdate.', 'data' => $Konfigurasi], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil diupdate.');
    }

    public function hapusPengaturanSistem(Request $request, $id)
    {
        $Konfigurasi = PengaturanSistem::find($id);

        if (!$Konfigurasi) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Data PengaturanSistem tidak ditemukan.'], 404);
            }
            return redirect()->route('akun.karyawan')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $Konfigurasi->delete();

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Pengaturan Sistem berhasil dihapus.'], 200);
        }

        // return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil dihapus.');
    }
}
