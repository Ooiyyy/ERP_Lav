<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Toko;
use App\Models\Supplier;
use App\Models\Pelanggan;
use App\Models\BahanBakuMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function Symfony\Component\Clock\now;

class SupplierController
{
    public function index()
    {
        $Toko = Supplier::all();

        return view('master::DaftarSupplier', compact('Toko'));
    }

    public function dt() {
        $supplier = Supplier::all();

        $data = $supplier->map(function ($t) {
            return [
                'id'             => $t->id,
                'nama_toko'      => $t->nama_toko,
                'alamat'         => $t->alamat,
            ];
        });
    
        return response()->json(compact('data'));
    }

    // public function data()
    // {
    //     // Eager-load relasi supaya bisa ambil nama sales & nama pelanggan
    //     $list = Toko::with([
    //         'salesPembawa',
    //         'CPtoko'
    //     ])->get();

    //     // Map supaya response per baris lebih sederhana untuk DataTables
    //     $data = $list->map(function ($t) {
    //         return [
    //             'id'             => $t->id,
    //             'id_karyawan'    => $t->id_karyawan,
    //             'id_pelanggan'   => $t->id_pelanggan,
    //             'nama_toko'      => $t->nama_toko,
    //             'alamat'         => $t->alamat,
    //             'lintang'        => $t->lintang,
    //             'bujur'          => $t->bujur,
    //             'nama_karyawan'  => $t->salesPembawa->nama ?? '-',
    //             'nama_pelanggan' => $t->CPtoko->nama ?? '-',
    //         ];
    //     });

    //     // Daftar untuk dropdown (sales dan pelanggan)
    //     $sales = Karyawan::where('id_jabatan', '004')->select('id', 'nama')->get();
    //     $pelanggan = Pelanggan::select('id', 'nama')->get();

    //     // Kembalikan JSON: { data: [...], sales: [...], pelanggan: [...] }
    //     return response()->json(compact('data', 'sales', 'pelanggan'));
    // }


    public function simpanSupplier(Request $request)
    {
        $validated = $request->validate([
            'id'            => 'required',
            'nama_toko'          => 'required|max:100',
            'alamat'        => 'required',
        ]);

        $idToko = $validated['id'];

        $Toko = Supplier::create([
            'id'            => $idToko,
            'nama_toko'          => $validated['nama_toko'],
            'alamat'        => $validated['alamat'],
            'id_buat'       => auth('web')->id(),
            'tanggal_buat'  =>now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Supplier berhasil ditambahkan.', 'data' => $Toko], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function updateSupplier(Request $request, $id)
    {
        $Toko = Supplier::findOrFail($id);

        $request->validate([
            'nama_toko'          => 'required|max:100',
            'alamat'        => 'required',
        ]);

        $Toko->update([

            'nama_toko'     =>$request->nama_toko,     
            'alamat'        =>$request->alamat,   
            'id_update'     => auth('web')->id(),
            'tanggal_update' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Supplier berhasil diupdate.', 'data' => $Toko], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil diupdate.');
    }

    public function hapusSupplier(Request $request, $id)
    {
        $Toko = Supplier::find($id);

        if (! $Toko) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Data Supplier tidak ditemukan.'], 404);
            }
            return redirect()->route('akun.karyawan')->with('error', 'Data karyawan tidak ditemukan.');
        }

        if ($Toko->bahanMasuk()->exists()) {
            return response()->json(['message' => 'Tidak dapat menghapus: masih ada Bahan masuk yang tersimpan ini.'], 422);
        }

        $Toko->delete();

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Supplier berhasil dihapus.'], 200);
        }

        // return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil dihapus.');
    }
}
