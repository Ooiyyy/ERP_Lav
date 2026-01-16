@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Manajemen Pesanan</h3>
            <div>
                <button id="btnAddOrder" class="btn btn-primary" type="button">Tambah Pesanan</button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <div class="table-responsive">
                <table id="ordersTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th >ID Transaksi</th>
                            <th >Tanggal Buat</th>
                            <th >Nama Sales</th>
                            <th >Nomor Telepon</th>
                            <th >Nama Toko</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        <div class="mt-2" id="datatablePager"></div>


        {{-- MODAL Add --}}
        <div id="modalAddOrder" class="modal custom-modal" tabindex="-1" aria-hidden="true" style="display:none;">
            <div class="modal-dialog modal-lg">
                <form id="formAddOrder" method="POST" action="{{ route('penjualan.pesanan.store') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Pesanan</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label>Nama Sales</label>
                                    <select name="id_sales" id="add_id_sales" class="form-control">
                                        <option value="">-- Pilih Sales --</option>
                                        @foreach ($sales as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-4">
                                    <label>Nama Toko</label>
                                    <select name="id_toko" id="add_id_toko" class="form-control" disabled>
                                        <option value="">-- Pilih Sales terlebih dahulu --</option>
                                    </select>
                                </div>
                            </div>

                            <hr>
                            <div>
                                <h6>Item Pesanan</h6>
                                <table class="table table-sm table-fixed" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th class="col-produk">Produk</th>
                                            <th class="col-qty" width="120">Qty</th>
                                            <th class="col-harga" width="140">Harga (Rp)</th>
                                            <th class="col-satuan" width="100">Satuan</th>
                                            <th class="col-subtot text-end" width="140">Subtotal (Rp)</th>
                                            <th class="col-action" width="60"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div><button type="button" class="btn btn-sm btn-outline-primary"
                                            id="addItemBtn">Tambah Item</button></div>
                                    <div><strong>Total: </strong> <span id="orderTotalDisplay">Rp 0</span></div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" id="btnCloseAdd">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL Edit --}}
        <div id="modalEditOrder" class="modal custom-modal" tabindex="-1" aria-hidden="true" style="display:none;">
            <div class="modal-dialog modal-lg">
                <form id="formEditOrder" method="POST" action="">
                    @csrf @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Pesanan</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_order_id" name="id_order" value="">
                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label>Nama Sales</label>
                                    <select name="id_sales" id="edit_id_sales" class="form-control">
                                        <option value="">-- Pilih Sales --</option>
                                        @foreach ($sales as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-4">
                                    <label>Nama Toko</label>
                                    <select name="id_toko" id="edit_id_toko" class="form-control" disabled>
                                        <option value="">-- Pilih Sales terlebih dahulu --</option>
                                    </select>
                                </div>

                            </div>

                            <hr>
                            <div>
                                <h6>Item Pesanan</h6>
                                <table class="table table-sm table-fixed" id="itemsTableEdit">
                                    <thead>
                                        <tr>
                                            <th class="col-produk">Produk</th>
                                            <th class="col-qty" width="120">Qty</th>
                                            <th class="col-harga" width="140">Harga (Rp)</th>
                                            <th class="col-satuan" width="100">Satuan</th>
                                            <th class="col-subtot text-end" width="140">Subtotal (Rp)</th>
                                            <th class="col-action" width="60"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div><button type="button" class="btn btn-sm btn-outline-primary"
                                            id="addItemBtnEdit">Tambah Item</button></div>
                                    <div><strong>Total: </strong> <span id="orderEditTotalDisplay">Rp 0</span></div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" id="btnCloseEdit">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL Detail (ubah ke custom-modal supaya compatible dengan showModal/hideModal) -->
        <div id="modalDetail" class="modal custom-modal" tabindex="-1" role="dialog"
            aria-labelledby="modalDetailLabel" aria-hidden="true" style="display:none;">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetailLabel">Detail Pesanan</h5>
                    </div>
                    <div class="modal-body">
                        <div id="detailContent">
                            <p><strong>Sales:</strong> <span id="detailSales">-</span></p>
                            <p><strong>Toko:</strong> <span id="detailToko">-</span></p>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="detailItemsTable">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-right">Harga Satuan (Rp)</th>
                                            <th class="text-right">Subtotal (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right">Total</th>
                                            <th id="detailTotal" class="text-right">0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btnCloseDetail">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection


    <style>
        /* atur lebar kolom spesifik untuk table-fixed */
        table.table-fixed th,
        table.table-fixed td {
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Sales: lebih sempit, pakai ellipsis jika teks panjang */
        .col-sales {
            width: 100px;
            /* sesuaikan angka bila mau lebih kecil/besar */
            max-width: 100px;
            white-space: nowrap;
        }

        /* Toko tetap bisa meluas tapi batasi sedikit */
        .col-toko {
            min-width: 180px;
            max-width: 320px;
        }

        /* Total: biar rapi, pertahankan text-end */
        .col-total {
            width: 100px;
            max-width: 100px;
        }

        .col-tanggal {
            width: 160px;
            max-width: 160px;
        }

        /* Aksi: jangan biarkan tombol wrap, siapkan ruang tetap */
        .col-aksi {
            width: 180px;
            /* sesuaikan bila perlu */
            max-width: 180px;
            white-space: nowrap;
        }

        /* Pastikan tombol di dalam sel aksi inline dan tidak berpindah baris */
        .col-aksi .btn {
            display: inline-block;
            margin-right: 6px;
        }

        /* Jika butuh responsif: pada layar kecil, biarkan kolom aksi jadi lebih kecil */
        @media (max-width: 768px) {
            .col-sales {
                width: 120px;
                max-width: 120px;
            }

            .col-aksi {
                width: 140px;
                max-width: 140px;
            }
        }

        .modal.custom-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200000 !important;
            background: rgba(0, 0, 0, 0.45);
        }

        .modal.custom-modal .modal-dialog {
            max-width: 900px;
            width: 95%;
            margin: 0;
        }

        .modal .modal-content {
            max-height: 85vh;
            overflow: auto;
        }

        .table-fixed {
            table-layout: fixed;
            width: 100%;
            word-wrap: break-word;
        }

        .table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        .table-body-scroll {
            max-height: 380px;
            overflow-y: auto;
            display: block;
        }

        .text-currency {
            white-space: nowrap;
        }
    </style>
    @push('scripts')
        <script>
            $(function() {
                // --- config / data dari blade ---
                const products = @json($productsMapped ?? ($products ?? []));
                const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

                // --- util ---
                function formatCurrencyID(n) {
                    return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID');
                }

                function safeInt(v) {
                    return parseInt(v || 0) || 0;
                }

                // <-- TAMBAHKAN escapeHtml helper (penting) -->
                function escapeHtml(s) {
                    return String(s || '').replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                }

                const table = $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('penjualan.pesanan.data') }}",
                        type: 'GET'
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },

                        {
                            data: 'sales_name',
                            name: 'sales_name'
                        },

                        {
                            data: 'sales_phone',
                            name: 'sales_phone',
                            render: function(val) {
                                return val ? '<span>' + escapeHtml(val) + '</span>' : '-';
                            }
                        },

                        {
                            data: 'toko_name',
                            name: 'toko_name'
                        },

                        {
                            data: 'calculated_total',
                            name: 'calculated_total',
                            className: 'text-end',
                            render: d => formatCurrencyID(d || 0)
                        },

                        {
                            data: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: v => v
                        }
                    ], // <-- penting: koma di sini
                    order: [
                        [1, 'desc']
                    ],
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 10,

                    rowCallback: function(row, data) {
                        try {
                            var isLunas = Number(data.is_lunas || 0);
                            var total = Number(data.calculated_total || 0);
                            var paid = Number(data.paid || 0);
                            var remaining = Number(data.remaining !== undefined ? data.remaining : (total -
                                paid));

                            if (isLunas === 1 || remaining <= 0) {
                                $(row).addClass('order-lunas d-none');
                                return;
                            }

                            if (paid > 0 && remaining > 0) {
                                $(row).find('.btn-edit, .btn-delete').prop('disabled', true).addClass(
                                    'disabled').attr('aria-disabled', 'true');
                            } else {
                                $(row).find('.btn-edit, .btn-delete').prop('disabled', false).removeClass(
                                    'disabled').attr('aria-disabled', 'false');
                            }

                            $(row).find('.btn-detail').prop('disabled', false).removeClass('disabled').attr(
                                'aria-disabled', 'false');
                        } catch (e) {
                            console.warn('rowCallback error', e);
                        }
                    },


                    drawCallback: function() {
                        $('#ordersTable tbody tr').each(function() {
                            var $tr = $(this);
                            if ($tr.hasClass('order-lunas')) $tr.addClass('d-none');
                            else $tr.removeClass('d-none');
                        });
                    }
                });

                function loadTokoBySales(salesId, $select, selected = null) {
                    $select.prop('disabled', true)
                        .html('<option value="">Loading...</option>');

                    if (!salesId) {
                        $select
                            .html('<option value="">-- Pilih Sales terlebih dahulu --</option>')
                            .prop('disabled', true);
                        return;
                    }

                    fetch(`/penjualan/pesanan/toko-by-sales/${encodeURIComponent(salesId)}`)
                        .then(res => res.json())
                        .then(data => {
                            $select.empty();

                            if (!data || data.length === 0) {
                                $select.append('<option value="">Tidak ada toko</option>');
                            } else {
                                data.forEach(t => {
                                    const opt = $('<option>')
                                        .val(t.id)
                                        .text(t.nama_toko || t.id);

                                    if (selected && String(selected) === String(t.id)) {
                                        opt.prop('selected', true);
                                    }

                                    $select.append(opt);
                                });
                            }

                            $select.prop('disabled', false);
                        })
                        .catch(() => {
                            $select
                                .html('<option value="">Gagal memuat toko</option>')
                                .prop('disabled', true);
                        });
                }

                // === ketika SALES dipilih (ADD & EDIT) ===
                $('#add_id_sales').on('change', function() {
                    loadTokoBySales($(this).val(), $('#add_id_toko'));
                });

                $('#edit_id_sales').on('change', function() {
                    loadTokoBySales($(this).val(), $('#edit_id_toko'));
                });



                function isDisabledBtn($el) {
                    return $el.is('[disabled]') || $el.attr('aria-disabled') === 'true' || $el.hasClass('disabled');
                }
                $(document).on('click', '.btn-edit, .btn-delete, .btn-bayar, .btn-detail', function(e) {
                    var $b = $(this);
                    if (isDisabledBtn($b)) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return;
                    }
                    // handler...
                });


                // --- modal helpers (works for custom-modal and fallback) ---
                function showModal($m) {
                    $m.attr('aria-hidden', 'false').css('display', 'flex').addClass('show');
                    // ensure z-index if custom-modal
                    if ($m.hasClass('custom-modal')) {
                        $m.css({
                            'align-items': 'center',
                            'justify-content': 'center'
                        });
                        $m.css('z-index', 200000);
                    }
                    $('body').addClass('modal-open');
                }

                function hideModal($m) {
                    $m.attr('aria-hidden', 'true').css('display', 'none').removeClass('show');
                    $('body').removeClass('modal-open');
                }

                // --- builders, compute helpers (unchanged logic) ---
                function buildProductSelect(selected = null, classes = 'product-select form-select form-select-sm') {
                    const $sel = $('<select>').addClass(classes);
                    $sel.append($('<option>').attr('value', '').text('-- Pilih Produk --'));
                    (products || []).forEach(p => {
                        const $o = $('<option>').attr('value', p.id)
                            .data('harga', p.harga || 0)
                            .data('stok', p.stok || 0)
                            .data('satuan', p.satuan || '')
                            .text((p.name || '') + ' (stok: ' + (p.stok || 0) + ')');
                        if (selected !== null && String(selected) === String(p.id)) $o.prop('selected', true);
                        $sel.append($o);
                    });
                    return $sel;
                }

                function sumQtyInOtherRows(prodId, $tbody, $excludeRow) {
                    if (!$tbody) return 0;
                    let s = 0;
                    $tbody.children('tr').each(function() {
                        const $r = $(this);
                        if ($r.is($excludeRow)) return;
                        const selVal = $r.find('select.product-select').val();
                        if (String(selVal) === String(prodId)) {
                            s += safeInt($r.find('.item-jumlah').val());
                        }
                    });
                    return s;
                }

                function computeAllowedMaxForRow(prodObj, $row) {
                    if (!prodObj) return 0;
                    const prodStok = safeInt(prodObj.stok);
                    const original = safeInt($row.data('originalQty'));
                    const $tbody = $row.parent();
                    const others = sumQtyInOtherRows(prodObj.id, $tbody, $row);
                    const allowed = Math.max(0, prodStok + original - others);
                    return allowed;
                }

                function updateRowSubtotal($tr) {
                    const qty = safeInt($tr.find('.item-jumlah').val());
                    const harga = safeInt($tr.find('.item-harga').val());
                    const subtotal = qty * harga;
                    $tr.find('.item-subtotal-display').text(formatCurrencyID(subtotal));
                }

                function buildItemRow(data = null, isEdit = false) {
                    const $tr = $('<tr>');
                    const origQty = data && data.jumlah ? safeInt(data.jumlah) : 0;
                    $tr.data('originalQty', origQty);

                    const $tdProd = $('<td>').append(buildProductSelect(data ? data.id_produk : null));
                    const $tdQty = $('<td>').append($('<input>').attr({
                            type: 'number',
                            min: 0,
                            step: 1
                        })
                        .addClass('form-control form-control-sm item-jumlah').val(data ? safeInt(data.jumlah) : 0));
                    const $tdHarga = $('<td>').append($('<input>').attr({
                            type: 'number',
                            readonly: true
                        })
                        .addClass('form-control form-control-sm item-harga').val(data ? safeInt(data.harga) : 0));
                    const $tdSatuan = $('<td>').append($('<div>').addClass('item-satuan small').text(data ? (data
                        .satuan || '') : ''));
                    const initSubtotal = data ? (typeof data.subtotal !== 'undefined' ? safeInt(data.subtotal) : (
                        safeInt(data.jumlah) * safeInt(data.harga))) : 0;
                    const $tdSub = $('<td>').addClass('text-end item-subtotal-display').text(formatCurrencyID(
                        initSubtotal));
                    const $tdAct = $('<td>').append($('<button type="button">').addClass(
                        'btn btn-sm btn-danger btn-remove-item').text('x'));

                    $tr.append($tdProd, $tdQty, $tdHarga, $tdSatuan, $tdSub, $tdAct);

                    const $sel = $tdProd.find('select.product-select');
                    const $inpQty = $tdQty.find('input.item-jumlah');
                    const $inpHarga = $tdHarga.find('input.item-harga');

                    // helper to get product object by a select element (DOM or jQuery)
                    function getProductObjBySel(selEl) {
                        const val = $(selEl).val();
                        if (!val) return null;
                        return products.find(p => String(p.id) === String(val)) || null;
                    }

                    // ---------- change handler (when user picks product) ----------
                    $sel.on('change', function() {
                        try {
                            const opt = $sel.find('option:selected');
                            const price = safeInt(opt.data('harga'));
                            const satuanVal = opt.data('satuan') || '';
                            const stok = safeInt(opt.data('stok'));

                            // merging logic like your snippet:
                            const tbody = $tr.parent();
                            if (tbody && $sel.val()) {
                                // check duplicate rows
                                let merged = false;
                                tbody.children('tr').each(function() {
                                    const $r = $(this);
                                    if ($r.is($tr)) return; // skip self
                                    const otherSel = $r.find('select.product-select');
                                    if (!otherSel.length) return;
                                    if (String(otherSel.val()) === String($sel.val())) {
                                        // merge qty into other row
                                        const otherQtyEl = $r.find('.item-jumlah')[0];
                                        const currentQty = parseInt($inpQty.val() || 0) || 0;

                                        // compute allowed max for target row (use computeAllowedMaxForRow with jQuery row)
                                        const prodForCalc = getProductObjBySel(otherSel);
                                        const allowedMaxTarget = computeAllowedMaxForRow(prodForCalc,
                                            $r);
                                        const existingQty = parseInt($(otherQtyEl).val() || 0) || 0;
                                        const desired = existingQty + currentQty;

                                        if (desired > allowedMaxTarget) {
                                            alert('Stok hanya tersisa ' + allowedMaxTarget +
                                                ' untuk produk ini.');
                                            // set target to allowedMaxTarget
                                            $(otherQtyEl).val(allowedMaxTarget);
                                        } else {
                                            $(otherQtyEl).val(desired);
                                        }

                                        // update subtotal target row
                                        const hargaExisting = parseInt($r.find('.item-harga').val() ||
                                            price) || price;
                                        $r.find('.item-subtotal-display').text(formatCurrencyID((
                                                parseInt($(otherQtyEl).val() || 0) || 0) *
                                            hargaExisting));

                                        // update satuan target row
                                        const satuanElTarget = $r.find('.item-satuan');
                                        if (satuanElTarget && satuanElTarget.length) satuanElTarget
                                            .text(satuanVal || '');

                                        // remove current row
                                        $tr.remove();
                                        updateOrderTotal(isEdit);
                                        merged = true;
                                        return false; // break loop
                                    }
                                });

                                if (merged) {
                                    // already handled above
                                    return;
                                }
                            }
                        } catch (e) {
                            console.warn(e);
                        }

                        // no duplicate -> set harga/satuan and validate qty <= stok
                        const optNow = $sel.find('option:selected');
                        const priceNow = safeInt(optNow.data('harga'));
                        const satuanNow = optNow.data('satuan') || '';
                        const stokNow = safeInt(optNow.data('stok'));

                        $inpHarga.val(priceNow);
                        const satuanEl = $tr.find('.item-satuan');
                        if (satuanEl && satuanEl.length) satuanEl.text(satuanNow || '');

                        // compute allowed using existing helper (expects product object and jQuery $tr)
                        const prodObj = getProductObjBySel($sel);
                        const allowedMax = computeAllowedMaxForRow(prodObj, $tr);
                        let curQty = parseInt($inpQty.val() || 0) || 0;

                        if (curQty > allowedMax) {
                            alert('Stok hanya tersisa ' + allowedMax + ' untuk produk ini.');
                            $inpQty.val(allowedMax);
                            curQty = allowedMax;
                        }

                        // if stok is 0, allow qty 0 but warn
                        if (stokNow <= 0) {
                            if (curQty !== 0) {
                                alert('Stok habis untuk produk ini.');
                                $inpQty.val(0);
                                curQty = 0;
                            }
                        }

                        updateRowSubtotal($tr);
                        updateOrderTotal(isEdit);
                    });

                    // ---------- qty change handler (input) ----------
                    $inpQty.on('input', function() {
                        try {
                            const prodObj = getProductObjBySel($sel);
                            const allowedMax = computeAllowedMaxForRow(prodObj, $tr);
                            let val = parseInt($(this).val() || 0) || 0;

                            if (val > allowedMax) {
                                alert('Stok hanya tersisa ' + allowedMax + ' untuk produk ini.');
                                val = allowedMax;
                                $(this).val(allowedMax);
                            } else if (val < 0) {
                                val = 0;
                                $(this).val(0);
                            }
                        } catch (e) {
                            console.warn(e);
                        }
                        updateRowSubtotal($tr);
                        updateOrderTotal(isEdit);
                    });

                    // remove btn
                    $tdAct.on('click', '.btn-remove-item', function() {
                        $tr.remove();
                        updateOrderTotal(isEdit);
                    });

                    // init: if passed data, set select/harga/qty properly, otherwise qty=0
                    if (data && data.id_produk) {
                        const prodObjInit = products.find(p => String(p.id) === String(data.id_produk));
                        if (prodObjInit) {
                            $sel.val(prodObjInit.id);
                            $sel.trigger('change');
                            $inpHarga.val(prodObjInit.harga || safeInt(data.harga) || 0);
                            $tr.find('.item-satuan').text(prodObjInit.satuan || (data.satuan || ''));
                            // set qty respecting stok
                            const initialQty = safeInt(data.jumlah || 0);
                            const allowed = computeAllowedMaxForRow(prodObjInit, $tr);
                            if (initialQty > allowed) {
                                alert('Jumlah pada data melebihi stok. Dibatasi ke ' + allowed + '.');
                                $inpQty.val(allowed);
                            } else {
                                $inpQty.val(initialQty);
                            }
                            updateRowSubtotal($tr);
                        } else {
                            $inpHarga.val(safeInt(data.harga) || 0);
                            $tr.find('.item-satuan').text(data.satuan || '');
                            $inpQty.val(safeInt(data.jumlah) || 0);
                            updateRowSubtotal($tr);
                        }
                    } else {
                        // default new row: qty zero (user must input)
                        $inpQty.val(0);
                    }

                    return $tr;
                }


                function readItemsFromTable(isEdit = false) {
                    const $tbody = isEdit ? $('#itemsTableEdit tbody') : $('#itemsTable tbody');
                    const items = [];
                    $tbody.children('tr').each(function() {
                        const $r = $(this);
                        const prod = $r.find('select.product-select').val();
                        const jumlah = safeInt($r.find('.item-jumlah').val());
                        const harga = safeInt($r.find('.item-harga').val());
                        const satuan = $r.find('.item-satuan').text() || '';
                        if (prod && jumlah > 0) {
                            items.push({
                                id_produk: prod,
                                jumlah: jumlah,
                                harga: harga,
                                satuan: satuan,
                                subtotal: jumlah * harga
                            });
                        }
                    });
                    return items;
                }

                function updateOrderTotal(isEdit = false) {
                    const items = readItemsFromTable(isEdit);
                    const total = items.reduce((s, it) => s + ((safeInt(it.jumlah) || 0) * (safeInt(it.harga) || 0)),
                        0);
                    if (isEdit) $('#orderEditTotalDisplay').text(formatCurrencyID(total));
                    else $('#orderTotalDisplay').text(formatCurrencyID(total));
                }

                // --- Add item buttons ---
                $('#addItemBtn').on('click', function() {
                    $('#itemsTable tbody').append(buildItemRow(null, false));
                    updateOrderTotal(false);
                });
                $('#addItemBtnEdit').on('click', function() {
                    $('#itemsTableEdit tbody').append(buildItemRow(null, true));
                    updateOrderTotal(true);
                });

                // --- Open Add modal ---
                $('#btnAddOrder').on('click', function(e) {
                    e.preventDefault();
                    const $m = $('#modalAddOrder');
                    const $f = $('#formAddOrder');
                    $f[0].reset();
                    $f.find('input[type="hidden"][name^="items["]').remove();
                    $('#itemsTable tbody').empty().append(buildItemRow(null, false));
                    updateOrderTotal(false);
                    showModal($m);
                });
                $('#btnCloseAdd').on('click', function() {
                    hideModal($('#modalAddOrder'));
                });

                // --- Open Edit modal (AJAX load) ---
                window.openEditOrderModal = function(orderId) {
                    $.ajax({
                            url: '/penjualan/pesanan/' + encodeURIComponent(orderId) + '/edit',
                            method: 'GET',
                            dataType: 'json'
                        })
                        .done(function(data) {
                            const $m = $('#modalEditOrder');

                            // set order id
                            $('#edit_order_id').val(data.id || '');

                            // reset items
                            const $tbody = $('#itemsTableEdit tbody').empty();
                            (data.items || []).forEach(function(it) {
                                $tbody.append(buildItemRow({
                                    id_produk: it.id_produk,
                                    jumlah: it.jumlah,
                                    harga: it.harga,
                                    satuan: it.satuan || ''
                                }, true));
                            });
                            updateOrderTotal(true);

                            // set sales
                            $('#edit_id_sales').val(data.id_sales || '');

                            // 🔥 PENTING: load toko BERDASARKAN sales
                            loadTokoBySales(
                                data.id_sales,
                                $('#edit_id_toko'),
                                data.id_toko // <- auto selected
                            );

                            // set form action
                            $('#formEditOrder').attr(
                                'action',
                                '/penjualan/pesanan/' + encodeURIComponent(orderId)
                            );

                            showModal($m);
                        })
                        .fail(function() {
                            Swal.fire('Gagal', 'Gagal memuat data order', 'error');
                        });
                };

                // close edit modal
                $('#btnCloseEdit').on('click', function() {
                    hideModal($('#modalEditOrder'));
                });


                // --- Detail modal ---
                window.openDetailModal = function(orderId) {
                    $.ajax({
                        url: '/penjualan/pesanan/' + encodeURIComponent(orderId) + '/detail',
                        method: 'GET',
                        dataType: 'json'
                    }).done(function(data) {
                        // gunakan data.toko_name yang dikirim controller
                        $('#detailSales').text(data.sales_name || '-');
                        $('#detailToko').text(data.toko_name || '-');

                        const $tbody = $('#detailItemsTable tbody').empty();
                        let total = 0;
                        (data.items || []).forEach(function(it) {
                            const nama = it.nama || '-';
                            const jumlah = Number(it.jumlah || 0);
                            const satuan = it.satuan || '-';
                            const harga = Number(it.harga_satuan || 0);
                            const subtotal = jumlah * harga;
                            const $tr = $('<tr>').html(
                                `<td>${nama}</td><td class="text-center">${jumlah}</td><td class="text-center">${satuan}</td><td class="text-right">${harga.toLocaleString('id-ID')}</td><td class="text-right">${subtotal.toLocaleString('id-ID')}</td>`
                            );
                            $tbody.append($tr);
                            total += subtotal;
                        });
                        $('#detailTotal').text(total.toLocaleString('id-ID'));
                        showModal($('#modalDetail'));
                    }).fail(function() {
                        Swal.fire('Gagal', 'Gagal mengambil detail pesanan', 'error');
                    });
                };

                $('#btnCloseDetail').on('click', function() {
                    hideModal($('#modalDetail'));
                });

                // --- Form submit Add (AJAX) ---
                $('#formAddOrder').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const items = readItemsFromTable(false);
                    if (items.length === 0) {
                        Swal.fire('Perhatian', 'Tambahkan minimal 1 item pesanan yang lengkap.', 'warning');
                        return;
                    }

                    // validate totals vs stok (client-side)
                    const totals = {};
                    items.forEach(it => {
                        totals[it.id_produk] = (totals[it.id_produk] || 0) + safeInt(it.jumlah);
                    });
                    for (const pid in totals) {
                        const prod = products.find(p => String(p.id) === String(pid));
                        const allowed = prod ? safeInt(prod.stok) : 0;
                        if (totals[pid] > allowed) {
                            Swal.fire('Stok tidak cukup', 'Jumlah untuk produk ' + (prod ? prod.name : pid) +
                                ' melebihi stok (' + allowed + ').', 'warning');
                            return;
                        }
                    }

                    const payload = {
                        id_toko: $form.find('[name="id_toko"]').val() || null,
                        id_sales: $form.find('[name="id_sales"]').val() || null,
                        items: items,
                        total: items.reduce((s, it) => s + ((safeInt(it.jumlah) || 0) * (safeInt(it
                            .harga) || 0)), 0)
                    };

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(payload),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                    }).done(function(res) {
                        if (res && res.success) {
                            Swal.fire('Sukses', res.message || 'Pesanan dibuat', 'success');
                            hideModal($('#modalAddOrder'));
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', (res && res.message) || 'Gagal membuat pesanan',
                                'error');
                        }
                    }).fail(function(xhr) {
                        let msg = 'Gagal membuat pesanan.';
                        try {
                            msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON
                                .errors && xhr.responseJSON.errors.join(', '))) || msg;
                        } catch (e) {}
                        Swal.fire('Gagal', msg, 'error');
                    });
                });

                // --- Form submit Edit (AJAX PUT) ---
                $('#formEditOrder').on('submit', function(e) {
                    e.preventDefault();
                    const $form = $(this);
                    const actionUrl = $form.attr('action') || '';
                    const items = readItemsFromTable(true);
                    if (!items.length) {
                        Swal.fire('Perhatian',
                            'Tambahkan minimal 1 item pesanan yang lengkap sebelum menyimpan.', 'warning');
                        return;
                    }

                    // validate requested vs allowed (prod.stok + original)
                    const requested = {};
                    $('#itemsTableEdit tbody tr').each(function() {
                        const $r = $(this);
                        const pid = $r.find('select.product-select').val();
                        const q = safeInt($r.find('.item-jumlah').val());
                        if (!pid) return;
                        requested[pid] = (requested[pid] || 0) + q;
                    });
                    const original = {};
                    $('#itemsTableEdit tbody tr').each(function() {
                        const $r = $(this);
                        const pid = $r.find('select.product-select').val();
                        const orig = safeInt($r.data('originalQty'));
                        if (!pid) return;
                        original[pid] = (original[pid] || 0) + orig;
                    });

                    for (const pid in requested) {
                        const prod = products.find(p => String(p.id) === String(pid));
                        const allowed = (prod ? safeInt(prod.stok) : 0) + (original[pid] || 0);
                        if (requested[pid] > allowed) {
                            Swal.fire('Stok tidak cukup', 'Jumlah untuk produk ' + (prod ? prod.name : pid) +
                                ' melebihi stok (' + allowed + ').', 'warning');
                            return;
                        }
                    }

                    const payload = {
                        id_toko: $form.find('[name="id_toko"]').val() || null,
                        id_sales: $form.find('[name="id_sales"]').val() || null,
                        items: items,
                        total: items.reduce((s, it) => s + ((safeInt(it.jumlah) || 0) * (safeInt(it
                            .harga) || 0)), 0)
                    };

                    $.ajax({
                        url: actionUrl,
                        method: 'PUT',
                        contentType: 'application/json',
                        data: JSON.stringify(payload),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                    }).done(function(res) {
                        if (res && res.success) {
                            Swal.fire('Sukses', res.message || 'Pesanan diperbarui', 'success');
                            hideModal($('#modalEditOrder'));
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', (res && res.message) || 'Gagal memperbarui pesanan',
                                'error');
                        }
                    }).fail(function(xhr) {
                        let msg = 'Gagal memperbarui pesanan.';
                        try {
                            msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON
                                .errors && xhr.responseJSON.errors.join(', '))) || msg;
                        } catch (e) {}
                        Swal.fire('Gagal', msg, 'error');
                    });
                });

                // --- Delete handler (AJAX) ---
                $(document).on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    if (!id) {
                        Swal.fire('Error', 'Order ID tidak ditemukan', 'error');
                        return;
                    }

                    Swal.fire({
                        title: 'Yakin hapus pesanan?',
                        text: 'Tindakan ini akan menghapus pesanan dan mengembalikan stok.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/penjualan/pesanan/' + encodeURIComponent(id),
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                            }).done(function(res) {
                                if (res && res.success) {
                                    Swal.fire('Sukses', res.message || 'Pesanan dihapus',
                                        'success');
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal', (res && res.message) ||
                                        'Gagal menghapus', 'error');
                                }
                            }).fail(function(xhr) {
                                let msg = 'Gagal menghapus pesanan.';
                                try {
                                    msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr
                                        .responseJSON.error)) || msg;
                                } catch (e) {}
                                Swal.fire('Gagal', msg, 'error');
                            });
                        }
                    });
                });

                // --- delegated edit/detail click (in case actions use classes) ---
                $(document).on('click', '.btn-edit', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    if (!id) {
                        Swal.fire('Error', 'Order ID tidak ditemukan', 'error');
                        return;
                    }
                    openEditOrderModal(id);
                });
                $(document).on('click', '.btn-detail', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    if (!id) {
                        Swal.fire('Error', 'Order ID tidak ditemukan', 'error');
                        return;
                    }
                    openDetailModal(id);
                });

            });
        </script>
    @endpush
