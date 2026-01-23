<?php

namespace App\Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KaryawanController
{
    public function index()
    {
        $karyawan = Karyawan::with('jabatan')->get();

        $last = Karyawan::orderBy('id', 'desc')->first();
        $num = $last ? (int) substr($last->id, 1) + 1 : 1;
        $idBaru = 'K' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $jabatan = Jabatan::all();

        return view('master::DaftarKaryawan', compact('karyawan', 'idBaru', 'jabatan'));
    }
    
    public function data()
    {
        $pembuatpengubah = Karyawan::query()
            ->leftJoin('karyawan as pembuat', 'pembuat.id', '=', 'karyawan.id_buat')
            ->leftJoin('karyawan as pengubah', 'pengubah.id', '=', 'karyawan.id_update')
            ->select([
                'karyawan.id as id_karyawan_row',
                'karyawan.id_buat',
                'karyawan.id_update',
                'pembuat.nama as nama_pembuat',
                'pengubah.nama as nama_pengubah',
            ])
            ->get()
            ->keyBy('id_karyawan_row');

        $rows = Karyawan::with('jabatan')->get();

        $alldata = $rows->map(function ($r) use ($pembuatpengubah) {
            $pp = $pembuatpengubah->get($r->id);

            return [
                'id' => $r->id,
                'nama' => $r->nama,
                'jabatan' => optional($r->jabatan)->nama ?? '-',
                'alamat' => $r->alamat,
                'jenis_kelamin' => $r->jenis_kelamin,
                'no_hp' => $r->no_hp,
                'status' => $r->status,
                'tempat_lahir' => $r->tempat_lahir,
                'tanggal_lahir' => $r->tanggal_lahir,
                'nama_pembuat' => optional($pp)->nama_pembuat ?? '-',
                'nama_pengubah' => optional($pp)->nama_pengubah ?? '-',
            ];
        });

        return response()->json(['data' => $alldata]);
    }

    public function create()
    {
        $last = Karyawan::orderBy('id', 'desc')->first();
        $num = $last ? (int) substr($last->id, 1) + 1 : 1;
        $idBaru = 'K' . str_pad($num, 3, '0', STR_PAD_LEFT);
        $jabatan = Jabatan::all();
        return view('akun::DaftarKaryawan', compact('idBaru', 'jabatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|max:5|unique:karyawan,id',
            'nama' => 'required|max:100',
            'id_jabatan' => 'required|exists:jabatan,id',
            'alamat' => 'required',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'required|max:13',
            'status' => 'required|in:Y,N',
            'tempat_lahir' => 'required|max:100',
            'tanggal_lahir' => 'required|date',
            'foto' => ['nullable', 'file', 'max:1024', 'mimetypes:image/jpeg'],
        ]);

        $idKaryawan = $validated['id'];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $ext = strtolower($file->getClientOriginalExtension());

            if (!in_array($ext, ['jpg', 'jpeg'])) {
                if ($request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => 'Format foto harus .jpg'], 422);
                }

                return back()->withInput()->with('error', 'Format foto harus .jpg');
            }

            $fileName = $idKaryawan . '.jpg';
            $file->storeAs('foto_karyawan', $fileName, 'public');
        }

        $karyawan = Karyawan::create([
            'id' => $idKaryawan,
            'id_jabatan' => $validated['id_jabatan'],
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'no_hp' => $validated['no_hp'],
            'status' => $validated['status'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'id_buat' => auth('web')->id(),
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Karyawan berhasil ditambahkan.', 'data' => $karyawan], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $jabatan = Jabatan::orderBy('nama')->get();
        return view('akun::DaftarKaryawan', compact('karyawan', 'jabatan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nama' => 'required|max:100',
            'id_jabatan' => 'required|exists:jabatan,id',
            'alamat' => 'required',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'required|max:13',
            'status' => 'required|in:Y,N',
            'tempat_lahir' => 'required|max:100',
            'tanggal_lahir' => 'required|date',
            'foto' => 'nullable|file|max:1024|mimetypes:image/jpeg',
        ], [
            'foto.mimetypes' => 'Foto harus berformat JPG.',
            'foto.max' => 'Ukuran foto maksimal 1MB.',
        ]);

        $karyawan->update([
            'nama' => $request->nama,
            'id_jabatan' => $request->id_jabatan,
            'alamat' => $request->alamat,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'status' => $request->status,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_update' => auth('web')->id(),
        ]);

        if ($request->hasFile('foto')) {
            $fileName = $id . '.jpg';
            $path = 'foto_karyawan/' . $fileName;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $request->file('foto')->storeAs('foto_karyawan', $fileName, 'public');
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Karyawan berhasil diupdate.', 'data' => $karyawan], 201);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil diupdate.');
    }

    public function destroy(Request $request, $id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Data karyawan tidak ditemukan.'], 404);
            }
            return redirect()->route('akun.karyawan')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $cekAkun = Akun::where('id_karyawan', $id)->exists();
        if ($cekAkun) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Tidak dapat menghapus karena karyawan ini masih memiliki akun.'], 422);
            }
            return redirect()->route('akun.karyawan')->with('error', 'Tidak dapat menghapus karena karyawan ini masih memiliki akun.');
        }

        $fileName = $id . '.jpg';
        $fotoPath = 'foto_karyawan/' . $fileName;
        if (Storage::disk('public')->exists($fotoPath)) {
            Storage::disk('public')->delete($fotoPath);
        }

        $karyawan->delete();

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Karyawan berhasil dihapus.'], 200);
        }

        return redirect()->route('akun.karyawan')->with('success', 'Karyawan berhasil dihapus.');
    }
}
