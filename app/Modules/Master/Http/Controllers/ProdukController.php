<?php

namespace App\Modules\Master\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProdukController
{
    public function index()
    {
        return view('master::DaftarProduk'); // file: resources/views/produk/index.blade.php
    }

    public function data()
    {
        $rows = Produk::orderBy('id')->get();

        $data = $rows->map(function ($r) {
            return [
                'id' => $r->id,
                'nama' => $r->nama,
                'foto' => $r->foto, // relative path produk/{id}/1.jpg
                'harga' => $r->harga,
                'satuan' => $r->satuan,
                'deskripsi' => $r->deskripsi,
                'stok' => $r->stok,
            ];
        });

        return response()->json(['data' => $data]);
    }

    // Optional server-side ID generator (PRD-001)
    public function getNewId()
    {
        $last = Produk::orderBy('id', 'desc')->first();
        if (!$last) {
            $next = 1;
        } else {
            $parts = explode('-', $last->id);
            $num = intval(end($parts));
            $next = $num + 1;
        }
        $idBaru = 'PRD-' . str_pad($next, 3, '0', STR_PAD_LEFT);
        return response()->json(['id_baru' => $idBaru]);
    }

    public function simpanProduk(Request $request)
    {
        $rules = [
            'id' => 'required|string|max:25|unique:produk,id',
            'nama' => 'required|string|max:100',
            'harga' => 'required|integer',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer',
            'foto1' => 'nullable|file|mimes:jpg,jpeg|max:1024',
            'foto2' => 'nullable|file|mimes:jpg,jpeg|max:1024',
            'foto3' => 'nullable|file|mimes:jpg,jpeg|max:1024',
        ];

        $messages = [
            'foto1.mimes' => 'Foto 1 harus berformat JPG.',
            'foto2.mimes' => 'Foto 2 harus berformat JPG.',
            'foto3.mimes' => 'Foto 3 harus berformat JPG.',
            'foto1.max' => 'Ukuran foto 1 maksimal 1MB.',
            'foto2.max' => 'Ukuran foto 2 maksimal 1MB.',
            'foto3.max' => 'Ukuran foto 3 maksimal 1MB.',
        ];

        $v = Validator::make($request->all(), $rules, $messages);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        // sanitize id (hindari path traversal)
        $id = preg_replace('/[^A-Za-z0-9\-_]/', '', $request->id);

        $p = new Produk();
        $p->id = $id;
        $p->nama = $request->nama;
        $p->harga = (int) $request->harga;
        $p->satuan = $request->satuan;
        $p->deskripsi = $request->deskripsi;
        $p->stok = (int) $request->stok;
        $p->tanggal_buat = now();
        $p->tanggal_update = now();
        $p->foto = null; // set after storing file1
        $p->save();

        $disk = 'public';
        $baseDir = 'produk/' . $p->id;

        // create directory explicitly (kontrol visibility)
        Storage::disk($disk)->makeDirectory($baseDir);
        Storage::disk($disk)->setVisibility($baseDir, 'public');

        for ($i = 1; $i <= 3; $i++) {
            $field = 'foto' . $i;
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $i . '.jpg';
                $file->storeAs($baseDir, $filename, $disk);

                if ($i === 1) {
                    // store relative path
                    $p->foto = $baseDir . '/' . $filename;
                    $p->save();
                }
            }
        }

        return response()->json(['message' => 'Produk berhasil disimpan.']);
    }

    public function updateProduk(Request $request, $id)
    {
        $p = Produk::find($id);
        if (!$p) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $rules = [
            'nama' => 'required|string|max:100',
            'harga' => 'required|integer',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer',
            'foto1' => 'nullable|file|mimes:jpg,jpeg|max:1024',
            'foto2' => 'nullable|file|mimes:jpg,jpeg|max:1024',
            'foto3' => 'nullable|file|mimes:jpg,jpeg|max:1024',
        ];

        $messages = [
            'foto1.mimes' => 'Foto 1 harus berformat JPG.',
            'foto2.mimes' => 'Foto 2 harus berformat JPG.',
            'foto3.mimes' => 'Foto 3 harus berformat JPG.',
            'foto1.max' => 'Ukuran foto 1 maksimal 1MB.',
            'foto2.max' => 'Ukuran foto 2 maksimal 1MB.',
            'foto3.max' => 'Ukuran foto 3 maksimal 1MB.',
        ];

        $v = Validator::make($request->all(), $rules, $messages);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $p->nama = $request->nama;
        $p->harga = (int) $request->harga;
        $p->satuan = $request->satuan;
        $p->deskripsi = $request->deskripsi;
        $p->stok = (int) $request->stok;
        $p->tanggal_update = now();
        $p->save();

        $disk = 'public';
        $baseDir = 'produk/' . $p->id;

        // ensure directory exists
        Storage::disk($disk)->makeDirectory($baseDir);
        Storage::disk($disk)->setVisibility($baseDir, 'public');

        for ($i = 1; $i <= 3; $i++) {
            $field = 'foto' . $i;
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $i . '.jpg';
                $path = $baseDir . '/' . $filename;

                // delete old if exists
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                }

                $file->storeAs($baseDir, $filename, $disk);

                if ($i === 1) {
                    $p->foto = $path;
                    $p->save();
                }
            }
        }

        return response()->json(['message' => 'Produk berhasil diperbarui.']);
    }

    public function hapusProduk($id)
    {
        $p = Produk::find($id);
        if (!$p) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        if ($p->pesanan()->exists()) {
            return response()->json(['message' => 'Tidak dapat menghapus: masih ada Produk yang dipesan/terjual.'], 422);
        }

        $disk = 'public';
        $baseDir = 'produk/' . $p->id;

        // delete directory and all files inside
        if (Storage::disk($disk)->exists($baseDir)) {
            Storage::disk($disk)->deleteDirectory($baseDir);
        }

        $p->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}
