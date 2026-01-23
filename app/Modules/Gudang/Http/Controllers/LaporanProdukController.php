<?php

namespace App\Modules\Gudang\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;

class LaporanProdukController extends Controller
{
    // =============================
    // HALAMAN WEB
    // =============================
    public function index(Request $request)
    {
      $startDate = $request->filled('start_date') ? $request->start_date : null;
      $endDate   = $request->filled('end_date')   ? $request->end_date   : null;


        // =============================
        // PRODUK MASUK
        // =============================
    $masuk = DB::table('produk_masuk as h')
        ->join('detail_produk_masuk as d', 'd.id_produk_masuk', '=', 'h.id')
        ->join('produk as p', 'p.id', '=', 'd.id_produk')
        ->whereBetween('h.tanggal_transaksi', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
    ->groupBy('p.nama', 'd.satuan')
    ->select(
        'p.nama as nama_produk',
        'd.satuan',
        DB::raw('SUM(d.jumlah) as total_jumlah')
    )
    ->get();
        // =============================
        // PRODUK KELUAR (DARI PENJUALAN)
        // =============================
        $keluar = DB::table('order as o')
        ->join('detail_order as d', 'd.id_transaksi', '=', 'o.id')
        ->join('produk as p', 'p.id', '=', 'd.id_produk')
        ->whereBetween('o.tanggal_buat', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
    ->groupBy('p.nama', 'd.satuan')
    ->select(
        'p.nama as nama_produk',
        'd.satuan',
        DB::raw('SUM(d.jumlah) as total_jumlah')
    )
    ->get();
       return view('gudang::laporan-produk.index', compact(
    'startDate', 'endDate', 'masuk', 'keluar'
    ));
    }

    // =============================
    // EXPORT PDF
    // =============================
   
public function exportPdf(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
    ]);

    $startDate = $request->start_date;
    $endDate   = $request->end_date;

    $masuk = DB::table('produk_masuk as h')
        ->join('detail_produk_masuk as d', 'd.id_produk_masuk', '=', 'h.id')
        ->join('produk as p', 'p.id', '=', 'd.id_produk')
        ->whereBetween('h.tanggal_transaksi', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
        ->groupBy('p.nama', 'd.satuan')
        ->select(
            'p.nama as nama_produk',
            'd.satuan',
            DB::raw('SUM(d.jumlah) as total_jumlah')
        )
        ->get();

    $keluar = DB::table('order as o')
        ->join('detail_order as d', 'd.id_transaksi', '=', 'o.id')
        ->join('produk as p', 'p.id', '=', 'd.id_produk')
        ->whereBetween('o.tanggal_buat', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
        ->groupBy('p.nama', 'd.satuan')
        ->select(
            'p.nama as nama_produk',
            'd.satuan',
            DB::raw('SUM(d.jumlah) as total_jumlah')
        )
        ->get();

    // 🔥 Render blade ke HTML
    $html = view('gudang::laporan-produk.pdf', compact(
        'startDate', 'endDate', 'masuk', 'keluar'
    ))->render();

    // 🔥 DomPDF manual
    $options = new Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return response($dompdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="laporan-produk.pdf"');
}
}