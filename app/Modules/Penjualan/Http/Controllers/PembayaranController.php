<?php

namespace App\Modules\Penjualan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\PembayaranCash;
use App\Models\PembayaranVa;
use App\Models\Karyawan;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

class PembayaranController
{
    public function index(Request $request)
    {
        $params = [
            'transaction_details' => [
                'order_id' => rand(),
                'gross_amount' => 10000,
            ],
            'customer_details' => [
                'first_name' => 'budi',
                'last_name' => 'pratama',
                'email' => 'budi.pra@example.com',
                'phone' => '08111222333',
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        return view('penjualan::pembayaran', ['snap_token' => $snapToken]);
    }

    /**
     * Generate Snap token untuk jumlah yang user input (jumlah_bayar).
     * Dipanggil via AJAX POST dari frontend.
     */
    public function createSnapToken(Request $request)
    {
        $v = Validator::make($request->all(), [
            'order_id'     => 'required|exists:order,id',
            'jumlah_bayar' => 'required|numeric|min:100',
        	'id_sales' => 'nullable|string',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $v->errors()->all()
            ], 422);
        }
    	$idSales = $request->filled('id_sales')? $request->id_sales: auth('web')->id();


        $order = Order::with(['detailOrders.product', 'toko', 'sales'])
            ->findOrFail($request->order_id);

        $jumlahBayar = (int) round($request->jumlah_bayar);

        $total = $order->detailOrders->sum(fn($d) => ((int)$d->jumlah * (int)$d->harga));

        $paidCash = DB::table('pembayaran_cash')->where('id_order', $order->id)->sum('nominal');
        $paidVa   = DB::table('pembayaran_va')->where('id_order', $order->id)->where('status','paid')->sum('nominal');

        $remaining = max(0, $total - ($paidCash + $paidVa));
        if ($remaining <= 0) {
            return response()->json(['success'=>false,'message'=>'Order sudah lunas'],422);
        }

        if ($jumlahBayar > $remaining) $jumlahBayar = $remaining;

        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        // 🔥 BUAT SEKALI SAJA
        $midtransOrderId = 'VA-' . $order->id . '-' . Str::uuid();

        // EXPIRE PAYMENT LAMA (PENTING)
        // PembayaranVa::where('id_order', $order->id)
        //     ->where('status', 'pending')
        //     ->update(['status' => 'expired']);

        $itemDetails = [];

        foreach ($order->detailOrders as $d) {
            $itemDetails[] = [
                'id' => 'INFO-' . ($d->id_produk ?? Str::random(6)),
                'price' => 0,
                'quantity' => (int) $d->jumlah,
                'name' => substr($d->product->nama ?? 'Produk', 0, 30),
            ];
        }

        $itemDetails[] = [
            'id' => 'PAY-' . $order->id,
            'price' => $jumlahBayar,
            'quantity' => 1,
            'name' => 'Pembayaran Tagihan',
        ];

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => $jumlahBayar,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->sales->nama ?? 'Sales',
                'phone'      => $order->sales->no_hp ?? '',
            ],
        ];

        try {
            $snap = \Midtrans\Snap::createTransaction($params);

            PembayaranVa::create([
                'id'                => 'PV' . Str::upper(Str::random(10)),
                'id_order'          => $order->id,
                'nominal'           => $jumlahBayar,
                'channel'           => null,
                'tanggal_transaksi' => Carbon::now(),
                'payment_link'      => $snap->redirect_url,
                'nomor_ref'         => $midtransOrderId, // 🔥 MATCH
                'status'            => 'pending',
                //'id_buat'           => auth('web')->id(),
            	'id_buat'           => $idSales,
                'tanggal_buat'      => Carbon::now(),
            ]);

            return response()->json([
                'success'    => true,
                'snap_token' => $snap->token,
            ]);

        } catch (\Throwable $e) {
            \Log::error('MIDTRANS ERROR', ['msg'=>$e->getMessage()]);
            return response()->json(['success'=>false,'message'=>'Gagal membuat Snap'],500);
        }
    }



    /**
     * Data endpoint untuk DataTables
     */
    public function data(Request $req)
    {
        $start = (int) $req->input('start', 0);
        $length = (int) $req->input('length', 10);
        $search = trim((string) $req->input('search.value', ''));

        $baseQuery = Order::with(['detailOrders', 'sales', 'toko'])->orderBy('tanggal_buat', 'desc');

        if ($search !== '') {
            $baseQuery = $baseQuery->where(function ($w) use ($search) {
                $w->where('id', 'like', "%{$search}%")
                ->orWhere('id_toko', 'like', "%{$search}%")
                ->orWhereHas('toko', function ($t) use ($search) {
                    $t->where('nama', 'like', "%{$search}%")
                        ->orWhere('nama_toko', 'like', "%{$search}%");
                })
                ->orWhereHas('sales', function ($s) use ($search) {
                    $s->where('nama', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('id_karyawan', 'like', "%{$search}%");
                });
            });
        }

        $totalRecords = $baseQuery->count();
        $rows = $baseQuery->skip($start)->take($length)->get();

        $data = $rows->map(function ($order) {
            $total = method_exists($order, 'totalAmount') ? $order->totalAmount() : (
                isset($order->total) && $order->total !== null ? (float) $order->total :
                ($order->detailOrders->sum(function($d){ return ((float)($d->jumlah ?? 0) * (float)($d->harga ?? 0)); }) ?? 0)
            );

            $paidCash = Schema::hasTable('pembayaran_cash') ? (float) DB::table('pembayaran_cash')->where('id_order', $order->id)->sum('nominal') : 0;
            $paidVa = DB::table('pembayaran_va')
                ->where('id_order', $order->id)
                ->where('status', 'paid')
                ->sum('nominal');

            $paid = $paidCash + $paidVa;

            $remaining = max(0, $total - $paid);
            $isLunas = ($total > 0 && $paid >= $total);
            if ($isLunas) return null; // filter later

            $salesName = optional($order->sales)->nama
                        ?? optional($order->sales)->name
                        ?? optional($order->sales)->id_karyawan
                        ?? $order->id_sales;

            $tokoName = optional($order->toko)->nama_toko
                    ?? optional($order->toko)->nama
                    ?? $order->id_toko;

            // AMAN: ambil nomor hp dari relasi sales, fallback ke Karyawan model bila relasi null / kosong
            $salesPhone = optional($order->sales)->no_hp
                        ?? optional($order->sales)->phone
                        ?? null;

            if (empty($salesPhone) && !empty($order->id_sales)) {
                try {
                    // jika id_sales menyimpan PK karyawan
                    $k = Karyawan::find($order->id_sales);
                    if ($k) $salesPhone = $k->no_hp ?? $k->phone ?? $salesPhone;
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            // buat payload untuk data-order tapi encode aman untuk HTML
            $payload = [
                'id' => $order->id,
                'kode' => $order->kode ?? $order->id,
                'total' => $total,
                'sudah_bayar' => $paid,
                'kurang' => $remaining,
                'sales_phone' => $salesPhone,
            ];
            // json encode tanpa escape unicode / slashes supaya lebih ringkas
            $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $actions = '<button type="button" class="btn btn-sm btn-success btn-detail" data-id="' . e($order->id) . '" data-order=\'' . e($payloadJson) . '\'>Detail</button> '
                    . '<button type="button" class="btn btn-sm btn-info btn-bayar" data-id="' . e($order->id) . '" data-order=\'' . e($payloadJson) . '\'>Bayar</button>';

            return [
                'id' => $order->id,
                'created_at' => $order->tanggal_buat,
                'sales_name' => $salesName,
                'sales_phone' => $salesPhone,
                'toko_name' => $tokoName,
                'total' => $total,
                'paid' => $paid,
                'remaining' => $remaining,
                'is_lunas' => $isLunas ? 1 : 0,
                'actions' => $actions,
            ];
        })->filter()->values();

        return response()->json([
            'draw' => (int)$req->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $data->count(),
            'data' => $data,
        ]);
    }


    /**
     * Show detail + payment history (AJAX)
     */
    public function show($id)
    {
        $order = Order::with(['detailOrders', 'sales', 'toko'])->find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan'], 404);
        }

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
            $cash = DB::table('pembayaran_cash as pc')
                ->leftJoin('karyawan as k', 'k.id', '=', 'pc.petugas_pembayaran')
                ->where('pc.id_order', $id)
                ->orderBy('pc.tanggal_pembayaran','asc')
                ->select(
                    'pc.id',
                    'pc.nominal',
                    'pc.tanggal_pembayaran',
                    'k.nama as nama_petugas'
                )
                ->get();
        
            foreach ($cash as $c) {
                $payments[] = [
                    'id' => $c->id,
                    'method' => 'cash',
                    'nominal' => (float) $c->nominal,
                    'petugas' => $c->nama_petugas ?? '-',
                    'tanggal' => (string) $c->tanggal_pembayaran,
                ];
            }
        }
        
        if (Schema::hasTable('pembayaran_va')) {
            $va = DB::table('pembayaran_va')->where('id_order', $id)->orderBy('tanggal_transaksi','asc')->get();
            foreach ($va as $v) {
                $payments[] = [
                    'id' => $v->id,
                    'method' => 'va',
                    'channel' => $v->channel,
                    'nominal' => (float) $v->nominal,
                    'petugas' => $v->channel ?? null,
                    'tanggal' => (string) ($v->tanggal_transaksi ?? $v->tanggal_buat ?? null),
                    'payment_link' => $v->payment_link ?? null,
                    'status' => $v->status ?? 'pending',
                    'nomor_ref' => $v->nomor_ref ?? null,
                ];
            }
        }

        usort($payments, function($a,$b){
            return strtotime($a['tanggal'] ?? '1970-01-01') <=> strtotime($b['tanggal'] ?? '1970-01-01');
        });

        $total = method_exists($order, 'totalAmount') ? $order->totalAmount() : (
            isset($order->total) && $order->total !== null ? (float) $order->total :
            array_reduce($items, function($c,$it){ return $c + (($it['qty'] ?? 0) * ($it['harga'] ?? 0)); }, 0)
        );

        $paid = 0;
        foreach ($payments as $p) {
            if ($p['method'] === 'cash') {
                $paid += (float) $p['nominal'];
            } elseif ($p['method'] === 'va' && ($p['status'] ?? '') === 'paid') {
                $paid += (float) $p['nominal'];
            }
        }
        

        $salesPhone = optional($order->sales)->no_hp ?? optional($order->sales)->phone ?? null;
        if (empty($salesPhone) && !empty($order->id_sales)) {
            try {
                $k = Karyawan::find($order->id_sales);
                if ($k) $salesPhone = $k->no_hp ?? $k->phone ?? $salesPhone;
            } catch (\Throwable $e) {}
        }

        $resp = [
            'id' => $order->id,
            'kode' => $order->kode ?? $order->id,
            'created_at' => (string) ($order->created_at ?? $order->tanggal_buat ?? null),
            'sales_name' => optional($order->sales)->nama ?? optional($order->sales)->name ?? $order->id_sales,
            'sales_phone' => $salesPhone, // <-- pastikan ada
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
    

    /**
     * Simpan pembayaran cash (AJAX)
     */
    public function storeCash(Request $req)
    {
        $v = Validator::make($req->all(), [
            'order_id' => 'required|exists:order,id',
            'jumlah_bayar' => 'required|numeric|min:1',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()->all()], 422);

        $orderId = $req->input('order_id');
        $jumlah = (float) $req->input('jumlah_bayar');

        DB::beginTransaction();
        try {
            $order = Order::with('detailOrders')->lockForUpdate()->findOrFail($orderId);

            $total = method_exists($order, 'totalAmount') ? $order->totalAmount() : (
                isset($order->total) && $order->total !== null ? (float) $order->total :
                $order->detailOrders->sum(function($d){ return ((float)($d->jumlah ?? 0) * (float)($d->harga ?? 0)); })
            );

            $paidCash = Schema::hasTable('pembayaran_cash') ? (float) DB::table('pembayaran_cash')->where('id_order', $order->id)->sum('nominal') : 0;
            $paidVa = Schema::hasTable('pembayaran_va') 
            ? (float) DB::table('pembayaran_va')
                ->where('id_order', $order->id)
                ->where('status', 'paid')   // ⬅️ INI KUNCI
                ->sum('nominal')
            : 0;

            $paid = $paidCash + $paidVa;

            $sisa = max(0, $total - $paid);
            if ($sisa <= 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Order sudah lunas.'], 422);
            }

            $toSave = $jumlah > $sisa ? $sisa : $jumlah;

            $pc = PembayaranCash::create([
                'id' => 'PC' . Str::upper(Str::random(10)),
                'id_order' => $order->id,
                'nominal' => $toSave,
                'petugas_pembayaran' => auth('web')->id(),
                'tanggal_pembayaran' => Carbon::now(),
                'id_buat' => auth('web')->id() ?? null,
                'id_update' => auth('web')->id() ?? null,
                'tanggal_buat' => Carbon::now(),
            ]);

            $petugasNama = null;
            if ($pc->petugas_pembayaran) {
                $k = Karyawan::find($pc->petugas_pembayaran);
                $petugasNama = $k?->nama;
            }

            DB::commit();

            // recalc
            $paidCash = Schema::hasTable('pembayaran_cash') ? (float) DB::table('pembayaran_cash')->where('id_order', $order->id)->sum('nominal') : 0;
            $paidVa = DB::table('pembayaran_va')
                ->where('id_order', $order->id)
                ->where('status', 'paid')
                ->sum('nominal');

            $paid = $paidCash + $paidVa;
            $kurang = max(0, $total - $paid);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran tunai tersimpan.',
                'new_payment' => [
                    'id' => $pc->id,
                    'method' => 'cash',
                    'nominal' => $pc->nominal,
                    'petugas' => $petugasNama ?? '-',
                    'tanggal' => (string) $pc->tanggal_pembayaran
                ],
                
                'order' => [
                    'id' => $order->id,
                    'total' => $total,
                    'sudah_bayar' => $paid,
                    'kurang' => $kurang,
                ]
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()], 500);
        }
    }

    
    /**
     * Simpan pembayaran VA (AJAX)
     */
    public function storeVa(Request $req)
    {
        $v = Validator::make($req->all(), [
            'order_id' => 'required|exists:order,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'channel' => 'required|string',
            'payment_link' => 'nullable|string',
            'nomor_ref' => 'nullable|string',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()->all()], 422);

        $orderId = $req->input('order_id');
        $jumlah = (float) $req->input('jumlah_bayar');
        $channel = $req->input('channel');
        $payment_link = $req->input('payment_link', null);
        $nomor_ref = $req->input('nomor_ref', null);

        DB::beginTransaction();
        try {
            $order = Order::with('detailOrders')->lockForUpdate()->findOrFail($orderId);

            $total = method_exists($order, 'totalAmount') ? $order->totalAmount() : (
                isset($order->total) && $order->total !== null ? (float) $order->total :
                $order->detailOrders->sum(function($d){ return ((float)($d->jumlah ?? 0) * (float)($d->harga ?? 0)); })
            );

            $paidCash = Schema::hasTable('pembayaran_cash') ? (float) DB::table('pembayaran_cash')->where('id_order', $order->id)->sum('nominal') : 0;
            $paidVa = Schema::hasTable('pembayaran_va') 
            ? (float) DB::table('pembayaran_va')
                ->where('id_order', $order->id)
                ->where('status', 'paid')   // ⬅️ WAJIB
                ->sum('nominal')
            : 0;

            $paid = $paidCash + $paidVa;

            $sisa = max(0, $total - $paid);
            if ($sisa <= 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Order sudah lunas.'], 422);
            }

            $toSave = $jumlah > $sisa ? $sisa : $jumlah;

            $pv = PembayaranVa::create([
                'id' => 'PV' . Str::upper(Str::random(10)),
                'id_order' => $order->id,
                'nominal' => $toSave,
                'channel' => $channel,
                'tanggal_transaksi' => Carbon::now(),
                'payment_link' => $payment_link,
                'nomor_ref' => $nomor_ref,
                'id_buat' => auth('web')->id() ?? null,
                'id_update' => auth('web')->id() ?? null,
                'tanggal_buat' => Carbon::now(),
            ]);

            DB::commit();

            $paidCash = Schema::hasTable('pembayaran_cash') ? (float) DB::table('pembayaran_cash')->where('id_order', $order->id)->sum('nominal') : 0;
            $paidVa = Schema::hasTable('pembayaran_va') ? (float) DB::table('pembayaran_va')->where('id_order', $order->id)->sum('nominal') : 0;
            $paid = $paidCash + $paidVa;
            $kurang = max(0, $total - $paid);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran VA tersimpan.',
                'new_payment' => [
                    'id' => $pv->id,
                    'method' => 'va',
                    'nominal' => $pv->nominal,
                    'channel' => $pv->channel,
                    'tanggal' => (string) $pv->tanggal_transaksi,
                    'payment_link' => $pv->payment_link,
                    'nomor_ref' => $pv->nomor_ref,
                ],
                'order' => [
                    'id' => $order->id,
                    'total' => $total,
                    'sudah_bayar' => $paid,
                    'kurang' => $kurang,
                ]
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan pembayaran VA: ' . $e->getMessage()], 500);
        }
    }
}
