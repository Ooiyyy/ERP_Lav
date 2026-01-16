{{-- resources/views/penjualan/pembayaran.blade.php --}}
@extends('layouts.app')

@push('title')
    Pembayaran
@endpush

@section('content')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Midtrans SNAP (sandbox) -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-CFvfJ5GdPBWmUden"></script>
    <!-- Note: ganti ke https://app.midtrans.com/snap/snap.js untuk production -->

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Menu Pembayaran</h1>
            <a href="{{ route('penjualan.pesanan.index') }}" class="btn btn-secondary">Kembali ke Pesanan</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="flash-placeholder">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="table-pesanan" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Tanggal Buat</th>
                                <th>Nama Sales</th>
                                <th>Nomor HP</th>
                                <th>Nama Toko</th>
                                <th class="text-end">Sisa (Rp)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pesanan</h5>
                </div>
                <div class="modal-body">
                    <div><strong>ID:</strong> <span id="d_id">-</span></div>
                    <div><strong>Tanggal:</strong> <span id="d_tanggal">-</span></div>
                    <div><strong>Sales:</strong> <span id="d_sales">-</span></div>
                    <div><strong>Nomor HP:</strong> <span id="d_phone">-</span></div>
                    <div><strong>Toko:</strong> <span id="d_toko">-</span></div>

                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm" id="detail_items">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th id="d_total" class="text-end">Rp 0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <hr>
                    <h6>Riwayat Pembayaran</h6>
                    <div class="table-responsive">
                        <table class="table table-sm" id="history_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>Payment Link</th>
                                    <th class="text-end">Nominal</th>
                                    <th>Status</th>
                                    <th>Petugas</th>
                                </tr>
                            </thead>
                                
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Sudah Bayar</th>
                                    <th id="history_total" class="text-end">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                                
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCloseDetail" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Bayar --}}
    <div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formBayar">
                @csrf
                <input type="hidden" name="order_id" id="pay_order_id">
                <input type="hidden" name="items" x-model="JSON.stringify($store.cart.items)" id="hidden_items">
                <input type="hidden" name="cart_total" x-model="$store.cart.total" id="hidden_cart_total">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pembayaran</h5>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2"><label>Kode / ID</label><input type="text" id="pay_kode" class="form-control" readonly></div>
                        <div class="row">
                            <div class="col-md-4 mb-2"><label>Total</label><input type="text" id="pay_total" class="form-control" readonly></div>
                            <div class="col-md-4 mb-2"><label>Sudah Bayar</label><input type="text" id="pay_sudah" class="form-control" readonly></div>
                            <div class="col-md-4 mb-2"><label>Nomor HP</label><input type="text" id="pay_phone" name="no_hp" class="form-control" placeholder="Nomor HP sales (otomatis)" readonly></div>
                        </div>

                        <div class="mb-2">
                            <label>Metode</label>
                            <select id="pay_method" name="jenis_bayar" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <option value="cash">Cash</option>
                                <option value="va">VA</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Jumlah Bayar (Rp)</label>
                            <input type="number" min="1" id="pay_amount" name="jumlah_bayar" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Sisa setelah bayar</label>
                            <input type="text" id="pay_kurang" class="form-control" readonly>
                        </div>

                        <div id="pay_msg" class="small text-muted"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btnCloseBayar" data-dismiss="modal">Batal</button>
                        <button type="submit" id="btnPaySubmit" class="btn btn-primary disabled">Bayar</button>
                    </div>
                </div>

                {{-- snap container berada di luar modal-body awal karena nanti kita akan replace modal-body saat Snap-mode --}}
                <div id="snap-container" style="display:none;"></div>
            </form>
        </div>
    </div>
@endsection

<style>
    /* gaya visual untuk tombol disabled */
    .btn.disabled, .btn[disabled], #btnPaySubmit.disabled {
        background-color: #999 !important;
        border-color: #999 !important;
        cursor: not-allowed !important;
        opacity: 0.9;
        pointer-events: none;
    }
    
</style>

@push('scripts')
<script>
$(function() {
    // --------------------
    // Helpers
    // --------------------
    function fmt(n) { return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID'); }
    function escapeHtml(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
    function escapeAttr(s) { return String(s||'').replace(/'/g, "&#39;").replace(/"/g, "&quot;"); }

    // --------------------
    // Elements / state
    // --------------------
    var $modalBayar = $('#modalBayar');
    var $form = $('#formBayar');
    var $btnPay = $('#btnPaySubmit');

    var table = $('#table-pesanan').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: "{{ route('penjualan.pembayaran.data') }}", type: 'GET' },
        columns: [
            { data: 'id' },
            { data: 'created_at' },
            { data: 'sales_name' },
            {
                data: 'sales_phone',
                render: function(v) { return v ? escapeHtml(v) : '-'; }
            },
            { data: 'toko_name' },
            {
                data: 'remaining',
                render: function(d) { return fmt(d); },
                className: 'text-end'
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(rowData, type, row, meta) {
                    var remaining = Number(row.remaining || 0);
                    var id = row.id;
                    var safeJson = escapeAttr(JSON.stringify(row));
                    var btnDetail = '<button class="btn btn-sm btn-success btn-detail" data-id="' + escapeHtml(id) + '" data-order=\'' + safeJson + '\' title="Detail">Detail</button> ';
                    var btnBayar = remaining > 0 ? '<button class="btn btn-sm btn-info btn-bayar" data-id="' + escapeHtml(id) + '" data-order=\'' + safeJson + '\' title="Bayar">Bayar</button>' : '';
                    return btnDetail + btnBayar;
                }
            }
        ],
        order: [[1, 'desc']],
        createdRow: function(row, data, dataIndex){
            if (!data.sales_phone) $(row).addClass('no-sales-phone'); else $(row).removeClass('no-sales-phone');
        },
        rowCallback: function(row, data) {
            try {
                var rem = Number(data.remaining || 0);
                if (rem <= 0) $(row).addClass('order-lunas d-none'); else $(row).removeClass('order-lunas d-none');
            } catch (e) {}
        },
        drawCallback: function() {
            $('#table-pesanan tbody tr').each(function() {
                var $tr = $(this);
                if ($tr.hasClass('order-lunas')) { $tr.addClass('d-none'); return; }
                var remCell = $tr.find('td').eq(5).text() || '';
                var num = Number(String(remCell).replace(/[^\d\-]/g, '')) || 0;
                if (num <= 0) $tr.addClass('order-lunas d-none'); else $tr.removeClass('order-lunas d-none');
            });
        }
    });

    // --------------------
    // Hidden items helpers
    // --------------------
    function readHiddenItemsRaw() {
        var el = document.querySelector('input[name="items"], #hidden_items');
        if (!el) return '[]';
        var v = '';
        try { v = el.value; } catch (e) { v = null; }
        if (!v && el.getAttribute) v = el.getAttribute('value') || '';
        return v || '[]';
    }
    function parseHiddenItems() {
        var raw = readHiddenItemsRaw();
        if (!raw) return [];
        try {
            var parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            try { return JSON.parse(raw.replace(/'/g, '"')); } catch (err) { return []; }
        }
    }

    // --------------------
    // Render payment history row
    // --------------------
    function renderPaymentRow(idx, p) {
        // TANGGAL
        var tanggal = p.tanggal_transaksi || p.tanggal || p.created_at || '-';
        try { tanggal = new Date(tanggal).toLocaleString(); } catch(e){}

        // =====================
        // METODE PEMBAYARAN
        // =====================
        var method = '-';

        // CASH
        if ((p.jenis_bayar || p.method || '').toLowerCase() === 'cash') {
            method = 'CASH';
        }
        // VA / ONLINE ? ambil dari channel
        else {
            if (p.channel) {
                method = String(p.channel).toUpperCase(); // DANA / BRI / GOPAY / QRIS
            } else {
                method = 'ONLINE';
            }
        }


        // PETUGAS
        // var petugas = p.petugas || p.id_karyawan || '-';
        var petugas = '-';

        if ((method || '').toUpperCase() === 'CASH') {
            petugas = p.petugas || '-';
        } else {
            petugas = 'ONLINE';
        }


        // PAYMENT LINK (KHUSUS VA)
        var paymentLink = '-';
        if (p.payment_link) {
            var short = p.payment_link.length > 40
                ? p.payment_link.substr(0, 40) + '...'
                : p.payment_link;

            paymentLink = `
                <a href="${escapeHtml(p.payment_link)}" target="_blank">
                    ${escapeHtml(short)}
                </a>
            `;
        }

        // NOMINAL
        var nominal = Number(p.nominal || p.amount || 0);

        // ? STATUS (LOGIKA UTAMA)
        var status = 'PAID'; // default untuk CASH

        if (p.payment_link) {
            // VA ? ambil dari DB
            status = (p.status || 'PENDING').toUpperCase();
        }

        // badge warna
        var badgeClass =
            status === 'PAID' ? 'bg-success text-white' :
            status === 'PENDING' ? 'bg-warning text-white' :
            status === 'EXPIRED' ? 'bg-primary text-white' :
            'bg-danger';

        return `
            <tr>
                <td>${idx}</td>
                <td>${escapeHtml(tanggal)}</td>
                <td>${escapeHtml(method)}</td>
                <td>${paymentLink}</td>
                <td class="text-end">${fmt(nominal)}</td>
                <td>
                    <span class="badge ${badgeClass}">
                        ${status}
                    </span>
                </td>
                <td>${escapeHtml(petugas)}</td>
            </tr>
        `;
    }


    // --------------------
    // Detail modal
    // --------------------
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (!id) return Swal.fire('Error', 'Order ID tidak ditemukan', 'error');

        $.ajax({
            url: "{{ route('penjualan.pembayaran.show', ['id' => '__ID__']) }}".replace('__ID__', encodeURIComponent(id)),
            method: 'GET',
            dataType: 'json'
        }).done(function(res) {
            if (!res) return Swal.fire('Gagal', 'Gagal ambil detail pesanan', 'error');
            var o = (res.success && res.order) ? res.order : res;
            $('#d_id').text(o.id || '-');
            $('#d_tanggal').text(o.created_at || '-');
            $('#d_sales').text(o.sales_name || '-');
            $('#d_phone').text(o.sales_phone || o.salesPhone || '-');
            $('#d_toko').text(o.toko_name || '-');

            var $it = $('#detail_items tbody').empty();
            var total = 0;
            (o.items || []).forEach(function(it, idx) {
                var qty = Number(it.qty || it.jumlah || 0);
                var harga = Number(it.harga || it.harga_satuan || 0);
                var sub = Number(it.sub_total || qty * harga);
                total += sub;
                $it.append('<tr><td>' + (idx + 1) + '</td><td>' + (it.nama || '-') + '</td><td class="text-center">' + qty + '</td><td class="text-end">' + fmt(harga) + '</td><td class="text-end">' + fmt(sub) + '</td></tr>');
            });
            $('#d_total').text(fmt(total));

            var $h = $('#history_table tbody').empty();
            var sum = 0;

            (o.payments || []).forEach(function(p, idx) {
                var nom = Number(p.nominal || p.amount || 0);
                var method = (p.method || p.metode || p.jenis_bayar || '').toLowerCase();
                var status = (p.status || '').toLowerCase();

                // ? CASH SELALU DIHITUNG
                if (method === 'cash') {
                    sum += nom;
                }
                // ? VA HANYA JIKA PAID
                else if (method === 'va' && status === 'paid') {
                    sum += nom;
                }

                $h.append(renderPaymentRow(idx + 1, p));
            });

            $('#history_total').text(fmt(sum));



            $('#modalDetail').modal('show');
        }).fail(function(){
            Swal.fire('Gagal', 'Gagal ambil detail pesanan', 'error');
        });
    });

    // --------------------
    // Open bayar modal
    // --------------------
    $(document).on('click', '.btn-bayar', function(e) {
        e.preventDefault();
        var payload = $(this).attr('data-order');
        if (payload) {
            try {
                var o = JSON.parse(payload);
                openBayarModal(o); return;
            } catch(err) {
                try {
                    var txt = payload.replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&amp;/g, '&');
                    var o2 = JSON.parse(txt);
                    openBayarModal(o2); return;
                } catch(err2) {}
            }
        }
        var id = $(this).data('id');
        if (!id) return Swal.fire('Error', 'Order ID tidak ditemukan', 'error');
        $.ajax({
            url: "{{ route('penjualan.pembayaran.show', ['id' => '__ID__']) }}".replace('__ID__', encodeURIComponent(id)),
            method: 'GET',
            dataType: 'json'
        }).done(function(res) {
            var o = (res.success && res.order) ? res.order : res;
            openBayarModal(o);
        }).fail(function(){ Swal.fire('Gagal', 'Gagal ambil data order', 'error'); });
    });

    // --------------------
    // Core: openBayarModal + state + handlers
    // --------------------
    // unbind old handlers to avoid duplicates
    $('#pay_amount').off('.pay').off('.readiness');
    $('#pay_method').off('.pay').off('.readiness');
    $form.off('.pay');

    function openBayarModal(o) {
        var originalTotal = Number(o.total || 0);
        var paid = Number(o.sudah_bayar || o.paid_amount || o.paid || 0);
        var remaining = Math.max(0, originalTotal - paid);

        $modalBayar.data('original_total', originalTotal);
        $modalBayar.data('paid_amount', paid);
        $modalBayar.data('remaining', remaining);

        $('#pay_order_id').val(o.id || '');
        $('#pay_kode').val(o.kode || o.id || '');
        $('#pay_phone').val(o.sales_phone || o.salesPhone || '');
        $('#pay_total').val(fmt(remaining));
        $('#pay_sudah').val(fmt(paid));

        // jangan reset total/jumlah bila tidak perlu, hanya kosongkan input bayar agar user isi
        $('#pay_amount').val('');
        $('#pay_kurang').val(fmt(remaining));
        $('#pay_msg').text('');

        // reset hanya metode & snap container agar modal bersih setiap buka
        $('#pay_method').val('');
        $('#snap-container').hide().empty();

        disablePayBtnUI();
        // show modal normally (allow backdrop)
        $modalBayar.modal({ backdrop: true, keyboard: true });
        $modalBayar.modal('show');

        setTimeout(checkFormReadyFull, 120);
    }

    function disablePayBtnUI() {
        $btnPay.prop('disabled', true).attr('aria-disabled','true').addClass('disabled');
    }
    function enablePayBtnUI() {
        $btnPay.prop('disabled', false).removeAttr('aria-disabled').removeClass('disabled');
    }

    function checkFormReadyFull(){
        var method = ($('#pay_method').val() || '').trim();
        var amount = Number($('#pay_amount').val() || 0);
        var remaining = Number($modalBayar.data('remaining') || 0);
        var itemsLen = parseHiddenItems().length;

        if (method && amount > 0 && amount <= remaining && itemsLen >= 0) {
            enablePayBtnUI();
            return true;
        } else {
            disablePayBtnUI();
            return false;
        }
    }

    $('#pay_amount').on('input.pay', function() {
        var val = Number($(this).val() || 0);
        var remaining = Number($modalBayar.data('remaining') || 0);

        if (!val || val <= 0) {
            $('#pay_kurang').val(fmt(remaining));
            $('#pay_msg').text('');
        } else if (val >= remaining) {
            $(this).val(remaining);
            $('#pay_kurang').val(fmt(0));
            $('#pay_msg').text('Nilai dibatasi sampai sisa tagihan: ' + fmt(remaining));
        } else {
            var after = Math.max(0, remaining - val);
            $('#pay_kurang').val(fmt(after));
            $('#pay_msg').text(after === 0 ? 'Lunas.' : 'Masih kurang: ' + fmt(after));
        }
        checkFormReadyFull();
    });

    $('#pay_method').on('change.pay', checkFormReadyFull);

    // --------------------
    // SNAP MODE helpers: enter/exit snap inside modal
    // --------------------
    function enterSnapMode() {
        // store original parts if not stored
        if (!$modalBayar.data('origHeader')) $modalBayar.data('origHeader', $modalBayar.find('.modal-header').html());
        if (!$modalBayar.data('origBody')) $modalBayar.data('origBody', $modalBayar.find('.modal-body').html());
        if (!$modalBayar.data('origFooter')) $modalBayar.data('origFooter', $modalBayar.find('.modal-footer').html());

        // hide header/footer
        $modalBayar.find('.modal-header').hide();
        $modalBayar.find('.modal-footer').hide();

        // replace modal-body with snap container + close btn
        var snapHtml = '<div id="snap-container" style="min-height:360px;"></div>' +
                       '<div class="text-end mt-2"><button type="button" id="btnCloseSnap" class="btn btn-secondary btn-sm">Tutup Snap</button></div>';
        $modalBayar.find('.modal-body').html(snapHtml);

        // ensure modal not closed by backdrop/esc while in snap-mode (use Bootstrap api if available)
        try {
            var modalEl = $modalBayar[0];
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var inst = bootstrap.Modal.getInstance(modalEl);
                if (inst) inst._config.backdrop = 'static', inst._config.keyboard = false;
            }
        } catch(e){}
    }

    function exitSnapMode() {
        // try to close snap iframe via API if available
        try { if (window.snap && typeof window.snap.close === 'function') window.snap.close(); } catch(e){}

        // restore header/footer/body
        var origBody = $modalBayar.data('origBody') || '';
        var origHeader = $modalBayar.data('origHeader') || '';
        var origFooter = $modalBayar.data('origFooter') || '';

        $modalBayar.find('.modal-body').html(origBody);
        $modalBayar.find('.modal-header').html(origHeader).show();
        $modalBayar.find('.modal-footer').html(origFooter).show();

        // restore backdrop/keyboard default (Bootstrap)
        try {
            var modalEl = $modalBayar[0];
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var inst = bootstrap.Modal.getInstance(modalEl);
                if (inst) inst._config.backdrop = true, inst._config.keyboard = true;
            }
        } catch(e){}

        // re-bind any controls that were replaced (we rely on openBayarModal to re-fill fields when reopened)
        setTimeout(function(){ checkFormReadyFull(); }, 120);
    }

    // --------------------
    // Submit handler (AJAX)
    // --------------------
    $form.on('submit.pay', function(e) {
        e.preventDefault();
        if (!checkFormReadyFull()) return Swal.fire('Perhatian', 'Lengkapi metode, jumlah (? sisa) dan pastikan keranjang tidak kosong.', 'warning');

        var method = $('#pay_method').val();
        var orderId = $('#pay_order_id').val() || '';
        var amount = Number($('#pay_amount').val() || 0);
        var csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        // ---------- CASE: CASH ----------
        if (method === 'cash') {
            var url = "{{ route('penjualan.pembayaran.cash') }}";
            var fd = new FormData();
            fd.append('order_id', orderId);
            fd.append('jumlah_bayar', String(amount));
            fd.append('jenis_bayar', 'cash');
            var itemsRaw = readHiddenItemsRaw();
            try { JSON.parse(itemsRaw); fd.append('items', itemsRaw); } catch(e) { fd.append('items','[]'); }
            if (csrf) fd.append('_token', csrf);

            $btnPay.prop('disabled', true).addClass('disabled').text('Menyimpan...');
            $.ajax({
                url: url,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false
            }).done(function(res) {
                $btnPay.prop('disabled', false).removeClass('disabled').text('Bayar');
                if (res && res.success) {
                    Swal.fire('Sukses', res.message || 'Pembayaran tersimpan', 'success');
                    if (res.new_payment) {
                        var idx = $('#history_table tbody tr').length + 1;
                        $('#history_table tbody').append(renderPaymentRow(idx, res.new_payment));
                    }
                    if (res.order) {
                        var newRemaining = Number(res.order.kurang || 0);
                        var newPaid = Number(res.order.sudah_bayar || 0);
                        $modalBayar.data('remaining', newRemaining);
                        $modalBayar.data('paid_amount', newPaid);
                        $('#history_total').text(fmt(newPaid));
                        $('#pay_kurang').val(fmt(newRemaining));
                        $('#pay_sudah').val(fmt(newPaid));
                    }
                    if (table && table.ajax) table.ajax.reload(null, false);
                    $modalBayar.modal('hide');
                    // ensure reset after hide
                    setTimeout(function(){ resetBayarModal(); }, 350);
                } else {
                    var txt = (res && (res.message || (res.errors ? JSON.stringify(res.errors) : null))) || 'Gagal menyimpan';
                    Swal.fire('Gagal', txt, 'error');
                }
            }).fail(function(xhr) {
                $btnPay.prop('disabled', false).removeClass('disabled').text('Bayar');
                var msg = 'Terjadi kesalahan';
                try {
                    if (xhr && xhr.responseJSON) {
                        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        else if (xhr.responseJSON.errors) {
                            var e = xhr.responseJSON.errors;
                            if (Array.isArray(e)) msg = e.join(', ');
                            else msg = Object.values(e).map(it => Array.isArray(it) ? it[0] : it).join(', ');
                        }
                    }
                } catch (e) {}
                Swal.fire('Gagal', msg, 'error');
            });
            return;
        }

        // ---------- CASE: VA ----------
        if (method === 'va') {
            var urlCreateSnap = "{{ route('penjualan.pembayaran.createSnap') }}";
            var payload = {
                order_id: orderId,
                jumlah_bayar: amount,
                first_name: $('#pay_kode').val() || 'Customer',
                last_name: '-',
                email: $('#pay_kode').val() ? ($('#pay_kode').val() + '@example.com') : 'customer@example.com',
                phone: $('#pay_phone').val() || ''
            };

            $btnPay.prop('disabled', true).addClass('disabled').text('Membuat Payment Link...');

            $.ajax({
                url: urlCreateSnap,
                method: 'POST',
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
            }).done(function(res) {
                $btnPay.prop('disabled', false).removeClass('disabled').text('Bayar');
                if (!res || !res.snap_token) {
                    var txt = (res && (res.message || (res.error || 'Gagal generate snap token'))) || 'Gagal generate snap token';
                    return Swal.fire('Gagal', txt, 'error');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Payment link berhasil dibuat',
                    html: '<small>Link pembayaran telah dibuat dan disimpan di sistem.</small>',
                });

                // reload tabel agar histori tampil
                if (table && table.ajax) table.ajax.reload(null, false);

                // tutup modal
                $modalBayar.modal('hide');

                // reset modal
                setTimeout(function(){
                    resetBayarModal();
                }, 300);


                // embed inside the modal
                try {
                    window.snap.embed(res.snap_token, {
                        embedId: 'snap-container'
                    });
                } catch (err) {
                    console.error('snap embed error', err);
                    Swal.fire('Error', 'Gagal menampilkan Snap: ' + (err && err.message ? err.message : ''), 'error');
                    exitSnapMode();
                    return;
                }

                // GANTI / PASTEKAN handler ini (replace existing #btnCloseSnap handler)
                $(document).off('click', '#btnCloseSnap').on('click', '#btnCloseSnap', function(e){
                    e.preventDefault();
                    // tutup modal sepenuhnya -> user kembali ke page (modal hilang)
                    closeModalByEl($('#modalBayar'));

                    // setelah modal hide (animation selesai), restore state supaya saat buka lagi modal bersih
                    // gunakan delay ~300ms agar race condition tidak terjadi
                    setTimeout(function(){
                        resetBayarModal();
                    }, 350);
                });

                // don't close modal here - user interacts with embedded snap inside modal
            }).fail(function(xhr) {
                $btnPay.prop('disabled', false).removeClass('disabled').text('Bayar');
                var msg = 'Gagal generate snap token';
                try {
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                } catch(e){}
                Swal.fire('Gagal', msg, 'error');
            });

            return;
        }

        Swal.fire('Perhatian', 'Metode pembayaran tidak dikenali', 'warning');
    });

    // payment link click -> show details
    $(document).on('click', '.payment-link', function(e) {
        e.preventDefault();
        var $a = $(this);
        var link = $a.data('payment-link') || '';
        var nominal = Number($a.data('nominal') || 0);
        var idK = $a.data('id-karyawan') || '-';

        var html = '<div><strong>Payment Link:</strong><br><small style="word-break:break-all;">' + escapeHtml(link) + '</small></div>' +
            '<div class="mt-2"><strong>Nominal:</strong> ' + fmt(nominal) + '</div>' +
            '<div><strong>ID Karyawan:</strong> ' + escapeHtml(idK) + '</div>';

        Swal.fire({ title: 'Detail Payment Link', html: html, showCloseButton: true, confirmButtonText: 'Tutup' });
    });

    // initial state
    disablePayBtnUI();
    setTimeout(checkFormReadyFull, 120);

    // --------------------
    // Modal reset helpers & bindings
    // --------------------
    function resetDetailModal() {
        $('#detail_items tbody').empty();
        $('#history_table tbody').empty();
        $('#d_id').text('-'); $('#d_tanggal').text('-'); $('#d_sales').text('-'); $('#d_toko').text('-');
        $('#d_total').text('Rp 0'); $('#history_total').text('Rp 0');
    }

    function resetBayarModal() {
        // DON'T reset totals / amounts (so user sees them), only reset method/snap state and button
        $('#pay_method').val('');
        $('#snap-container').hide().empty();
        $('#btnPaySubmit').prop('disabled', true).addClass('disabled').text('Bayar');
        $('#pay_msg').text('');
        // ensure modal-body restored if we were in snap-mode
        exitSnapMode();
    }

    function closeModalByEl($modal) {
        if (!$modal || !$modal.length) return;
        var modalEl = $modal[0];
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                inst.hide();
                return;
            }
        } catch(e){}
        try { $modal.modal('hide'); } catch(e) {}
    }

    // close buttons
    $('#btnCloseDetail').off('click.close').on('click.close', function(e){
        e.preventDefault();
        closeModalByEl($('#modalDetail'));
        setTimeout(resetDetailModal, 220);
    });

    $('#btnCloseBayar').off('click.close').on('click.close', function(e){
        e.preventDefault();
        closeModalByEl($('#modalBayar'));
        setTimeout(resetBayarModal, 220);
    });

    // when modal hidden -> ensure reset state
    $('#modalDetail').on('hidden.bs.modal', function(){ resetDetailModal(); });
    $('#modalBayar').off('hidden.reset').on('hidden.bs.modal', function(){
        resetBayarModal();
    });

});
</script>
@endpush
