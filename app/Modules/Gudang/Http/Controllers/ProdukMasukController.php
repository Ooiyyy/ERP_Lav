<?php 

namespace App\Modules\Gudang\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProdukMasuk;
use App\Models\Produk;
use App\Models\DetailProdukMasuk;
use Illuminate\Support\Str;

class ProdukMasukController
{
   public function index()
{
    $data = ProdukMasuk::with('detail')
        ->orderBy('tanggal_transaksi', 'desc')
        ->get();

    return view('gudang::produk-masuk.index', compact('data'));
}

public function store(Request $request)
{
    $request->validate([
        'tanggal_transaksi'     => 'required|date',
        'items'                 => 'required|array|min:1',
        'items.*.id_produk'     => 'required|exists:produk,id',
        'items.*.jumlah'        => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {

        $idHeader = $this->generateId('PM', 'produk_masuk');

        ProdukMasuk::create([
            'id'                     => $idHeader,
            'id_karyawan_gudang'     => auth('web')->id(),
            'id_karyawan_produksi'   => auth('web')->id(),
            'tanggal_transaksi'      => $request->tanggal_transaksi,
            'id_buat'                => auth('web')->id(),
            'tanggal_buat'           => now(),
        ]);

        foreach ($request->items as $item) {

            // 🔥 AMBIL PRODUK MASTER
            $produk = Produk::lockForUpdate()
                ->where('id', $item['id_produk'])
                ->firstOrFail();

            DetailProdukMasuk::create([
                'id'               => 'PMD-' . uniqid(),
                'id_produk_masuk'  => $idHeader,
                'id_produk'        => $item['id_produk'],
                'jumlah'           => $item['jumlah'],
                'satuan'           => $produk->satuan, // ✅ FIX DI SINI
            ]);

            // 🔥 TAMBAH STOK
            $produk->increment('stok', $item['jumlah']);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Produk masuk berhasil disimpan'
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
        try {
            \Log::info('Show ID: ' . $id); // Debug log
            
            $data = ProdukMasuk::with('detail.produk')->find($id);
            
            if (!$data) {
                \Log::error('Data tidak ditemukan untuk ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan dengan ID: ' . $id
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // EDIT - Ambil data untuk edit
    public function edit($id)
    {
        try {
            \Log::info('Edit ID: ' . $id); // Debug log
            
            $data = ProdukMasuk::with('detail.produk')->find($id);
            
            if (!$data) {
                \Log::error('Data tidak ditemukan untuk ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan dengan ID: ' . $id
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error edit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

public function update(Request $request, $id)
{
    DB::beginTransaction();
    try {

        // 1️⃣ AMBIL HEADER + DETAIL LAMA
        $header = ProdukMasuk::with('detail')->findOrFail($id);

        // 2️⃣ ROLLBACK STOK LAMA
        foreach ($header->detail as $d) {
            Produk::where('id', $d->id_produk)
                ->decrement('stok', $d->jumlah);
        }

        // 3️⃣ HAPUS DETAIL LAMA
        DetailProdukMasuk::where('id_produk_masuk', $id)->delete();

        // 4️⃣ UPDATE HEADER
        $header->update([
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'id_update'         => auth('web')->id(),
            'tanggal_update'    => now(),
        ]);

        // 5️⃣ SIMPAN DETAIL BARU + TAMBAH STOK BARU
        foreach ($request->items as $item) {

            // 🔥 AMBIL PRODUK MASTER
            $produk = Produk::lockForUpdate()
                ->where('id', $item['id_produk'])
                ->firstOrFail();

            DetailProdukMasuk::create([
                'id'               => 'PMD-' . Str::random(10),
                'id_produk_masuk'  => $id,
                'id_produk'        => $item['id_produk'],
                'jumlah'           => $item['jumlah'],
                'satuan'           => $produk->satuan, // ✅ dari master
            ]);

            // 🔥 TAMBAH STOK BARU
            $produk->increment('stok', $item['jumlah']);
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

    // DELETE - Hapus data
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Hapus detail terlebih dahulu
            DetailProdukMasuk::where('id_produk_masuk', $id)->delete();
            
            // Hapus header
            ProdukMasuk::findOrFail($id)->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
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
    $header = ProdukMasuk::with([
            'detail.detail',     // relasi detail → produk
            'karyawan',    // relasi langsung dari produk_masuk
        ])
        ->where('id', $id)
        ->firstOrFail();

    // // 🔥 NOMINAL DARI DETAIL (kalau ada harga)
    // $nominal = $header->detail->sum(function ($d) {
    //     return $d->qty * ($d->harga ?? 0);
    // });

    return response()->json([
        'id'       => $header->id,
        'tanggal'  => $header->tanggal_transaksi,
        'petugas'  => optional($header->karyawan)->nama ?? '-',
        'detail'   => $header->detail->map(function ($d) {
            return [
                'nama'   => optional($d->produk)->nama_produk ?? '-',
                'jumlah' => $d->qty,
                'satuan' => $d->satuan,
            ];
        })
    ]);
}
}

