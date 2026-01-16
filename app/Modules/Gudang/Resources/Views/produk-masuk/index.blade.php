@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h1 class="h4">Produk Masuk</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalProdukMasuk">
            + Tambah Produk Masuk
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tableProdukMasuk">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Total Item</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $d)
                        <tr>
                            <td>{{ $d->id }}</td>
                            <td>{{ $d->tanggal_transaksi }}</td>
                            <td>{{ $d->detail->count() }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-detail" data-id="{{ $d->id }}">Detail</button>
                                <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $d->id }}">Edit</button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $d->id }}">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ================= MODAL TAMBAH / EDIT ================= -->
<div class="modal fade" id="modalProdukMasuk">
    <div class="modal-dialog modal-lg">
        <form id="formProdukMasuk">
            @csrf
            <input type="hidden" name="id" id="id_produk_masuk">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle">Tambah Produk Masuk</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <input type="datetime-local"
                    name="tanggal_transaksi"
                    id="tanggal_transaksi"
                    class="form-control mb-2"
                    required>
                    <table class="table table-bordered" id="tableDetail">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <button type="button"
                            class="btn btn-success btn-sm"
                            id="btnAddRow">
                        + Baris
                    </button>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div class="modal fade" id="modalDetail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Detail Produk Masuk</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>ID:</strong> <span id="detail_id"></span></p>
                <p><strong>Tanggal:</strong> <span id="detail_tanggal"></span></p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody id="detail_items"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rowIndex = 0;

// ================= DATATABLE =================
$('#tableProdukMasuk').DataTable({
    pageLength: 10,
    responsive: true
});

// ================= ADD ROW =================
$('#btnAddRow').click(function () {
    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][id_produk]" class="form-control" required>
                <option value="">-- Pilih --</option>
                @foreach(\App\Models\Produk::all() as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][jumlah]" class="form-control" min="1" required>
        </td>
        <td>
            <input type="text" name="items[${rowIndex}][satuan]" class="form-control" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm btn-remove">X</button>
        </td>
    </tr>`;
    $('#tableDetail tbody').append(row);
    rowIndex++;
});

// ================= REMOVE ROW =================
$(document).on('click', '.btn-remove', function(){
    $(this).closest('tr').remove();
});

// ================= RESET FORM =================
function resetForm() {
    $('#formProdukMasuk')[0].reset();
    $('#produk_masuk_id').val('');
    $('#tableDetail tbody').html('');
    $('#modalTitle').text('Tambah Produk Masuk');
    rowIndex = 0;
}

$('[data-target="#modalProdukMasuk"]').click(function() {
    resetForm();
});

// ================= SUBMIT =================
$('#formProdukMasuk').submit(function (e) {
    e.preventDefault();

    let id = $('#id_produk_masuk').val();
    let url = id
        ? "{{ url('gudang/produk-masuk') }}/" + id
        : "{{ route('gudang.produk-masuk.store') }}";

    let method = id ? 'PUT' : 'POST';

    Swal.fire({
        title: 'Simpan data?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize() + `&_method=${method}`,
                success: function (res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    location.reload();
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'Gagal menyimpan',
                        'error'
                    );
                }
            });
        }
    });
});

// ================= DETAIL =================
$(document).on('click', '.btn-detail', function() {
    let id = $(this).data('id');

    $.get("{{ url('gudang/produk-masuk') }}/" + id, function(res) {
        $('#detail_id').text(res.data.id);
        $('#detail_tanggal').text(res.data.tanggal_transaksi);
        let rows = '';
        res.data.detail.forEach(function(item) {
            rows += `
            <tr>
                <td>${item.produk?.nama ?? '-'}</td>
                <td>${item.jumlah}</td>
                <td>${item.satuan}</td>
            </tr>`;
        });

        $('#detail_items').html(rows);
        $('#modalDetail').modal('show');
    });
});

// ================= EDIT =================
$(document).on('click', '.btn-edit', function() {
    let id = $(this).data('id');

    $.get("{{ url('gudang/produk-masuk') }}/" + id + "/edit", function(res) {

        resetForm();
        $('#modalTitle').text('Edit Produk Masuk');
        $('#id_produk_masuk').val(res.data.id);
        $('#tanggal_transaksi').val(
            res.data.tanggal_transaksi.replace(' ', 'T').slice(0,16)
        );


        res.data.detail.forEach(function(item) {
            $('#btnAddRow').click();
            let idx = rowIndex - 1;

            $(`select[name="items[${idx}][id_produk]"]`).val(item.id_produk);
            $(`input[name="items[${idx}][jumlah]"]`).val(item.jumlah);
            $(`input[name="items[${idx}][satuan]"]`).val(item.satuan);
        });

        $('#modalProdukMasuk').modal('show');
    });
});

// ================= DELETE =================
$(document).on('click', '.btn-delete', function() {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Hapus data?',
        text: 'Stok produk akan dikurangi',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('gudang/produk-masuk') }}/" + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    location.reload();
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'Gagal menghapus',
                        'error'
                    );
                }
            });
        }
    });
});
</script>
@endpush
