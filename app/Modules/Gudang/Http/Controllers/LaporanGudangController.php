<?php

namespace App\Modules\Gudang\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanGudangController
{
    public function index(Request $request)
{
    $tanggal = $request->tanggal ?? date('Y-m-d');

    // =============================
    // BAHAN BAKU MASUK
    // =============================
    $masuk = DB::table('bahan_baku_masuk as h')
        ->join('detail_bahan_baku_masuk as d',
            'd.id_bahan_baku_masuk', '=', 'h.id')
        ->join('bahan_baku as b',
            'b.id', '=', 'd.id_bahan_baku')
        ->whereDate('h.tanggal_transaksi', $tanggal)
        ->select(
            'b.nama as nama_bahan',
            'd.satuan',
            DB::raw('SUM(d.jumlah) as total_jumlah'),
            DB::raw('SUM(d.jumlah * d.nominal) as total_nominal')
        )
        ->groupBy('b.nama', 'd.satuan')
        ->orderBy('b.nama')
        ->get();

    // =============================
    // BAHAN BAKU KELUAR
    // =============================
    $keluar = DB::table('bahan_baku_keluar as h')
        ->join('detail_bahan_baku_keluar as d',
            'd.id_bahan_baku_keluar', '=', 'h.id')
        ->join('bahan_baku as b',
            'b.id', '=', 'd.id_bahan_baku')
        ->whereDate('h.tanggal_transaksi', $tanggal)
        ->select(
            'b.nama as nama_bahan',
            'd.satuan',
            DB::raw('SUM(d.jumlah) as total_jumlah')
        )
        ->groupBy('b.nama', 'd.satuan')
        ->orderBy('b.nama')
        ->get();

    return view('gudang::laporan-harian/index', compact(
        'tanggal',
        'masuk',
        'keluar'
    ));
}

}
