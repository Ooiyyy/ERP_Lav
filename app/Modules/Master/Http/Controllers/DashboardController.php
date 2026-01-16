<?php

namespace App\Modules\Master\Http\Controllers;

use App\Models\Akun;
use App\Models\Toko;

// models
use App\Models\Order;
use App\Models\Produk;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\BahanBaku;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController
{
    public function index()
    {
        // ambil count tiap master (gunakan count() Eloquent)
        // jika model berada di namespace lain, sesuaikan use di atas
        $counts = [
            'jabatan'    => (int) optional(Jabatan::query())->count(),
            'karyawan'   => (int) optional(Karyawan::query())->count(),
            'akun'       => (int) optional(Akun::query())->count(),
            'produk'     => (int) optional(Produk::query())->count(),
            'pelanggan'  => (int) optional(Pelanggan::query())->count(),
            'toko'       => (int) optional(Toko::query())->count(),
            'bahan_baku' => (int) optional(BahanBaku::query())->count(),
        ];

        // konfigurasi card — label, ikon, kelas warna, dan route tujuan saat diklik
        $cards = [
            [
                'key' => 'jabatan',
                'label' => 'Jabatan',
                'count' => $counts['jabatan'],
                'icon' => 'fa-briefcase',
                'color' => 'primary',
                'route' => route('master.daftarJabatan'),
            ],
            [
                'key' => 'karyawan',
                'label' => 'Karyawan',
                'count' => $counts['karyawan'],
                'icon' => 'fa-users',
                'color' => 'success',
                'route' => route('master.karyawan'),
            ],
            [
                'key' => 'akun',
                'label' => 'Akun',
                'count' => $counts['akun'],
                'icon' => 'fa-user-shield',
                'color' => 'info',
                'route' => route('master.daftarAkun'),
            ],
            [
                'key' => 'produk',
                'label' => 'Produk',
                'count' => $counts['produk'],
                'icon' => 'fa-box-open',
                'color' => 'warning',
                'route' => route('master.daftar-produk'),
            ],
            [
                'key' => 'bahan_baku',
                'label' => 'Bahan Baku',
                'count' => $counts['bahan_baku'],
                'icon' => 'fa-cubes',
                'color' => 'secondary',
                'route' => route('master.databahan-baku'),
            ],
            [
                'key' => 'toko',
                'label' => 'Toko',
                'count' => $counts['toko'],
                'icon' => 'fa-store',
                'color' => 'dark',
                'route' => route('master.daftarToko'),
            ],
            [
                'key' => 'pelanggan',
                'label' => 'Pelanggan',
                'count' => $counts['pelanggan'],
                'icon' => 'fa-user-friends',
                'color' => 'danger',
                'route' => route('master.daftarPelanggan'),
            ],
        ];

        // render view module (sesuaikan nama view jika berbeda)
        // jika view berada di App\Modules\Master\Resources\Views\beranda.blade.php
        return view('master::beranda', compact('cards'));
    }
    public function pesananLunas(Request $r)
    {
        $from = $r->query('date_from') ?? now()->startOfMonth()->toDateString();
        $to = $r->query('date_to') ?? now()->endOfMonth()->toDateString();

        // 1) total per order = sum(detail_order.jumlah * detail_order.harga)
        // 2) total pembayaran per order = sum(pembayaran_cash.nominal) + sum(pembayaran_va.nominal)
        // 3) order considered "lunas" when pembayaran_sum >= total_order
        // 4) group by DATE(pembayaran tanggal) -> count orders lunas per date

        // Subquery: order_totals
        $orderTotals = DB::table('detail_order')
            ->select('id_transaksi as id_order', DB::raw('SUM(jumlah * harga) as order_total'))
            ->groupBy('id_transaksi');

        // Subquery: pembayaran sums grouped by order (combine cash + va)
        $pembCash = DB::table('pembayaran_cash')
            ->select('id_order', DB::raw('SUM(nominal) as paid_cash'), DB::raw('MIN(tanggal_pembayaran) as first_paid_date'))
            ->whereBetween(DB::raw('DATE(tanggal_pembayaran)'), [$from, $to])
            ->groupBy('id_order');

        $pembVa = DB::table('pembayaran_va')
            ->select('id_order', DB::raw('SUM(nominal) as paid_va'), DB::raw('MIN(tanggal_transaksi) as first_paid_date'))
            ->whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$from, $to])
            ->groupBy('id_order');

        // combine pembayaran => left join both to get total paid
        $paid = DB::table(DB::raw("({$pembCash->toSql()}) as pc"))
            ->mergeBindings($pembCash)
            ->leftJoin(DB::raw("({$pembVa->toSql()}) as pv"), function ($join) use ($pembVa) {
                $join->on('pc.id_order', '=', 'pv.id_order');
            })
            ->select(
                DB::raw('COALESCE(pc.id_order, pv.id_order) as id_order'),
                DB::raw('COALESCE(pc.paid_cash,0) + COALESCE(pv.paid_va,0) as total_paid'),
                DB::raw('COALESCE(pc.first_paid_date, pv.first_paid_date) as paid_date')
            );

        // Note: above method using subqueries built via query builder is complex; simpler approach:
        // Use raw query combining payments, then group. I'll implement a simpler DB query below.

        // Simpler approach: compute paid per order by union cash & va then group
        $unionPayments = DB::table('pembayaran_cash')
            ->select('id_order', DB::raw('nominal as nominal'), DB::raw('DATE(tanggal_pembayaran) as paid_date'))
            ->whereBetween(DB::raw('DATE(tanggal_pembayaran)'), [$from, $to])
            ->unionAll(
                DB::table('pembayaran_va')
                    ->select('id_order', DB::raw('nominal as nominal'), DB::raw('DATE(tanggal_transaksi) as paid_date'))
                    ->whereBetween(DB::raw('DATE(tanggal_transaksi)'), [$from, $to])
            );

        // wrap union
        $payments = DB::table(DB::raw("({$unionPayments->toSql()}) as union_p"))
            ->mergeBindings($unionPayments)
            ->select('id_order', DB::raw('SUM(nominal) as total_paid'), 'paid_date')
            ->groupBy('id_order', 'paid_date');

        // Now join orderTotals with payments and select only those where total_paid >= order_total
        $result = DB::table(DB::raw("({$orderTotals->toSql()}) as ot"))
            ->mergeBindings($orderTotals)
            ->join(DB::raw("({$payments->toSql()}) as pay"), 'ot.id_order', '=', 'pay.id_order')
            ->mergeBindings($payments)
            ->select(DB::raw('pay.paid_date as date'), DB::raw('COUNT(ot.id_order) as jumlah_lunas'))
            ->whereRaw('pay.total_paid >= ot.order_total')
            ->groupBy('pay.paid_date')
            ->orderBy('pay.paid_date')
            ->get();

        // Format response for Highcharts: arrays categories (dates) & data (counts)
        $dates = [];
        $counts = [];
        foreach ($result as $row) {
            $dates[] = $row->date;
            $counts[] = (int) $row->jumlah_lunas;
        }

        return response()->json([
            'status' => 'ok',
            'date_from' => $from,
            'date_to' => $to,
            'categories' => $dates,
            'data' => $counts,
        ]);
    }

    /**
     * Return JSON: total penjualan per sales (group by sales name or karyawan)
     * Request params: date_from, date_to
     */
    public function penjualanPerSales(Request $request)
    {
        try {
            // 1. rentang tanggal
            $tanggal_dari   = $request->query('date_from') ?? now()->startOfMonth()->toDateString();
            $tanggal_sampai = $request->query('date_to')   ?? now()->endOfMonth()->toDateString();

            // 2. ambil nama tabel order dari model Order (agar aman jika bernama khusus)
            $modelOrder = new \App\Models\Order;
            $nama_tabel_order = DB::getTablePrefix() . $modelOrder->getTable(); // mis. 'order'
            $nama_tabel_order_quoted = "`" . str_replace("`", "", $nama_tabel_order) . "`";

            // 3. cek apakah kolom petugas ada di masing-masing tabel pembayaran
            $kolom_petugas_cash = null;
            $kolom_petugas_va   = null;

            if (Schema::hasColumn('pembayaran_cash', 'petugas_pembayaran')) {
                $kolom_petugas_cash = 'pc.petugas_pembayaran';
            } elseif (Schema::hasColumn('pembayaran_cash', 'petugas')) {
                $kolom_petugas_cash = 'pc.petugas';
            } elseif (Schema::hasColumn('pembayaran_cash', 'id_petugas')) {
                $kolom_petugas_cash = 'pc.id_petugas';
            }

            if (Schema::hasColumn('pembayaran_va', 'petugas_pembayaran')) {
                $kolom_petugas_va = 'pv.petugas_pembayaran';
            } elseif (Schema::hasColumn('pembayaran_va', 'petugas')) {
                $kolom_petugas_va = 'pv.petugas';
            } elseif (Schema::hasColumn('pembayaran_va', 'id_petugas')) {
                $kolom_petugas_va = 'pv.id_petugas';
            }

            // 4. siapkan select petugas untuk masing-masing (NULL jika kolom tidak ada)
            $select_petugas_cash = $kolom_petugas_cash ? DB::raw("$kolom_petugas_cash as petugas") : DB::raw("NULL as petugas");
            $select_petugas_va   = $kolom_petugas_va   ? DB::raw("$kolom_petugas_va as petugas")   : DB::raw("NULL as petugas");

            // 5. query pembayaran_cash (id_order, nominal, petugas?/NULL)
            $query_cash = DB::table('pembayaran_cash as pc')
                ->select('pc.id_order', DB::raw('pc.nominal as nominal'), $select_petugas_cash)
                ->whereBetween(DB::raw('DATE(pc.tanggal_pembayaran)'), [$tanggal_dari, $tanggal_sampai]);

            // 6. query pembayaran_va (id_order, nominal, petugas?/NULL)
            $query_va = DB::table('pembayaran_va as pv')
                ->select('pv.id_order', DB::raw('pv.nominal as nominal'), $select_petugas_va)
                ->whereBetween(DB::raw('DATE(pv.tanggal_transaksi)'), [$tanggal_dari, $tanggal_sampai]);

            // 7. gabungkan (unionAll)
            $union_pembayaran = $query_cash->unionAll($query_va);

            // 8. bungkus union, left join ke tabel order, ambil sales id (order.id_sales atau petugas)
            $query_gabungan = DB::table(DB::raw("({$union_pembayaran->toSql()}) as pembayaran"))
                ->mergeBindings($union_pembayaran)
                ->leftJoin(DB::raw("{$nama_tabel_order_quoted} as o"), 'pembayaran.id_order', '=', 'o.id')
                ->select(
                    DB::raw('COALESCE(o.id_sales, pembayaran.petugas) as id_sales'),
                    DB::raw('SUM(pembayaran.nominal) as total_penjualan')
                )
                ->groupBy(DB::raw('COALESCE(o.id_sales, pembayaran.petugas)'))
                ->orderByRaw('SUM(pembayaran.nominal) desc');

            $hasil = $query_gabungan->get();

            // 9. mapping id_sales -> nama sales (coba Karyawan, lalu Akun->Karyawan)
            $kategori = [];
            $nilai = [];

            foreach ($hasil as $baris) {
                $id_sales = $baris->id_sales;

                if (empty($id_sales) && $id_sales !== '0') {
                    $nama_sales = 'Unknown';
                } else {
                    // coba sebagai id_karyawan
                    $karyawan = \App\Models\Karyawan::find($id_sales);
                    if ($karyawan && !empty($karyawan->nama)) {
                        $nama_sales = $karyawan->nama;
                    } else {
                        // coba sebagai id_akun -> akun->karyawan->nama
                        $akun = \App\Models\Akun::find($id_sales);
                        if ($akun && $akun->karyawan && !empty($akun->karyawan->nama)) {
                            $nama_sales = $akun->karyawan->nama;
                        } else {
                            $nama_sales = 'Sales ' . $id_sales;
                        }
                    }
                }

                $kategori[] = $nama_sales;
                $nilai[] = (float) $baris->total_penjualan;
            }

            // 10. kembalikan JSON untuk Highcharts
            return response()->json([
                'status' => 'ok',
                'date_from' => $tanggal_dari,
                'date_to' => $tanggal_sampai,
                'categories' => $kategori,
                'data' => $nilai,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
