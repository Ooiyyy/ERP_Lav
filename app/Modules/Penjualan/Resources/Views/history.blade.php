{{-- resources/views/penjualan/history.blade.php --}}
@extends('layouts.app')

@push('title')
    History Pelunasan
@endpush

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">History Pelunasan</h4>
            <div>
                <!-- optional controls -->
            </div>
        </div>

        {{-- Filters (SB Admin2 / Bootstrap 4) --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong class="mb-0">Filter</strong>

                <div class="btn-group btn-group-sm" role="group" aria-label="filter actions">
                    <button class="btn btn-outline-secondary d-md-none" type="button" data-toggle="collapse"
                        data-target="#historyFiltersBody" aria-expanded="false" aria-controls="historyFiltersBody">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button id="btnFilterReset" class="btn btn-secondary" title="Reset filter">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>

            <div id="historyFiltersBody" class="collapse show">
                <div class="card-body">
                    <form id="historyFiltersForm" class="form-row align-items-end">
                        <div class="form-group col-lg-4 col-md-4 col-4">
                            <label class="small mb-1">Dari (Range)</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                </div>
                                <input type="date" id="fDateFrom" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-md-4 col-4">
                            <label class="small mb-1">Sampai (Range)</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-times"></i></span>
                                </div>
                                <input type="date" id="fDateTo" class="form-control form-control-sm">
                            </div>
                        </div>


                        <div class="form-group col-lg-4 col-md-4 col-4">
                            <label class="small mb-1">Sales</label>
                            <select id="fSales" class="form-control form-control-sm">
                                <option value="">Semua Sales</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-6 col-12">
                            <label class="small mb-1 d-block">&nbsp;</label>
                            <div>
                                <!-- Placeholder untuk tombol tambahan nanti (Apply/Preview) -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="historyTable" class="table table-bordered table-sm" style="width:100%;">
                <thead class="table-light">
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal Pelunasan</th>
                        <th>Nama Sales</th>
                        <th>Nomor HP</th> <!-- <- baru -->
                        <th>Nama Toko</th>
                        <th class="text-end">Nominal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pembayaran / Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12"><strong>Kode / ID:</strong> <span id="detailKodeVal">-</span></div>
                        <div class="col-md-12"><strong>Tanggal:</strong> <span id="detailTanggal">-</span></div>
                    </div>

                    <p>
                        <strong>Nama Sales:</strong> <span id="detailSalesName">-</span><br>
                        <strong>Nomor HP:</strong> <span id="detailPhone">-</span><br> <!-- <- baru -->
                        <strong>Nama Toko:</strong> <span id="detailTokoName">-</span>
                    </p>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm" id="detailItemsTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th id="detailTotalText" class="text-end">Rp 0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <h6>Riwayat Pembayaran</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="paymentHistoryTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th class="text-end">Nominal</th>
                                    <th>Status</th> <!-- ⬅️ BARU -->
                                    <th>Petugas</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total Sudah Bayar</th>
                                    <th id="historyTotal" class="text-end">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <a href="#" id="btnCetakPdf" class="btn btn-success" target="_blank"
                        rel="noopener">CetakPDF</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // ------------ helper -------------
            function fmtRupiah(n) {
                n = Number(n) || 0;
                return 'Rp ' + n.toLocaleString('id-ID');
            }

            function getCsrfToken() {
                var m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.getAttribute('content') : '';
            }

            // populate bulan
            var months = [{
                    v: '01',
                    n: 'Januari'
                }, {
                    v: '02',
                    n: 'Februari'
                }, {
                    v: '03',
                    n: 'Maret'
                }, {
                    v: '04',
                    n: 'April'
                },
                {
                    v: '05',
                    n: 'Mei'
                }, {
                    v: '06',
                    n: 'Juni'
                }, {
                    v: '07',
                    n: 'Juli'
                }, {
                    v: '08',
                    n: 'Agustus'
                },
                {
                    v: '09',
                    n: 'September'
                }, {
                    v: '10',
                    n: 'Oktober'
                }, {
                    v: '11',
                    n: 'November'
                }, {
                    v: '12',
                    n: 'Desember'
                }
            ];
            months.forEach(function(m) {
                $('#fMonth').append($('<option>').val(m.v).text(m.n));
            });

            // populate 5 tahun terakhir (sesuai permintaan)
            var thisYear = (new Date()).getFullYear();
            for (var y = thisYear; y >= thisYear - 4; y--) {
                $('#fYear').append($('<option>').val(y).text(y));
            }

            // load select options (sales, toko) — endpoint harus mengembalikan array {id, nama/name}
            function loadSelectOptions(url, $sel, placeholder) {
                $sel.prop('disabled', true);
                $.ajax({
                        url: url,
                        method: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken()
                        }
                    })
                    .done(function(resp) {
                        $sel.empty().append($('<option>').val('').text(placeholder || 'Semua'));
                        var list = resp && resp.data ? resp.data : resp;
                        if (!Array.isArray(list)) list = [];
                        list.forEach(function(it) {
                            var val = it.id || it.value || it.key || '';
                            var txt = it.nama || it.name || it.text || it.label || val;
                            $sel.append($('<option>').val(val).text(txt));
                        });
                    })
                    .fail(function() {
                        $sel.empty().append($('<option>').val('').text(placeholder || 'Semua'));
                    })
                    .always(function() {
                        $sel.prop('disabled', false);
                    });
            }

            loadSelectOptions('/penjualan/history/filters/sales', $('#fSales'), 'Semua Sales');
            loadSelectOptions('/penjualan/history/filters/toko', $('#fToko'), 'Semua Toko');

            // ------------- DataTable -------------
            var ajaxUrl = '/penjualan/history/data';
            var table = $('#historyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl,
                    type: 'GET',
                    data: function(d) {
                        d.year = $('#fYear').val();
                        d.month = $('#fMonth').val();
                        d.date_from = $('#fDateFrom').val();
                        d.date_to = $('#fDateTo').val();
                        d.sales = $('#fSales').val();
                        d.toko = $('#fToko').val();
                    }
                },

                // layout sesuai Bootstrap: tombol di kiri, search di kanan
                dom: "<'row mb-2'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

                buttons: [{
                    extend: 'pdfHtml5',
                    download: 'open',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-secondary',
                    filename: (function() {
                        var d = new Date();
                        return 'laporan_penjualan_' + d.toISOString().slice(0, 10);
                    })(),
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    },
                    customize: function(doc) {
                        // sederhana: title + footer timestamp + format nominal
                        var title = 'LAPORAN PENJUALAN';
                        doc.header = function() {
                            return {
                                text: title,
                                margin: [20, 10],
                                alignment: 'left',
                                style: 'headerStyle'
                            };
                        };
                        var gen = new Date();
                        var genStr = gen.getFullYear() + '-' + String(gen.getMonth() + 1)
                            .padStart(2, '0') + '-' + String(gen.getDate()).padStart(2, '0') +
                            ' ' + String(gen.getHours()).padStart(2, '0') + ':' + String(gen
                                .getMinutes()).padStart(2, '0');
                        doc.footer = function(currentPage, pageCount) {
                            return {
                                columns: [{
                                    text: 'Generated: ' + genStr,
                                    alignment: 'left',
                                    margin: [20, 0]
                                }, {
                                    text: 'Halaman ' + currentPage.toString() +
                                        ' / ' + pageCount,
                                    alignment: 'right',
                                    margin: [0, 0, 20, 0]
                                }],
                                fontSize: 9
                            };
                        };
                        doc.defaultStyle = doc.defaultStyle || {};
                        doc.defaultStyle.fontSize = 10;
                    }
                }, ],

                columns: [{
                        data: 'id_order',
                        name: 'id_order'
                    }, // ID Transaksi
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    }, // Tanggal pembayaran (last)
                    {
                        data: 'sales_name',
                        name: 'sales_name'
                    }, // Nama Sales
                    { // Nomor HP (baru)
                        data: 'sales_phone',
                        name: 'sales_phone',
                        render: function(v) {
                            return v ? $('<div>').text(v).html() : '-';
                        }
                    },
                    {
                        data: 'toko_name',
                        name: 'toko_name'
                    }, // Nama Toko
                    {
                        data: 'nominal',
                        name: 'nominal',
                        className: 'text-end',
                        render: function(data, type, row) {
                            return fmtRupiah(data);
                        }
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        className: 'text-center'
                    }, // LUNAS
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            var orderId = row.id_order || row.order_kode || '';
                            // safe-encode payload for attribute
                            var payload = {
                                order_id: orderId,
                                order_kode: row.order_kode || ''
                            };
                            var payloadEscaped = $('<div/>').text(JSON.stringify(payload)).html();
                            return '<button class="btn btn-sm btn-success btn-detail-history" data-payload=\'' +
                                payloadEscaped + '\'>Detail</button>';
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ], // sort by tanggal desc
                lengthMenu: [10, 25, 50],
                pageLength: 10
            });
            // ---------- events: reload on filter change ----------
            $('#fDateFrom, #fDateTo, #fYear, #fMonth, #fSales, #fToko').on('change', function() {
                table.ajax.reload();
            });

            $('#btnFilterReset').on('click', function() {
                $('#historyFiltersForm')[0].reset();
                if ($.fn.select2) {
                    $('#fSales,#fToko').val(null).trigger('change');
                }
                table.ajax.reload();
            });

            // delegated handler: Detail button -> open PembayaranController@show by order id
            $(document).on('click', '.btn-detail-history', function(e) {
                e.preventDefault();
                var payload = {};
                try {
                    payload = JSON.parse($('<div/>').html($(this).attr('data-payload') || '{}').text());
                } catch (e) {
                    payload = {};
                }
                var orderId = payload.order_id || payload.order_kode || '';
                if (!orderId) {
                    Swal.fire('Error', 'Order ID tidak ditemukan', 'error');
                    return;
                }

                // reset modal placeholders
                $('#detailKodeVal').text(payload.order_kode || orderId);
                $('#detailTanggal').text('-');
                $('#detailSalesName').text('-');
                $('#detailPhone').text('-'); // reset phone
                $('#detailTokoName').text('-');
                $('#detailItemsTable tbody').html('<tr><td colspan="5">Memuat...</td></tr>');
                $('#paymentHistoryTable tbody').html('<tr><td colspan="5">Memuat...</td></tr>');
                $('#detailTotalText').text('Rp 0');
                $('#historyTotal').text('Rp 0');

                var url = '/penjualan/pembayaran/' + encodeURIComponent(orderId);

                fetch(url, {
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(resp) {
                        if (!resp.ok) throw new Error('Gagal memuat data');
                        return resp.json();
                    })
                    .then(function(json) {
                        var o;
                        if (json && json.success && json.order) o = json.order;
                        else o = json;

                        $('#detailKodeVal').text(o.kode || orderId);
                        $('#detailTanggal').text(o.created_at || o.tanggal || '-');
                        $('#detailSalesName').text(o.sales_name || '-');
                        $('#detailPhone').text(o.sales_phone || o.salesPhone || '-'); // tampilkan phone
                        $('#detailTokoName').text(o.toko_name || '-');
                        $('#btnCetakPdf').attr('href', '/penjualan/history/nota/' + encodeURIComponent(
                            orderId));

                        // items
                        var $it = $('#detailItemsTable tbody').empty();
                        var total = 0;
                        (o.items || []).forEach(function(it, idx) {
                            var qty = Number(it.qty || it.jumlah || 0);
                            var harga = Number(it.harga || it.harga_satuan || 0);
                            var sub = Number(it.sub_total || qty * harga);
                            total += sub;
                            var tr = '<tr>' +
                                '<td>' + (idx + 1) + '</td>' +
                                '<td>' + (it.nama || it.product_name || '-') + '</td>' +
                                '<td class="text-center">' + qty + '</td>' +
                                '<td class="text-end">' + fmtRupiah(harga) + '</td>' +
                                '<td class="text-end">' + fmtRupiah(sub) + '</td>' +
                                '</tr>';
                            $it.append(tr);
                        });
                        $('#detailTotalText').text(fmtRupiah(total));

                        // payments
                        var $pt = $('#paymentHistoryTable tbody').empty();
                        var sum = 0;

                        function normalizeStatus(p, method) {
                            if (method === 'cash') return 'PAID';

                            return (
                                p.status ||
                                p.va_status ||
                                p.payment_status ||
                                p.transaction_status ||
                                'pending'
                            ).toString().toUpperCase();
                        }

                        (o.payments || []).forEach(function(p, idx) {
                            var nominal = Number(p.nominal || 0);

                            // METODE
                            // var method = (p.method || (p.channel ? 'va' : 'cash'))
                            //     .toLowerCase();

                            // =====================
                            // METODE PEMBAYARAN
                            // =====================
                            var method = '-';

                            // CASH
                            if ((p.jenis_bayar || p.method || '').toLowerCase() === 'cash') {
                                method = 'CASH';
                            }
                            // VA / ONLINE → ambil dari channel
                            else {
                                if (p.channel) {
                                    method = String(p.channel).toUpperCase(); // BRI / BCA / QRIS / GOPAY
                                } else {
                                    method = 'ONLINE';
                                }
                            }


                            // STATUS
                            var status = normalizeStatus(p, (p.jenis_bayar || p.method || ''));

                            // HITUNG TOTAL
                            if (
                                (p.jenis_bayar || p.method || '').toLowerCase() === 'cash' ||
                                status === 'PAID'
                            ) {
                                sum += nominal;
                            }


                            // TANGGAL
                            var tanggal = p.tanggal || p.tanggal_transaksi || p
                                .tanggal_pembayaran || null;
                            var tstr = tanggal ? new Date(tanggal).toLocaleString() : '-';

                            // var petugas = p.petugas || p.channel || '-';
                            var petugas = '-';

                            if ((p.method || '').toUpperCase() === 'CASH') {
                                petugas = p.petugas || '-'; // nama karyawan
                            } else {
                                petugas = 'ONLINE';
                            }


                            // BADGE
                            var badge = '';
                            switch (status) {
                                case 'PAID':
                                    badge =
                                        '<span class="badge bg-success text-white">PAID</span>';
                                    break;
                                case 'PENDING':
                                    badge =
                                        '<span class="badge bg-warning text-dark">TIDAK TERKONFIRMASI</span>';
                                    break;
                                case 'EXPIRED':
                                    badge =
                                        '<span class="badge bg-primary text-white">EXPIRED</span>';
                                    break;
                                case 'FAILED':
                                    badge =
                                        '<span class="badge bg-danger text-white">FAILED</span>';
                                    break;
                                default:
                                    badge = '<span class="badge bg-dark text-white">' + status +
                                        '</span>';
                            }

                            var tr = `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${tstr}</td>
                        <td>${method}</td>
                        <td class="text-end">${fmtRupiah(nominal)}</td>
                        <td>${badge}</td>
                        <td>${petugas}</td>
                    </tr>
                `;

                            $pt.append(tr);
                        });



                        $('#historyTotal').text(fmtRupiah(sum));

                    })
                    .catch(function(err) {
                        console.error(err);
                        $('#detailItemsTable tbody').html(
                            '<tr><td colspan="5">Gagal memuat items.</td></tr>');
                        $('#paymentHistoryTable tbody').html(
                            '<tr><td colspan="5">Gagal memuat riwayat pembayaran.</td></tr>');
                        Swal.fire('Gagal', 'Gagal memuat detail. Cek console untuk info.', 'error');
                    })
                    .finally(function() {
                        $('#modalDetail').modal('show');
                    });
            });
            // Pastikan modal bisa ditutup tanpa ubah HTML
            $(document).on('click', '#modalDetail [data-bs-dismiss="modal"]', function(e) {
                e.preventDefault();

                var modalEl = document.getElementById('modalDetail');

                // Bootstrap 5
                if (typeof bootstrap !== 'undefined') {
                    var instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    instance.hide();
                } else {
                    // fallback jQuery
                    $('#modalDetail').modal('hide');
                }
            });


        });
    </script>
@endpush
