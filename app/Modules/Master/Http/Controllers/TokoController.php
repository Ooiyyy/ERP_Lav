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

class TokoController
{
    public function index()
    {
        $Toko = Toko::all();
        // Daftar untuk dropdown (sales dan pelanggan)
        $sales = Karyawan::where('id_jabatan', '003')->select('id', 'nama')->get();
        $pelanggan = Pelanggan::select('id', 'nama')->get();

        return view('master::DaftarToko', compact('Toko', 'sales', 'pelanggan'));
    }

    public function data()
    {
        // Ambil toko + relasi, sekaligus join pembuat/pengubah
        $list = Toko::query()
            ->with(['salesPembawa', 'CPtoko'])
            ->leftJoin('karyawan as pembuat', 'pembuat.id', '=', 'toko.id_buat')
            ->leftJoin('karyawan as pengubah', 'pengubah.id', '=', 'toko.id_update')
            ->select([
                'toko.*',
                'pembuat.nama as nama_pembuat',
                'pengubah.nama as nama_pengubah',
            ])
            ->get();

        $data = $list->map(function ($t) {
            return [
                'id' => $t->id,
                'id_karyawan' => $t->id_karyawan,
                'id_pelanggan' => $t->id_pelanggan,
                'nama_toko' => $t->nama_toko,
                'alamat' => $t->alamat,
                'lintang' => $t->lintang,
                'bujur' => $t->bujur,
                'nama_karyawan' => optional($t->salesPembawa)->nama ?? '-',
                'nama_pelanggan' => optional($t->CPtoko)->nama ?? '-',

                'nama_pembuat' => $t->nama_pembuat ?? '-',
                'nama_pengubah' => $t->nama_pengubah ?? '-',
            ];
        });

        // Dropdown (sales dan pelanggan) tetap
        $sales = Karyawan::where('id_jabatan', '004')->select('id', 'nama')->get();
        $pelanggan = Pelanggan::select('id', 'nama')->get();

        return response()->json(compact('data', 'sales', 'pelanggan'));
    }



    public function simpanToko(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'id_karyawan' => 'required',
            'id_pelanggan' => 'required',
            'nama_toko' => 'required|max:100',
            'alamat' => 'required',
            'lintang' => 'required',
            'bujur' => 'required',
            'id_buat',
            'tanggal_buat',
        ]);

        $idToko = $validated['id'];

        $Toko = Toko::create([
            'id' => $idToko,
            'id_karyawan' => $validated['id_karyawan'],
            'id_pelanggan' => $validated['id_pelanggan'],
            'nama_toko' => $validated['nama_toko'],
            'alamat' => $validated['alamat'],
            'lintang' => $validated['lintang'],
            'bujur' => $validated['bujur'],
            'id_buat' => auth('web')->id(),
            'tanggal_buat' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Toko berhasil ditambahkan.', 'data' => $Toko], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function updateToko(Request $request, $id)
    {
        $Toko = Toko::findOrFail($id);

        $request->validate([
            'id_karyawan' => 'required',
            'id_pelanggan' => 'required',
            'nama_toko' => 'required|max:100',
            'alamat' => 'required',
            'lintang' => 'required',
            'bujur' => 'required',
            'id_update',
            'tanggal_update',
        ]);

        $Toko->update([

            'id_karyawan' => $request->id_karyawan,
            'id_pelanggan' => $request->id_pelanggan,
            'nama_toko' => $request->nama_toko,
            'alamat' => $request->alamat,
            'lintang' => $request->lintang,
            'bujur' => $request->bujur,
            'id_update' => auth('web')->id(),
            'tanggal_update' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Toko berhasil diupdate.', 'data' => $Toko], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil diupdate.');
    }

    public function hapusToko(Request $request, $id)
    {
        $Toko = Toko::find($id);

        if (!$Toko) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Data karyawan tidak ditemukan.'], 404);
            }
            return redirect()->route('akun.karyawan')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $Toko->delete();

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Toko berhasil dihapus.'], 200);
        }

        // return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil dihapus.');
    }
}
