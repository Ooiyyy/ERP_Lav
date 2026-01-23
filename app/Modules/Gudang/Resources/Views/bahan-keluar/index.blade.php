@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 text-gray-800">Bahan Baku Keluar</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalBahanKeluar">
            <i class="fas fa-minus"></i> Tambah Bahan Keluar
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tableBahanKeluar">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Total Item</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->tanggal_transaksi }}</td>
                            <td>{{ $t->detail->count() }}</td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-info btn-sm mx-1 btn-detail"data-id="{{ $t->id }}">
                                    Detail
                                </button>
                                <button class="btn btn-sm btn-warning mx-1 btn-edit" data-id="{{ $t->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger mx-1 btn-delete" data-id="{{ $t->id }}">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="modalBahanKeluar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form id="formBahanKeluar">
                @csrf
                <input type="hidden" id="edit_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">Bahan Baku Keluar</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Tanggal Transaksi</label>
                        <input type="datetime-local" name="tanggal_transaksi" class="form-control" required>
                    </div>

                    <hr>
                    <table class="table table-bordered" id="tableDetailKeluar">
                        <thead>
                            <tr>
                                <th>Bahan Baku</th>
                                <th>Stok</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <button type="button" class="btn btn-success btn-sm" id="btnAddRowKeluar">
                        + Tambah Baris
                    </button>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>
<div class="modal fade" id="modalDetailBahanKeluar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Bahan Baku Keluar</h5>
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <div class="modal-body">

                <p><strong>ID Transaksi:</strong> <span id="d_id"></span></p>
                <p><strong>Tanggal:</strong> <span id="d_tanggal"></span></p>
                <hr>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bahan Baku</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody id="detail_items"></tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(function () {

    let rowIndex = 0;

    // ================= DATATABLE =================
   $('#tableBahanKeluar').DataTable({
    pageLength: 10,
    responsive: true,
    language: {
        search: "Search:",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        zeroRecords: "No matching records found",
        paginate: {
            previous: "Previous",
            next: "Next"
        }
    }
});

    // ================= ADD ROW =================
    $('#btnAddRowKeluar').click(function () {

        let row = `
        <tr>
            <td>
                <select name="items[${rowIndex}][id_bahan_baku]"
                        class="form-control select-bahan" required>
                    <option value="">-- Pilih --</option>
                    @foreach (\App\Models\BahanBaku::all() as $b)
                        <option value="{{ $b->id }}"
                                data-stok="{{ $b->stok_bahan_baku }}"
                                data-satuan="{{ $b->satuan }}">
                            {{ $b->nama }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="text-center stok-text">0</td>
            <td>
                <input type="number"
                       name="items[${rowIndex}][jumlah]"
                       class="form-control"
                       min="1" required>
            </td>
            <td>
                <input type="text"
                       name="items[${rowIndex}][satuan]"
                       class="form-control satuan-text"
                       readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove">X</button>
            </td>
        </tr>`;

        $('#tableDetailKeluar tbody').append(row);
        rowIndex++;
    });

    // ================= AUTO STOK & SATUAN =================
    $(document).on('change', '.select-bahan', function () {
        let stok   = $(this).find(':selected').data('stok') || 0;
        let satuan = $(this).find(':selected').data('satuan') || '-';

        let tr = $(this).closest('tr');
        tr.find('.stok-text').text(stok);
        tr.find('.satuan-text').val(satuan);
    });

    // ================= REMOVE ROW =================
    $(document).on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
    });

    // ================= SUBMIT =================
    $('#formBahanKeluar').submit(function (e) {
        e.preventDefault();

        let id = $('#edit_id').val();
        let url = id
            ? `/gudang/bahan-baku-keluar/${id}`
            : `{{ route('gudang.bahan-keluar.store') }}`;

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
                    success: function () {
                        Swal.fire('Berhasil', 'Data tersimpan', 'success');
                        location.reload();
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal', 'error');
                    }
                });
            }
        });
    });

});

// ================= DELETE =================
$(document).on('click', '.btn-delete', function () {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Hapus data?',
        text: 'Stok akan dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/gudang/bahan-baku-keluar/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function () {
                    Swal.fire('Berhasil', 'Data dihapus', 'success');
                    location.reload();
                }
            });
        }
    });
});
function resetForm() {
    $('#formBahanKeluar')[0].reset();
    $('#tableDetail tbody').html('');
    $('#edit_id').val('');
    rowIndex = 0;
}

$(document).on('click', '.btn-edit', function () {

    let id = $(this).data('id');
    
    // reset form
    resetForm();
    mode = 'edit';

    $.ajax({
        url: "{{ url('gudang/bahan-baku-keluar') }}/" + id,
        type: "GET",
        success: function (res) {

            // set id edit
            $('#edit_id').val(res.id);
            $('#method').val('PUT');

            // isi header
            $('input[name="tanggal_transaksi"]').val(res.tanggal_transaksi);

            // isi detail
            res.detail.forEach(function (d) {

                $('#btnAddRow').trigger('click');
                let idx = rowIndex - 1;

                // set bahan
                $(`select[name="items[${idx}][id_bahan_baku]"]`).val(d.id_bahan_baku);

                // set jumlah
                $(`input[name="items[${idx}][jumlah]"]`).val(d.jumlah);

                // set satuan (readonly)
                $(`input[name="items[${idx}][satuan]"]`).val(d.satuan);
            });

            // buka modal
            $('.modal-title').text('Edit Bahan Baku Keluar');
            $('#modalBahanKeluar').modal('show');
        },
        error: function () {
            Swal.fire('Gagal', 'Data tidak dapat dimuat', 'error');
        }
    });
});
$(document).on('click', '.btn-detail', function () {
    let id = $(this).data('id');

    $.get(`{{ url('gudang/bahan-baku-keluar/detail') }}/${id}`, function (res) {

        // header
        $('#d_id').text(res.id);
        $('#d_tanggal').text(res.tanggal);
        // detail table
        let rows = '';
        if (res.detail.length) {
            res.detail.forEach((d, i) => {
                rows += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${d.nama}</td>
                    <td>${d.jumlah}</td>
                    <td>${d.satuan}</td>
                </tr>`;
            });
        } else {
            rows = `
            <tr>
                <td colspan="4" class="text-center">
                    Tidak ada detail
                </td>
            </tr>`;
        }

        $('#detail_items').html(rows);

        // show modal
        $('#modalDetailBahanKeluar').modal('show');
    });
});
</script>

@endpush
