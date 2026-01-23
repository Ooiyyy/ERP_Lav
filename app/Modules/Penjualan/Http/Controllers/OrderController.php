<?php

namespace App\Modules\Penjualan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Produk;
use App\Models\DetailOrder;
use App\Models\PembayaranCash;
use App\Models\Karyawan;
use App\Models\Toko; // jika ada model Toko, include; jika tidak ada, itu tidak fatal (cek di runtime)
use Illuminate\Support\Str;
use Throwable;

class OrderController
{
    public function index(Request $req)
    {
        $products = Produk::orderBy('id')->get();
        $productsMapped = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->nama ?? $p->name ?? '',
                'harga' => (int) ($p->harga ?? 0),
                'stok' => (int) ($p->stok ?? 0),
                'satuan' => $p->satuan ?? '',
            ];
        });

        // Ambil tokos (prefer model Toko kalau ada, kalau tidak pakai query DB)
        if (class_exists(\App\Models\Toko::class)) {
            // $tokos = Toko::orderBy('id')->get();
        } elseif (Schema::hasTable('toko')) {
            $tokos = DB::table('toko')->orderBy('id')->get();
        } else {
            $tokos = collect();
        }

        // Ambil daftar sales: join karyawan -> jabatan
        $sales = collect();

        try {
            if (Schema::hasTable('karyawan') && Schema::hasTable('jabatan')) {
                $sales = DB::table('karyawan')
                    ->join('jabatan', 'jabatan.id', '=', 'karyawan.id_jabatan')
                    ->leftJoin('akun', 'akun.id_karyawan', '=', 'karyawan.id') // optional mapping akun
                    ->whereRaw('LOWER(jabatan.nama) = ?', ['sales'])
                    ->select(
                        'karyawan.id as id',         // <-- penting: alias jadi id
                        'karyawan.nama as nama',
                        'karyawan.no_hp as no_hp',
                        'akun.id_karyawan as akun_id_karyawan' // optional, jangan pakai untuk value
                    )
                    ->orderBy('karyawan.nama')
                    ->get();
            }
        } catch (\Throwable $e) {
            $sales = collect();
        }

        // ambil orders dengan relasi sales & toko (pastikan relasi di model Order ada)
        $orders = Order::with(['detailOrders', 'sales', 'toko'])
            ->orderBy('tanggal_buat', 'desc')
            ->paginate(15);

        return view('penjualan::pesanan', [
            'products' => $products,
            'productsMapped' => $productsMapped,
            // 'tokos' => $tokos,
            'sales' => $sales,
            'orders' => $orders,
        ]);
    }

    public function data(Request $req)
    {
        $draw = (int) $req->input('draw', 1);
        $start = (int) $req->input('start', 0);
        $length = (int) $req->input('length', 10);
        $search = trim((string) $req->input('search.value', ''));

        // =====================================================
        // 1) Ambil semua order + relasi
        // =====================================================
        $orders = Order::with(['detailOrders', 'sales', 'toko'])
            ->orderBy('tanggal_buat', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        // =====================================================
        // 2) Hitung total & paid per order
        // =====================================================
        $result = $orders->map(function ($order) {

            $total = (int) $order->detailOrders->sum(function ($d) {
                return ((int) ($d->jumlah ?? 0)) * ((int) ($d->harga ?? 0));
            });

            $paidCash = (int) DB::table('pembayaran_cash')
                ->where('id_order', $order->id)
                ->sum('nominal');

            $paidVa = (int) DB::table('pembayaran_va')
                ->where('id_order', $order->id)
                ->where('status', 'paid')
                ->sum('nominal');

            $paid = $paidCash + $paidVa;

            $remaining = max(0, $total - $paid);
            $isLunas = ($total > 0 && $paid >= $total);

            $salesName = optional($order->sales)->nama
                ?? optional($order->sales)->name
                ?? optional($order->sales)->nama_sales
                ?? $order->id_sales;

            $salesPhone = optional($order->sales)->no_hp
                ?? optional($order->sales)->phone
                ?? null;

            $tokoName = optional($order->toko)->nama_toko
                ?? optional($order->toko)->nama
                ?? $order->id_toko;

            // tombol action (tetap punyamu)
            $shouldDisableActions = ($paid > 0 && $remaining > 0);
            $disabledAttr = $shouldDisableActions ? ' disabled aria-disabled="true"' : '';
            $disabledClass = $shouldDisableActions ? ' disabled' : '';

            $actions = '
                <button type="button" class="btn btn-sm btn-success btn-detail" data-id="' . e($order->id) . '">Detail</button>
                <button type="button" class="btn btn-sm btn-primary btn-edit' . $disabledClass . '" data-id="' . e($order->id) . '"' . $disabledAttr . '>Edit</button>
                <button type="button" class="btn btn-sm btn-danger btn-delete' . $disabledClass . '" data-id="' . e($order->id) . '"' . $disabledAttr . '>Hapus</button>
            ';

            return [
                'id' => $order->id,
                'created_at' => $order->tanggal_buat,
                'sales_name' => $salesName,
                'sales_phone' => $salesPhone,
                'toko_name' => $tokoName,
                'calculated_total' => $total,
                'paid' => $paid,
                'remaining' => $remaining,
                'is_lunas' => $isLunas ? 1 : 0,
                'actions' => $actions,
            ];
        });

        // =====================================================
        // 3) Filter: tampilkan hanya PESANAN BELUM LUNAS
        // =====================================================
        $result = $result->filter(fn($r) => (int) $r['is_lunas'] === 0)->values();

        // =====================================================
        // 4) Search DataTables (seperti History)
        // =====================================================
        if ($search !== '') {
            $s = mb_strtolower($search);

            $result = $result->filter(function ($r) use ($s) {
                foreach (['id', 'created_at', 'sales_name', 'sales_phone', 'toko_name', 'calculated_total', 'paid', 'remaining'] as $f) {
                    if (mb_stripos((string) ($r[$f] ?? ''), $s) !== false) {
                        return true;
                    }
                }
                return false;
            })->values();
        }

        // =====================================================
        // 5) Records + Pagination
        // =====================================================
        $recordsTotal = $result->count();
        $recordsFiltered = $recordsTotal;

        $data = $result->slice($start, $length)->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function dataModal()
    {
        // PRODUK (dinamis: stok)
        $products = Produk::orderBy('id')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->nama ?? $p->name ?? '',
                'harga' => (int) ($p->harga ?? 0),
                'stok' => (int) ($p->stok ?? 0),
                'satuan' => $p->satuan ?? '',
            ];
        });

        // TOKO (referensi)
        $tokos = collect();
        if (Schema::hasTable('toko')) {
            $tokos = DB::table('toko')
                ->select('id', 'nama_toko as nama')
                ->orderBy('id')
                ->get();
        }

        // SALES (referensi)
        $sales = collect();
        if (Schema::hasTable('karyawan') && Schema::hasTable('jabatan')) {
            $sales = DB::table('karyawan')
                ->join('jabatan', 'jabatan.id', '=', 'karyawan.id_jabatan')
                ->whereRaw('LOWER(jabatan.nama) = ?', ['sales'])
                ->select(
                    'karyawan.id',
                    'karyawan.nama',
                    'karyawan.no_hp'
                )
                ->orderBy('karyawan.nama')
                ->get();
        }

        return response()->json([
            'products' => $products,
            'tokos' => $tokos,
            'sales' => $sales,
        ]);
    }

    public function create()
    {
        $products = Produk::orderBy('id', 'asc')->paginate(12);

        if (class_exists(\App\Models\Toko::class)) {
            $tokos = Toko::orderBy('id')->get();
        } elseif (Schema::hasTable('toko')) {
            $tokos = DB::table('toko')->orderBy('id')->get();
        } else {
            $tokos = collect();
        }

        return view('penjualan::pesanan.create', compact('products', 'tokos'));
    }

    public function tokoBySales($id_sales)
    {
        if (!Schema::hasTable('toko')) {
            return response()->json([]);
        }

        $tokos = DB::table('toko')
            ->where('id_karyawan', $id_sales)
            ->select('id', 'nama_toko')
            ->orderBy('id')
            ->get();

        return response()->json($tokos);
    }


    public function store(Request $req)
    {

        $rules = [
            'id_toko' => 'nullable|string',
            'id_sales' => 'nullable|string', // optional; kita cek keberadaan manual
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 422);
        }

        // cek keberadaan karyawan jika id_sales diberikan
        if ($req->filled('id_sales') && $req->filled('id_toko')) {
            $valid = DB::table('toko')
                ->where('id', $req->id_toko)
                ->where('id_karyawan', $req->id_sales)
                ->exists();

            if (!$valid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak sesuai dengan sales yang dipilih.'
                ], 422);
            }
        }


        $validator = Validator::make($req->all(), [
            'id_toko' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 422);
        }

        DB::beginTransaction();
        try {
            // --- Generate sequential order id like ORD-01, ORD-02, ...
            // Lock table selection to reduce race conditions
            $orderModel = new Order();
            $table = $orderModel->getTable();

            // Ambil nilai max sequence numeric dari id yang berformat ORD-{number}
            // SUBSTRING_INDEX(id, '-', -1) memberi bagian setelah '-' ; CAST ke UNSIGNED
            $row = DB::table($table)
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)) as max_seq")
                ->where('id', 'like', 'ORD-%')
                ->lockForUpdate()
                ->first();

            $maxSeq = 0;
            if ($row && isset($row->max_seq) && is_numeric($row->max_seq)) {
                $maxSeq = (int) $row->max_seq;
            }

            $nextSeq = $maxSeq + 1;
            // format 2 digit (01, 02). Ubah 2 => 3 jika mau ORD-001
            $orderId = 'ORD-' . str_pad($nextSeq, 1, STR_PAD_LEFT);

            // --- end generate id ---

            $attrs = [
                'id' => $orderId,
                'id_sales' => $req->id_sales ?? auth('web')->id() ?? null,
                'id_toko' => $req->id_toko ?: null,
                'id_buat' => $req->id_buat ?? auth('web')->id() ?? null,
                'id_update' => $req->id_update ?? auth('web')->id() ?? null,
                'tanggal_buat' => now(),
            ];

            $order = Order::create($attrs);

            $total = 0;
            foreach ($req->items as $idx => $it) {
                $prodId = $it['id_produk'] ?? null;
                $qty = (int) ($it['jumlah'] ?? 0);
                $unitPrice = (int) ($it['harga'] ?? 0);
                $satuan = $it['satuan'] ?? null;

                if (!$prodId || $qty <= 0) {
                    throw new \Exception("Item ke-{$idx} tidak lengkap (produk/qty).");
                }

                $prod = Produk::where('id', $prodId)->lockForUpdate()->first();
                if (!$prod)
                    throw new \Exception("Produk dengan ID {$prodId} tidak ditemukan.");
                if (((int) $prod->stok) < $qty)
                    throw new \Exception("Stok produk \"{$prod->nama}\" tidak mencukupi. (tersedia: {$prod->stok})");

                $detailId = 'D' . Str::upper(Str::random(10));
                $subtotal = $qty * $unitPrice;

                DetailOrder::create([
                    'id' => $detailId,
                    'id_transaksi' => $orderId,
                    'id_produk' => $prodId,
                    'jumlah' => $qty,
                    'harga' => $unitPrice,
                    'satuan' => $satuan,
                    'subtotal' => $subtotal,
                    'id_buat' => auth('web')->id(),
                    'tanggal_buat' => now(),
                ]);

                $prod->stok = max(0, ((int) $prod->stok) - $qty);
                $prod->save();

                $total += $subtotal;
            }

            $table = $order->getTable();
            if (Schema::hasColumn($table, 'total')) {
                $order->total = $total;
                $order->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan dibuat dan total tersimpan.',
                'order_id' => $orderId,
                'total' => $total,
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function edit(Request $req, $id)
    {
        $order = Order::with('detailOrders')->findOrFail($id);

        if ($req->ajax()) {
            return response()->json([
                'id' => $order->id,
                'id_sales' => $order->id_sales,
                'id_toko' => $order->id_toko,
                'items' => $order->detailOrders->map(function ($d) {
                    return [
                        'id' => $d->id,
                        'id_produk' => $d->id_produk,
                        'jumlah' => (int) $d->jumlah,
                        'harga' => (int) $d->harga,
                        'satuan' => $d->satuan ?? '',
                        'subtotal' => (int) ($d->subtotal ?? ((int) $d->jumlah * (int) $d->harga)),
                    ];
                })->values(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Endpoint expects AJAX'], 400);
    }

    public function update(Request $req, $id)
    {

        $rules = [
            'id_toko' => 'nullable|string',
            'id_sales' => 'nullable|string', // optional; kita cek keberadaan manual
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 422);
        }

        // cek keberadaan karyawan jika id_sales diberikan
        if ($req->filled('id_sales') && $req->filled('id_toko')) {
            $valid = DB::table('toko')
                ->where('id', $req->id_toko)
                ->where('id_karyawan', $req->id_sales)
                ->exists();

            if (!$valid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak sesuai dengan sales yang dipilih.'
                ], 422);
            }
        }


        $validator = Validator::make($req->all(), [
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 422);
        }

        $rawItems = $req->input('items', []);
        $items = array_values(array_filter($rawItems, function ($it) {
            return is_array($it)
                && array_key_exists('id_produk', $it)
                && array_key_exists('jumlah', $it)
                && array_key_exists('harga', $it)
                && trim((string) $it['id_produk']) !== ''
                && (int) $it['jumlah'] > 0;
        }));

        if (count($items) === 0) {
            return response()->json(['success' => false, 'message' => 'Tidak ada item pesanan yang valid untuk diupdate.'], 422);
        }

        DB::beginTransaction();
        try {
            $order = Order::with('detailOrders')->findOrFail($id);

            foreach ($order->detailOrders as $old) {
                $prod = Produk::where('id', $old->id_produk)->lockForUpdate()->first();
                if ($prod) {
                    $prod->stok = ((int) $prod->stok) + (int) $old->jumlah;
                    $prod->save();
                }
            }

            DetailOrder::where('id_transaksi', $id)->delete();

            $total = 0;
            foreach ($items as $idx => $it) {
                $prodId = $it['id_produk'] ?? null;
                $qty = (int) ($it['jumlah'] ?? 0);
                $unitPrice = (int) ($it['harga'] ?? 0);
                $satuan = $it['satuan'] ?? null;

                if (!$prodId || $qty <= 0) {
                    throw new \Exception("Item ke-" . ($idx + 1) . " tidak lengkap (produk/jumlah).");
                }

                $prod = Produk::where('id', $prodId)->lockForUpdate()->first();
                if (!$prod)
                    throw new \Exception("Produk dengan ID {$prodId} tidak ditemukan.");
                if (((int) $prod->stok) < $qty)
                    throw new \Exception("Stok produk \"{$prod->nama}\" tidak mencukupi untuk update. (tersedia: {$prod->stok})");

                $subtotal = $qty * $unitPrice;
                DetailOrder::create([
                    'id' => 'D' . Str::upper(Str::random(10)),
                    'id_transaksi' => $id,
                    'id_produk' => $prodId,
                    'jumlah' => $qty,
                    'harga' => $unitPrice,
                    'satuan' => $satuan,
                    'subtotal' => $subtotal,
                ]);

                $prod->stok = ((int) $prod->stok) - $qty;
                $prod->save();

                $total += $subtotal;
            }

            if ($req->filled('id_sales'))
                $order->id_sales = $req->id_sales;
            if ($req->filled('id_toko'))
                $order->id_toko = $req->id_toko;
            $order->id_update = $req->id_update ?? $order->id_update;

            $table = $order->getTable();
            if (Schema::hasColumn($table, 'total')) {
                $order->total = $total;
            }

            $order->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pesanan berhasil diupdate.', 'order_id' => $order->id, 'total' => $total]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $order = Order::with('detailOrders')->findOrFail($id);

            foreach ($order->detailOrders as $old) {
                $prod = Produk::where('id', $old->id_produk)->lockForUpdate()->first();
                if ($prod) {
                    $prod->stok = ((int) $prod->stok) + (int) $old->jumlah;
                    $prod->save();
                }
            }

            DetailOrder::where('id_transaksi', $id)->delete();
            $order->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pesanan dihapus dan stok dikembalikan.', 'order_id' => $id]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function detail($id)
    {
        $order = Order::with(['detailOrders', 'toko', 'sales'])->find($id);
        if (!$order)
            return response()->json(['message' => 'Order not found'], 404);

        $items = $order->detailOrders->map(function ($d) {
            return [
                'nama' => $d->product->nama ?? $d->nama ?? $d->id_produk ?? '-',
                'jumlah' => (int) ($d->jumlah ?? 0),
                'satuan' => $d->satuan ?? ($d->product->satuan ?? '-'),
                'harga_satuan' => (int) ($d->harga ?? ($d->product->harga ?? 0)),
            ];
        })->toArray();

        // sales name (same as sebelum)
        $salesName = optional($order->sales)->nama
            ?? optional($order->sales)->name
            ?? $order->id_sales
            ?? '-';

        // toko name: cek relasi, lalu tabel 'toko' (jika ada), lalu fallback id
        $tokoName = optional($order->toko)->nama_toko
            ?? optional($order->toko)->nama
            ?? null;

        if (!$tokoName) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('toko')) {
                    $tokoFromTable = \Illuminate\Support\Facades\DB::table('toko')
                        ->where('id', $order->id_toko)
                        ->select('nama_toko', 'nama')
                        ->first();
                    if ($tokoFromTable) {
                        $tokoName = $tokoFromTable->nama_toko ?? $tokoFromTable->nama ?? null;
                    }
                }
            } catch (\Throwable $e) {
                // ignore DB error, fallback below
                $tokoName = $tokoName ?? null;
            }
        }

        $tokoName = $tokoName ?? $order->id_toko ?? '-';

        return response()->json([
            'sales_name' => $salesName,
            'sales_phone' => optional($order->sales)->no_hp ?? null,
            'toko_name' => $tokoName,
            'items' => $items,
        ]);
    }


    public function summary($id)
    {
        $order = Order::with('detailOrders')->findOrFail($id);
        $total = (int) $order->detailOrders->sum(fn($d) => ((int) $d->jumlah * (int) $d->harga));

        $paidCash = (int) DB::table('pembayaran_cash')
            ->where('id_order', $order->id)
            ->sum('nominal');

        $paidVa = (int) DB::table('pembayaran_va')
            ->where('id_order', $order->id)
            ->where('status', 'paid')
            ->sum('nominal');

        $paid = $paidCash + $paidVa;

        $remaining = max(0, $total - $paid);
        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'total' => $total,
            'paid' => $paid,
            'remaining' => $remaining,
        ]);
    }

    public function payments($id)
    {
        $order = Order::with('detailOrders')->find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan'], 404);
        }

        $payments = [];

        /**
         * =====================
         * CASH
         * =====================
         */
        if (Schema::hasTable('pembayaran_cash')) {
            $cash = DB::table('pembayaran_cash')
                ->leftJoin('karyawan', 'karyawan.id', '=', 'pembayaran_cash.petugas_pembayaran')
                ->where('id_order', $id)
                ->select(
                    'pembayaran_cash.id',
                    'pembayaran_cash.nominal',
                    'pembayaran_cash.tanggal_pembayaran',
                    'karyawan.nama as petugas_nama'
                )
                ->orderBy('tanggal_pembayaran', 'asc')
                ->get();

            foreach ($cash as $c) {
                $payments[] = [
                    'id' => $c->id,
                    'nominal' => (int) $c->nominal,
                    'tanggal' => $c->tanggal_pembayaran,
                    'method' => 'CASH',
                    'petugas' => $c->petugas_nama ?? 'PETUGAS',
                ];
            }
        }

        /**
         * =====================
         * VA / MIDTRANS
         * =====================
         */
        if (Schema::hasTable('pembayaran_va')) {
            $va = DB::table('pembayaran_va')
                ->where('id_order', $id)
                ->where('status', 'paid')
                ->select(
                    'id',
                    'nominal',
                    'tanggal_transaksi'
                )
                ->orderBy('tanggal_transaksi', 'asc')
                ->get();

            foreach ($va as $v) {
                $payments[] = [
                    'id' => $v->id,
                    'nominal' => (int) $v->nominal,
                    'tanggal' => $v->tanggal_transaksi,
                    'method' => 'ONLINE',
                    'petugas' => 'ONLINE',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }


    public function storeCash(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'order_id' => 'required|exists:order,id',
            'jumlah_bayar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 422);
        }

        $raw = (string) ($req->jumlah_bayar ?? $req->nominal ?? '0');
        $nominal = intval(preg_replace('/\D/', '', $raw));

        if ($nominal < 1) {
            return response()->json(['success' => false, 'message' => 'Nominal harus minimal 1.'], 422);
        }

        DB::beginTransaction();
        try {
            $p = PembayaranCash::create([
                'id' => 'PC' . Str::upper(Str::random(10)),
                'id_order' => $req->order_id,
                'nominal' => $nominal,
                'petugas_pembayaran' => auth('web')->id(),
                'tanggal_pembayaran' => $req->tanggal_pembayaran ?? now(),
                'id_buat' => auth('web')->id() ?? null,
                'id_update' => auth('web')->id() ?? null,
            ]);

            DB::commit();

            // recalc
            // recalc total
            $order = Order::with('detailOrders')->findOrFail($req->order_id);

            $totalOrder = (int) DB::table('detail_order')
                ->where('id_transaksi', $order->id)
                ->selectRaw('COALESCE(SUM(jumlah * harga),0)')
                ->value('COALESCE(SUM(jumlah * harga),0)');

            $paidCash = (int) DB::table('pembayaran_cash')
                ->where('id_order', $order->id)
                ->sum('nominal');

            $paidVa = (int) DB::table('pembayaran_va')
                ->where('id_order', $order->id)
                ->where('status', 'paid')
                ->sum('nominal');

            $paid = $paidCash + $paidVa;

            $isFullyPaid = ($totalOrder > 0 && $paid >= $totalOrder);

            if ($isFullyPaid) {
                if (Schema::hasColumn($order->getTable(), 'status')) {
                    $order->status = 'lunas';
                    if (Schema::hasColumn($order->getTable(), 'tanggal_pelunasan')) {
                        $order->tanggal_pelunasan = now();
                    }
                    $order->save();
                }
            }


            return response()->json(['success' => true, 'paid' => $paid, 'total' => $totalOrder, 'isFullyPaid' => $isFullyPaid]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
