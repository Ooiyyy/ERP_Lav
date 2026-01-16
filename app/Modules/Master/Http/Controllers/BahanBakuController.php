<?php

namespace App\Modules\Master\Http\Controllers;

use id;
use App\Models\BahanBaku;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BahanBakuController
{
    // Tampilkan halaman
    public function index()
    {
        return view('master::DaftarBahanBaku'); // atau folder/filename sesuai struktur kamu
    }

    // Data untuk DataTables
    public function data()
    {
        $rows = BahanBaku::orderBy('id')->get();

        $data = $rows->map(function ($r) {
            return [
                'id' => $r->id,
                'nama' => $r->nama,
                'stok_bahan_baku' => $r->stok_bahan_baku,
                'satuan' => $r->satuan,
                'deskripsi' => $r->deskripsi,
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }

    // Simpan bahan baku baru
    public function simpanBahanBaku(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required|string|max:50|unique:bahan_baku,id',
            'nama' => 'required|string|max:100',
            'stok_bahan_baku' => 'required|integer',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = new BahanBaku();
        $data->id = $request->id;
        $data->nama = $request->nama;
        $data->stok_bahan_baku = (int) $request->stok_bahan_baku;
        $data->satuan = $request->satuan;
        $data->deskripsi = $request->deskripsi;
        $data->tanggal_buat = now();
        $data->tanggal_update = now();
        $data->save();

        return response()->json(['message' => 'Bahan baku berhasil disimpan.']);
    }

    // Update bahan baku
    public function updateBahanBaku(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'stok_bahan_baku' => 'required|integer',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $item = BahanBaku::find($id);
        if (!$item) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $item->nama = $request->nama;
        $item->stok_bahan_baku = (int) $request->stok_bahan_baku;
        $item->satuan = $request->satuan;
        $item->deskripsi = $request->deskripsi;
        $item->tanggal_update = now();
        $item->save();

        return response()->json(['message' => 'Bahan baku berhasil diperbarui.']);
    }

    // Hapus bahan baku
    public function hapusBahanBaku($id)
    {
        $item = BahanBaku::find($id);
        if (!$item) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }
        if ($item->bahanMasuk()->exists()) {
            return response()->json(['message' => 'Tidak dapat menghapus: masih ada Bahan baku yang tersimpan di Bahan masuk.'], 422);
        }
        if ($item->bahanKeluar()->exists()) {
            return response()->json(['message' => 'Tidak dapat menghapus:  masih ada Bahan baku yang tersimpan di Bahan keluar.'], 422);
        }
        $item->delete();

        return response()->json(['message' => 'Bahan baku berhasil dihapus.']);
    }
}
