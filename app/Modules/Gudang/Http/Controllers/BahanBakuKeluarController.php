<?php

namespace App\Modules\Gudang\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BahanBaku;
use App\Models\BahanBakuKeluar;
use App\Models\DetailTransaksiBahanBakuKeluar;

class BahanBakuKeluarController
{
    public function index()
    {
        $data = BahanBakuKeluar::with('detail')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        return view('gudang::bahan-keluar.index', compact('data'));
    }

    // =========================================
    // STORE BAHAN BAKU KELUAR (KURANGI STOK)
    // =========================================
   public function store(Request $request)
{
    $request->validate([
        'tanggal_transaksi'       => 'required|date',
        'items'                   => 'required|array|min:1',
        'items.*.id_bahan_baku'   => 'required|exists:bahan_baku,id',
        'items.*.jumlah'          => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        // =========================
        // 1. GENERATE ID HEADER
        // =========================
      $idHeader = $this->generateId(
            'BK',
            'bahan_baku_keluar'
        );


        // =========================
        // 2. INSERT HEADER
        // =========================
        BahanBakuKeluar::create([
            'id'                 => $idHeader,
            'id_karyawan_gudang' => auth('web')->id(),
            'tanggal_transaksi'  => $request->tanggal_transaksi,
            'id_buat'            => auth('web')->id(),
            'tanggal_buat'       => now(),
        ]);

        // =========================
        // 3. INSERT DETAIL + KURANGI STOK
        // =========================
        foreach ($request->items as $item) {

            // LOCK ROW BAHAN BAKU
            $bahan = BahanBaku::where('id', $item['id_bahan_baku'])
                ->lockForUpdate()
                ->first();

            if (!$bahan) {
                throw new \Exception('Bahan baku tidak ditemukan');
            }

            // VALIDASI STOK
            if ($bahan->stok_bahan_baku < $item['jumlah']) {
                throw new \Exception(
                    'Stok bahan "' . $bahan->nama . '" tidak mencukupi'
                );
            }

            // INSERT DETAIL
           DetailTransaksiBahanBakuKeluar::create([
            'id'                   => 'BBKD-' . uniqid(),
            'id_bahan_baku_keluar' => $idHeader,
            'id_bahan_baku'        => $item['id_bahan_baku'],
            'jumlah'               => $item['jumlah'],
            'satuan'               => $item['satuan'], // 🔥 WAJIB
            'id_buat'              => auth('web')->id(),
            'tanggal_buat'         => now(),
        ]);


            // 🔥 KURANGI STOK MASTER
            $bahan->decrement('stok_bahan_baku', $item['jumlah']);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku keluar berhasil disimpan'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function show($id)
{
    $header = BahanBakuKeluar::with('detail')->findOrFail($id);

    $detail = $header->detail->map(function ($d) {
        $stokMaster = BahanBaku::where('id', $d->id_bahan_baku)
            ->value('stok_bahan_baku');

        return [
            'id_bahan_baku' => $d->id_bahan_baku,
            'jumlah'        => $d->jumlah,
            'satuan'        => $d->satuan,
            'stok_master'   => $stokMaster ?? 0,
        ]; 
    });

    return response()->json([
        'id' => $header->id,
        'tanggal_transaksi' => $header->tanggal_transaksi,
        'detail' => $detail
    ]);
}
    // =========================================
    // UPDATE BAHAN BAKU KELUAR
    // =========================================
   public function update(Request $request, $id)
{
    $request->validate([
        'tanggal_transaksi'       => 'required|date',
        'items'                   => 'required|array|min:1',
        'items.*.id_bahan_baku'   => 'required|exists:bahan_baku,id',
        'items.*.jumlah'          => 'required|integer|min:1',
        'items.*.satuan'          => 'required|string|max:20',
    ]);

    DB::beginTransaction();

    try {
        $header = BahanBakuKeluar::with('detail')->findOrFail($id);

        // =========================
        // 1. KEMBALIKAN STOK LAMA
        // =========================
        foreach ($header->detail as $d) {
            BahanBaku::where('id', $d->id_bahan_baku)
                ->increment('stok_bahan_baku', $d->jumlah);
        }

        // hapus detail lama
        $header->detail()->delete();

        // update header
        $header->update([
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'id_update'         => auth('web')->id(),
            'tanggal_update'    => now(),
        ]);

        // =========================
        // 2. INSERT DETAIL BARU + KURANGI STOK
        // =========================
        foreach ($request->items as $item) {

            $bahan = BahanBaku::lockForUpdate()
                ->where('id', $item['id_bahan_baku'])
                ->firstOrFail();

            if ($bahan->stok_bahan_baku < $item['jumlah']) {
                throw new \Exception(
                    'Stok bahan "' . $bahan->nama . '" tidak mencukupi'
                );
            }

            DetailTransaksiBahanBakuKeluar::create([
                'id'                   => 'BBKD-' . uniqid(),
                'id_bahan_baku_keluar' => $id,
                'id_bahan_baku'        => $item['id_bahan_baku'],
                'jumlah'               => $item['jumlah'],
                'satuan'               => $item['satuan'], // otomatis dari master
                'id_buat'              => auth('web')->id(),
                'tanggal_buat'         => now(),
            ]);

            // kurangi stok master
            $bahan->decrement('stok_bahan_baku', $item['jumlah']);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data bahan baku keluar berhasil diupdate'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


    // =========================================
    // DELETE BAHAN BAKU KELUAR
    // =========================================
public function destroy($id)
{
    DB::beginTransaction();

    try {
        $header = BahanBakuKeluar::with('detail')->findOrFail($id);

        // rollback stok
        foreach ($header->detail as $d) {
            BahanBaku::where('id', $d->id_bahan_baku)
                ->increment('stok_bahan_baku', $d->jumlah);
        }

        // hapus detail dulu
        DetailTransaksiBahanBakuKeluar::where(
            'id_bahan_baku_keluar', $id
        )->delete();

        // hapus header
        $header->delete();

        DB::commit();
        return response()->json(['success' => true]);

    } catch (\Throwable $e) {
        DB::rollBack();

        // 🔥 LOG ERROR ASLI
        \Log::error($e);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
private function generateId($prefix, $table, $column = 'id')
{
    $ym = date('Ym'); // 202508

    $last = DB::table($table)
        ->where($column, 'like', "$prefix-$ym-%")
        ->orderBy($column, 'desc')
        ->value($column);

    $number = $last
        ? intval(substr($last, -4)) + 1
        : 1;

    return sprintf('%s-%s-%04d', $prefix, $ym, $number);
}
public function detail($id)
{
    $header = BahanBakuKeluar::with([
            'detail.bahanBaku',
            'karyawan'
        ])
        ->where('id', $id)
        ->firstOrFail();

    return response()->json([
        'id'      => $header->id,
        'tanggal' => $header->tanggal_transaksi,
        'petugas' => optional($header->karyawanGudang)->nama ?? '-',
        'detail'  => $header->detail->map(function ($d) {
            return [
                'nama'   => optional($d->bahanBaku)->nama ?? '-',
                'jumlah' => $d->jumlah,
                'satuan' => $d->satuan,
            ];
        })
    ]);
}
}
