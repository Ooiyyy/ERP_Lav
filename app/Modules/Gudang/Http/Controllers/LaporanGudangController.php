<?php

namespace App\Modules\Gudang\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanGudangController
{
   public function index(Request $request)
{
    $request->validate([
        'start_date' => 'nullable|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
    ]);

    $startDate = $request->start_date;
    $endDate   = $request->end_date;

    // =============================
    // BAHAN BAKU MASUK
    // =============================
    $masuk = DB::table('bahan_baku_masuk as h')
        ->join('detail_bahan_baku_masuk as d', 'd.id_bahan_baku_masuk', '=', 'h.id')
        ->join('bahan_baku as b', 'b.id', '=', 'd.id_bahan_baku')
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            $q->whereBetween('h.tanggal_transaksi', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        })
        ->groupBy('b.nama', 'd.satuan')
        ->select(
            'b.nama as nama_bahan',
            'd.satuan',
            DB::raw('SUM(d.jumlah) as total_jumlah')
        )
        ->get();

    // =============================
    // BAHAN BAKU KELUAR
    // =============================
    $keluar = DB::table('bahan_baku_keluar as h')
        ->join('detail_bahan_baku_keluar as d', 'd.id_bahan_baku_keluar', '=', 'h.id')
        ->join('bahan_baku as b', 'b.id', '=', 'd.id_bahan_baku')
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            $q->whereBetween('h.tanggal_transaksi', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        })
        ->groupBy('b.nama', 'd.satuan')
        ->select(
            'b.nama as nama_bahan',
            'd.satuan',
            DB::raw('SUM(d.jumlah) as total_jumlah')
        )
        ->get();

    return view('gudang::laporan-harian.index', compact(
        'startDate', 'endDate', 'masuk', 'keluar'
    ));
}

    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $masuk = DB::table('bahan_baku_masuk as h')
            ->join('detail_bahan_baku_masuk as d', 'd.id_bahan_baku_masuk', '=', 'h.id')
            ->join('bahan_baku as b', 'b.id', '=', 'd.id_bahan_baku')
            ->whereBetween('h.tanggal_transaksi', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->groupBy('b.nama', 'd.satuan')
            ->select('b.nama as nama_bahan', 'd.satuan', DB::raw('SUM(d.jumlah) as total_jumlah'))
            ->get();

        $keluar = DB::table('bahan_baku_keluar as h')
            ->join('detail_bahan_baku_keluar as d', 'd.id_bahan_baku_keluar', '=', 'h.id')
            ->join('bahan_baku as b', 'b.id', '=', 'd.id_bahan_baku')
            ->whereBetween('h.tanggal_transaksi', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->groupBy('b.nama', 'd.satuan')
            ->select('b.nama as nama_bahan', 'd.satuan', DB::raw('SUM(d.jumlah) as total_jumlah'))
            ->get();

        // LANGSUNG PAKAI DOMPDF - TANPA BARRYVDH
        $html = view('gudang::laporan-harian.pdf', compact('startDate', 'endDate', 'masuk', 'keluar'))->render();
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'Laporan_Gudang_' . date('dmY') . '.pdf';
        
        return response()->streamDownload(function() use ($dompdf) {
            echo $dompdf->output();
        }, $filename);
    }

}
