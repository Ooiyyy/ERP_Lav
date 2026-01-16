<?php

namespace App\Modules\Penjualan\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\Order;
use App\Models\Toko;
use App\Models\PembayaranVa;
use Illuminate\Http\Request;
use App\Models\PembayaranCash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Karyawan; // (opsional jika butuh fallback lookup)

class HistoryController
{
    public function index()
    {
        return view('penjualan::history');
    }
    public function data(Request $req)
    {
        $draw   = (int) $req->input('draw', 1);
        $start  = (int) $req->input('start', 0);
        $length = (int) $req->input('length', 10);
        $search = trim((string) $req->input('search.value', ''));

        /* =====================================================
     | 1. AMBIL SEMUA PEMBAYARAN (CASH + VA PAID)
     ===================================================== */
        $rows = collect();

        if (Schema::hasTable('pembayaran_cash')) {
            $cash = DB::table('pembayaran_cash')
                ->select([
                    'id',
                    'id_order',
                    'nominal',
                    DB::raw("'cash' as method"),
                    'petugas_pembayaran as petugas',
                    'tanggal_pembayaran as tanggal'
                ])->get()->map(fn($r) => (array) $r);

            $rows = $rows->merge($cash);
        }

        if (Schema::hasTable('pembayaran_va')) {
            $va = DB::table('pembayaran_va')
                ->where('status', 'paid')
                ->select([
                    'id',
                    'id_order',
                    'nominal',
                    DB::raw("'va' as method"),
                    'channel as petugas',
                    'tanggal_transaksi as tanggal'
                ])->get()->map(fn($r) => (array) $r);

            $rows = $rows->merge($va);
        }

        if ($rows->isEmpty()) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        /* =====================================================
     | 2. DATA ORDER + META (SEKALI QUERY)
     ===================================================== */
        $orderIds = $rows->pluck('id_order')->filter()->unique()->values()->all();

        $orders = Order::whereIn('id', $orderIds)
            ->with(['detailOrders', 'sales', 'toko'])
            ->get()
            ->keyBy('id');

        /* =====================================================
     | 3. HITUNG TOTAL & TERBAYAR PER ORDER
     ===================================================== */
        $orderTotals = [];
        $orderPaid   = [];

        foreach ($orders as $o) {
            $total = method_exists($o, 'totalAmount')
                ? $o->totalAmount()
                : $o->detailOrders->sum(fn($d) => ($d->jumlah ?? 0) * ($d->harga ?? 0));

            $paidCash = Schema::hasTable('pembayaran_cash')
                ? (float) DB::table('pembayaran_cash')->where('id_order', $o->id)->sum('nominal')
                : 0;

            $paidVa = Schema::hasTable('pembayaran_va')
                ? (float) DB::table('pembayaran_va')
                    ->where('id_order', $o->id)
                    ->where('status', 'paid')
                    ->sum('nominal')
                : 0;

            $orderTotals[$o->id] = (float) $total;
            $orderPaid[$o->id]   = $paidCash + $paidVa;
        }

        /* =====================================================
     | 4. GROUP PEMBAYARAN & AMBIL YANG LUNAS
     ===================================================== */
        $grouped = $rows->groupBy('id_order');

        $lunasGroups = $grouped->filter(function ($group, $orderId) use ($orderTotals, $orderPaid) {
            if (!isset($orderTotals[$orderId], $orderPaid[$orderId])) return false;
            return $orderPaid[$orderId] >= $orderTotals[$orderId];
        });

        /* =====================================================
     | 5. BENTUK RESULT DASAR
     ===================================================== */
        $result = $lunasGroups->map(function ($group, $orderId) use ($orders) {
            $o = $orders[$orderId] ?? null;

            $lastDate = $group->pluck('tanggal')
                ->filter()
                ->sortByDesc(fn($v) => strtotime((string) $v))
                ->first();

            return [
                'id_order'     => $orderId,
                'order_kode'   => $o->kode ?? $orderId,
                'tanggal'      => $lastDate,
                'sales_id'     => $o->id_sales ?? null,
                'sales_name'   => optional($o->sales)->nama ?? optional($o->sales)->name ?? $o->id_sales,
                'sales_phone'  => optional($o->sales)->no_hp ?? optional($o->sales)->phone,
                'toko_id'      => $o->id_toko ?? null,
                'toko_name'    => optional($o->toko)->nama_toko ?? optional($o->toko)->nama,
                'nominal'      => $group->sum(fn($p) => (float) ($p['nominal'] ?? 0)),
                'keterangan'   => 'LUNAS',
            ];
        })->values();

        /* =====================================================
     | 6. FILTER REQUEST (INI INTI MASALAH SEBELUMNYA)
     ===================================================== */
        if ($req->filled('year')) {
            $result = $result->filter(
                fn($r) =>
                !empty($r['tanggal']) &&
                    date('Y', strtotime($r['tanggal'])) == $req->year
            )->values();
        }

        if ($req->filled('month')) {
            $result = $result->filter(
                fn($r) =>
                !empty($r['tanggal']) &&
                    date('m', strtotime($r['tanggal'])) == $req->month
            )->values();
        }

        if ($req->filled('date_from') && $req->filled('date_to')) {
            $from = strtotime($req->date_from . ' 00:00:00');
            $to   = strtotime($req->date_to . ' 23:59:59');

            $result = $result->filter(function ($r) use ($from, $to) {
                if (empty($r['tanggal'])) return false;
                $t = strtotime($r['tanggal']);
                return $t >= $from && $t <= $to;
            })->values();
        }

        if ($req->filled('sales')) {
            $result = $result->filter(fn($r) => $r['sales_id'] == $req->sales)->values();
        }

        if ($req->filled('toko')) {
            $result = $result->filter(fn($r) => $r['toko_id'] == $req->toko)->values();
        }

        /* =====================================================
     | 7. SEARCH DATATABLE
     ===================================================== */
        if ($search !== '') {
            $s = mb_strtolower($search);
            $result = $result->filter(function ($r) use ($s) {
                foreach (['id_order', 'order_kode', 'sales_name', 'sales_phone', 'toko_name', 'nominal'] as $f) {
                    if (mb_stripos((string) ($r[$f] ?? ''), $s) !== false) return true;
                }
                return false;
            })->values();
        }

        /* =====================================================
     | 8. SORT & PAGINATION
     ===================================================== */
        $recordsTotal    = $result->count();
        $recordsFiltered = $recordsTotal;

        $result = $result
            ->sortByDesc(fn($r) => strtotime((string) ($r['tanggal'] ?? '1970-01-01')))
            ->slice($start, $length)
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result,
        ]);
    }

    public function show($id)
    {
        // try treat $id as order id first
        $order = null;
        try {
            $order = Order::with(['detailOrders', 'sales', 'toko'])->find($id);
        } catch (\Throwable $e) {
            $order = null;
        }

        if ($order) {
            $items = $order->detailOrders->map(function ($d) {
                return [
                    'nama' => $d->product->nama ?? $d->nama ?? $d->id_produk ?? '-',
                    'qty' => (int) ($d->jumlah ?? 0),
                    'harga' => (float) ($d->harga ?? 0),
                    'sub_total' => ((int) ($d->jumlah ?? 0)) * ((float) ($d->harga ?? 0))
                ];
            })->toArray();

            $payments = [];
            if (Schema::hasTable('pembayaran_cash')) {
                $cash = DB::table('pembayaran_cash')->where('id_order', $order->id)->orderBy('tanggal_pembayaran', 'asc')->get();
                foreach ($cash as $c) {
                    $payments[] = [
                        'id' => $c->id,
                        'method' => 'cash',
                        'nominal' => (float) $c->nominal,
                        'petugas' => $c->petugas_pembayaran ?? null,
                        'tanggal' => (string) ($c->tanggal_pembayaran ?? $c->tanggal_buat ?? null),
                    ];
                }
            }
            if (Schema::hasTable('pembayaran_va')) {
                $va = DB::table('pembayaran_va')
                    ->where('id_order', $order->id)
                    ->select([
                        'id',
                        'id_order',
                        'nominal',
                        'status', // ⬅️ INI KUNCI UTAMA
                        DB::raw("'va' as method"),
                        'channel as petugas',
                        'tanggal_transaksi as tanggal',
                        'payment_link',
                        'nomor_ref',
                    ])
                    ->orderBy('tanggal_transaksi', 'asc')
                    ->get()

                    ->map(fn($r) => (array) $r);


                foreach ($va as $v) {
                    $payments[] = [
                        'id' => $v->id,
                        'method' => 'va',
                        'status' => $v->status,          // ⬅️ INI YANG HILANG SELAMA INI
                        'nominal' => (float) $v->nominal,
                        'petugas' => $v->petugas ?? null,
                        'tanggal' => (string) ($v->tanggal ?? null),
                        'payment_link' => $v->payment_link ?? null,
                        'nomor_ref' => $v->nomor_ref ?? null,
                    ];
                }
            }

            usort($payments, function ($a, $b) {
                return strtotime($a['tanggal'] ?? '1970-01-01') <=> strtotime($b['tanggal'] ?? '1970-01-01');
            });

            $total = method_exists($order, 'totalAmount') ? $order->totalAmount() : (
                isset($order->total) && $order->total !== null ? (float) $order->total :
                array_reduce($items, function ($c, $it) {
                    return $c + (($it['qty'] ?? 0) * ($it['harga'] ?? 0));
                }, 0)
            );

            $paid = array_reduce($payments, function ($c, $p) {
                if ($p['method'] === 'cash') {
                    return $c + (float) $p['nominal'];
                }
                if ($p['method'] === 'va' && ($p['status'] ?? '') === 'paid') {
                    return $c + (float) $p['nominal'];
                }
                return $c;
            }, 0);


            $salesPhone = optional($order->sales)->no_hp ?? optional($order->sales)->phone ?? null;
            if (empty($salesPhone) && !empty($order->id_sales)) {
                try {
                    $k = \App\Models\Karyawan::find($order->id_sales);
                    if ($k)
                        $salesPhone = $k->no_hp ?? $k->phone ?? $salesPhone;
                } catch (\Throwable $e) {
                }
            }

            $resp = [
                'id' => $order->id,
                'kode' => $order->kode ?? $order->id,
                'created_at' => (string) ($order->created_at ?? $order->tanggal_buat ?? null),
                'sales_name' => optional($order->sales)->nama ?? optional($order->sales)->name ?? $order->id_sales,
                'sales_phone' => $salesPhone, // <- tambahkan
                'toko_name' => optional($order->toko)->nama_toko ?? optional($order->toko)->nama ?? $order->id_toko,
                'items' => $items,
                'payments' => $payments,
                'total' => $total,
                'sudah_bayar' => $paid,
                'kurang' => max(0, $total - $paid),
                'is_lunas' => ($paid >= $total)
            ];

            return response()->json(['success' => true, 'order' => $resp]);
        }

        // fallback payment-id behavior (tidak berubah, tapi sertakan sales_phone jika ketemu order)
        $pay = PembayaranCash::with('order')->find($id);
        $type = 'cash';
        if (!$pay) {
            $pay = PembayaranVa::with('order')
                ->where('id', $id)
                ->where('status', 'paid')   // ⬅️ KUNCI UTAMA
                ->first();

            $type = 'va';
        }
        if (!$pay) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $order = $pay->order;
        $items = [];
        if ($order) {
            $items = $order->detailOrders()->get()->map(function ($d) {
                return [
                    'nama' => $d->product->nama ?? $d->nama ?? '-',
                    'qty' => (int) ($d->jumlah ?? 0),
                    'harga' => (float) ($d->harga ?? 0),
                    'sub_total' => ((int) ($d->jumlah ?? 0)) * ((float) ($d->harga ?? 0)),
                ];
            })->toArray();
        }

        $salesPhone = null;
        if ($order) {
            $salesPhone = optional($order->sales)->no_hp ?? optional($order->sales)->phone ?? null;
            if (empty($salesPhone) && !empty($order->id_sales)) {
                try {
                    $k = \App\Models\Karyawan::find($order->id_sales);
                    if ($k)
                        $salesPhone = $k->no_hp ?? $k->phone ?? $salesPhone;
                } catch (\Throwable $e) {
                }
            }
        }

        $resp = [
            'success' => true,
            'payment' => [
                'id' => $pay->id,
                'method' => $type,
                'nominal' => (float) ($pay->nominal ?? 0),
                'petugas' => $pay->petugas_pembayaran ?? $pay->channel ?? null,
                'tanggal' => $pay->tanggal_pembayaran ?? $pay->tanggal_transaksi ?? $pay->tanggal_buat ?? null,
            ],
            'order' => [
                'id' => $order->id ?? null,
                'kode' => $order->kode ?? null,
                'created_at' => (string) ($order->created_at ?? null),
                'sales_name' => optional($order->sales)->nama ?? optional($order->sales)->name ?? $order->id_sales ?? null,
                'sales_phone' => $salesPhone, // <- tambahkan
                'toko_name' => optional($order->toko)->nama_toko ?? optional($order->toko)->nama ?? $order->id_toko ?? null,
                'items' => $items,
            ],
        ];

        return response()->json($resp);
    }

    //untuk filter
    public function filterSales(Request $req)
    {
        // $q = Karyawan::query()
        //     ->select(['id', 'nama'])
        //     ->orderBy('nama');
        $q = Karyawan::query()
            ->join('jabatan as j', 'j.id', '=', 'karyawan.id_jabatan')
            ->where('j.nama', '=', 'sales')
            ->select('karyawan.id', 'karyawan.nama')
            ->orderBy('karyawan.nama');
        // // optional: filter active / role if needed
        // if ($req->filled('active')) {
        //     $q->where('status', $req->input('active') ? 'Y' : 'N');
        // }

        $items = $q->get()->map(function ($k) {
            return ['id' => $k->id, 'name' => $k->nama];
        })->values();

        return response()->json(['data' => $items]);
    }

    public function detailNota()
    {
        return view('penjualan::detailpelunasan');
    }
    public function notaPdf($id)
    {
        // Ambil order + relasi yang BENAR
        $order = \App\Models\Order::with([
            'detailOrders.product', // kalau ada relasi product
            'sales',
            'toko'
        ])->findOrFail($id);

        /* ===============================
         |  GABUNGKAN PEMBAYARAN
         =============================== */
        $payments = collect();

        // CASH
        if (Schema::hasTable('pembayaran_cash')) {
            $cash = DB::table('pembayaran_cash as pc')
                ->join('akun as a', 'a.id_karyawan', '=', 'pc.petugas_pembayaran')
                ->join('karyawan as k', 'k.id', '=', 'a.id_karyawan')
                ->where('pc.id_order', $order->id)
                ->select([
                    'pc.id',
                    'pc.nominal',
                    'k.nama as nama_petugas',
                    'pc.tanggal_pembayaran as tanggal'
                ])
                ->orderBy('pc.tanggal_pembayaran', 'asc')
                ->get()
                ->map(function ($r) {
                    return (object) [
                        'id' => $r->id,
                        'method' => 'CASH',
                        'nominal' => (float) $r->nominal,
                        'petugas' => $r->nama_petugas,
                        'tanggal' => $r->tanggal,
                    ];
                });

            $payments = $payments->merge($cash);
        }

        // VA (hanya PAID)
        if (Schema::hasTable('pembayaran_va')) {
            $va = DB::table('pembayaran_va')
                ->where('id_order', $order->id)
                ->where('status', 'paid')
                ->select([
                    'id',
                    'nominal',
                    'channel as metode',
                    'tanggal_transaksi as tanggal'
                ])
                ->orderBy('tanggal_transaksi', 'asc')
                ->get()
                ->map(function ($r) {
                    return (object) [
                        'id' => $r->id,
                        'method' => $r->metode,
                        'nominal' => (float) $r->nominal,
                        'petugas' => 'Online',
                        'tanggal' => $r->tanggal,
                    ];
                });

            $payments = $payments->merge($va);
        }

        // Urutkan pembayaran berdasarkan tanggal
        $payments = $payments->sortBy(function ($p) {
            return strtotime((string) ($p->tanggal ?? '1970-01-01'));
        })->values();

        /* ===============================
         |  HITUNG TOTAL
         =============================== */
        $totalOrder = method_exists($order, 'totalAmount')
            ? $order->totalAmount()
            : $order->detailOrders->sum(function ($d) {
                return ($d->jumlah ?? 0) * ($d->harga ?? 0);
            });

        $totalBayar = $payments->sum('nominal');

        /* ===============================
         |  DATA UNTUK VIEW
         =============================== */
        $data = [
            'order' => $order,
            'payments' => $payments,
            'total' => $totalOrder,
            'totalBayar' => $totalBayar,
            'generated_at' => now(),
        ];

        /* ===============================
         |  RENDER PDF
         =============================== */
        $html = view('penjualan::cetaknota', $data)->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 16,
            'margin_bottom' => 16,
        ]);

        // CSS ringan (biar konsisten di PDF)
        $css = '
        body { font-family: sans-serif; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f5f5f5; font-weight: bold; }
    ';
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        $filename = 'nota_pelunasan_' . ($order->kode ?? $order->id) . '.pdf';

        return $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
    }

}
