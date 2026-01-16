<?php

namespace App\Modules\Gudang\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BahanBaku;
use App\Models\BahanBakuMasuk;
use App\Models\Supplier;
use App\Models\DetailTransaksiBahanBakuMasuk;
use Illuminate\Support\Str;

class BahanBakuMasukController
{
    public function index()
{
    $data = BahanBakuMasuk::with(['detail', 'supplier'])
        ->orderBy('tanggal_transaksi', 'desc')
        ->get();

    return view('gudang::bahan-masuk.index', compact('data'));
}


    // =========================
    // DATATABLE DATA (JSON)
    // =========================
    // public function data0()
    // {
    //     return BahanBakuMasuk::with(['detail', 'supplier'])
    //         ->orderBy('tanggal_transaksi', 'desc')
    //         ->get()
    //         ->map(function ($t) {
    //             return [
    //                 'id'         => $t->id,
    //                 'tanggal'    => $t->tanggal_transaksi,
    //                 'toko'       => optional($t->toko)->nama_toko ?? '-',
    //                 'no_nota'    => $t->no_nota ?? '-',
    //                 'total_item' => $t->detail->count(),
                    
    //             ];
    //         });
    // }

        public function data()
{
    $data = BahanBakuMasuk::with('supplier')
        ->withCount('detail')
        ->select('bahan_baku_masuk.*')
        ->selectSub(function ($query) {
            $query->from('detail_bahan_baku_masuk')
                ->selectRaw('COALESCE(SUM(jumlah * nominal), 0)')
                ->whereColumn(
                    'detail_bahan_baku_masuk.id_bahan_baku_masuk',
                    'bahan_baku_masuk.id'
                );
        }, 'total_nominal')
        ->orderBy('tanggal_transaksi', 'desc')
        ->get();

    return response()->json([
        'data' => $data->map(function ($t) {
            return [
                'id'         => $t->id,
                'tanggal'    => $t->tanggal_transaksi,
                'toko'       => optional($t->supplier)->nama_toko ?? '-',
                'no_nota'    => $t->no_nota ?? '-',
              'total_item' => $t->detail()->count(),
                'aksi'       => '
                 <td class="text-center align-middle">
                                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-info btn-sm  mx-1 btn-detail" data-id="'.$t->id.'">Detail</button>
                    <button class="btn btn-warning btn-sm mx-1  btn-edit" data-id="'.$t->id.'">Edit</button>
                    <button class="btn btn-danger btn-sm  mx-1 btn-delete" data-id="'.$t->id.'">Hapus</button>
                '
            ];
        })
    ]);
}

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'id_toko'           => 'nullable|string',
            'no_nota'           => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.id_bahan_baku' => 'required|string',
            'items.*.jumlah'        => 'required|integer|min:1',
            'items.*.nominal'       => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {

           $idHeader = $this->generateId(
            'BM',
            'bahan_baku_masuk'
        );


            BahanBakuMasuk::create([
                'id'                => $idHeader,
                'id_karyawan_gudang' => auth('web')->id(),
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'id_toko'           => $request->id_toko,
                'no_nota'           => $request->no_nota,
                'id_buat'           => auth('web')->id(),
                'tanggal_buat'      => now(),
            ]);

            foreach ($request->items as $item) {

                DetailTransaksiBahanBakuMasuk::create([
                    'id' => 'BBMD-' . uniqid(),
                    'id_bahan_baku_masuk' => $idHeader,
                    'id_bahan_baku' => $item['id_bahan_baku'],
                    'jumlah' => $item['jumlah'],
                    'nominal' => $item['nominal'],
                    'satuan' => $item['satuan'],
                    'tanggal_buat' => now(),

                ]);

                BahanBaku::where('id', $item['id_bahan_baku'])
                    ->increment('stok_bahan_baku', $item['jumlah']);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
   public function update(Request $request, $id)
{
    DB::beginTransaction();
    try {

        $header = BahanBakuMasuk::with('detail')->findOrFail($id);

        // 1️⃣ ROLLBACK STOK LAMA (KURANGI)
        foreach ($header->detail as $d) {
            BahanBaku::where('id', $d->id_bahan_baku)
                ->decrement('stok_bahan_baku', $d->jumlah);
        }

        // 2️⃣ HAPUS DETAIL LAMA
        DetailTransaksiBahanBakuMasuk::where(
            'id_bahan_baku_masuk', $id
        )->delete();

        // 3️⃣ UPDATE HEADER
        $header->update([
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'id_toko' => $request->id_toko,
            'no_nota' => $request->no_nota,
        ]);

        // 4️⃣ SIMPAN DETAIL BARU + TAMBAH STOK
        foreach ($request->items as $item) {

            DetailTransaksiBahanBakuMasuk::create([
                'id' => 'BBMD-' . Str::random(10),
                'id_bahan_baku_masuk' => $id,
                'id_bahan_baku' => $item['id_bahan_baku'],
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'nominal' => $item['nominal'],
            ]);

            BahanBaku::where('id', $item['id_bahan_baku'])
                ->increment('stok_bahan_baku', $item['jumlah']);
        }

        DB::commit();
        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function destroy($id)
{
    DB::beginTransaction();
    try {

        $header = BahanBakuMasuk::with('detail')->findOrFail($id);

        // rollback stok
        foreach ($header->detail as $d) {
            BahanBaku::where('id', $d->id_bahan_baku)
                ->decrement('stok_bahan_baku', $d->jumlah);
        }

        $header->detail()->delete();
        $header->delete();

        DB::commit();
        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false], 500);
    }
}
public function show($id)
{
    return BahanBakuMasuk::with('detail')->findOrFail($id);
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
    $header = BahanBakuMasuk::with([
            'detail.bahanBaku',
            'karyawan.' // relasi ke karyawan
        ])
        ->where('id', $id)
        ->firstOrFail();

    // 🔥 NOMINAL DARI DETAIL
    $nominal = $header->detail->sum('nominal');

    return response()->json([
        'id'       => $header->id,
        'tanggal'  => $header->tanggal_transaksi,
        'petugas'  => optional($header->karyawan)->nama ?? '-',
        'nominal'  => $nominal,
        'detail'   => $header->detail->map(function ($d) {
            return [
                'nama'   => optional($d->bahanBaku)->nama ?? '-',
                'jumlah' => $d->jumlah,
                'satuan' => $d->satuan,
            ];
        })
    ]);
}
}