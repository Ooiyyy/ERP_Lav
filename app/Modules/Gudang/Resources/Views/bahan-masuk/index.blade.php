@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h1 class="h4">Bahan Baku Masuk</h1>
        <button class="btn btn-primary" id="btnTambah">
            + Tambah Bahan Masuk
        </button>
    </div>

    <!-- TABLE -->
    <table class="table table-bordered" id="tableBahanMasuk">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Toko</th>
                <th>No Nota</th>
                <th>Total Item</th>
                <th width="120">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>

<!-- MODAL -->
<div class="modal fade" id="modalBahanMasuk">
    <div class="modal-dialog modal-lg">
        <form id="formBahanMasuk">
            @csrf
            <input type="hidden" id="edit_id">
            <input type="hidden" id="method">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Bahan Baku Masuk</h5>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Tanggal Transaksi</label>
                        <input type="datetime-local"
                        name="tanggal_transaksi"
                        class="form-control"
                        required>
                    </div>

                    <div class="form-group">
                        <label>Toko</label>
                        <select name="id_toko" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(\App\Models\Supplier::all() as $t)
                                <option value="{{ $t->id }}">{{ $t->nama_toko }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>No Nota</label>
                        <input type="text"
                               name="no_nota"
                               class="form-control">
                    </div>
                    <hr>
                    <h6>Detail Bahan Baku</h6>

                    <table class="table table-bordered" id="tableDetail">
                        <thead>
                            <tr>
                                <th>Bahan</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                 <th>Nominal</th>
                                <th>Aksi</th>
                            </tr>
                            <tbody>
                                  <td class="text-center align-middle">
                                <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-info btn-sm mx-1 btn-detail"data-id="{{ $t->id }}">
                                    Detail
                                </button>
                                <button class="btn btn-sm btn-warning mx-1 btn-edit" data-id="{{ $t->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger mx-1 btn-delete" data-id="{{ $t->id }}">Hapus</button>
                            </td>
                            </tbody>
                        </thead>
                    </table>

                    <button type="button"
                            class="btn btn-success btn-sm"
                            id="btnAddRow">
                        + Baris
                    </button>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modalDetailBahanMasuk">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Bahan Masuk</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <p><strong>ID Transaksi:</strong> <span id="d_id"></span></p>
                <p><strong>Tanggal:</strong> <span id="d_tanggal"></span></p>
                <p><strong>Nominal Transaksi:</strong> <span id="d_nominal">-</span></p>


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
                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let table;

$(document).ready(function () {
    table = $('#tableBahanMasuk').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('gudang.bahan-masuk.data') }}",
            type: "GET"
        },
        columns: [
            { data: 'id' },
            { data: 'tanggal' },
            { data: 'toko' },
            { data: 'no_nota' },
            { data: 'total_item' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false
            }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: {
                previous: "Sebelumnya",
                next: "Berikutnya"
            }
        }
    });
});


// =========================
// RESET FORM
// =========================
function resetForm() {
    $('#formBahanMasuk')[0].reset();
    $('#tableDetail tbody').html('');
    $('#edit_id').val('');
    $('#method').val('');
    rowIndex = 0;
}

// =========================
// TAMBAH BARIS (SATUAN AUTO)
// =========================
$('#btnAddRow').on('click', function () {
    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][id_bahan_baku]"
                    class="form-control select-bahan"
                    required>
                <option value="">-- Pilih --</option>
                @foreach(\App\Models\BahanBaku::all() as $b)
                    <option value="{{ $b->id }}"
                            data-satuan="{{ $b->satuan }}">
                        {{ $b->nama }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <input type="number"
                   name="items[${rowIndex}][jumlah]"
                   class="form-control"
                   min="1"
                   required>
        </td>
      <td>
            <input type="text"
                   name="items[${rowIndex}][satuan]"
                   class="form-control satuan"
                   readonly>
        </td>
       <td>
            <input type="number"
                   name="items[${rowIndex}][nominal]"
                   class="form-control"
                   min="0"
                   required>
        </td>

        <td class="text-center">
            <button type="button"
                    class="btn btn-danger btn-sm btn-remove">
                X
            </button>
        </td>
    </tr>`;
    $('#tableDetail tbody').append(row);
    rowIndex++;
});

// =========================
// AUTO SET SATUAN
// =========================
$(document).on('change', '.select-bahan', function () {
    let satuan = $(this).find(':selected').data('satuan');
    $(this).closest('tr').find('.satuan').val(satuan ?? '');
});

// =========================
// HAPUS BARIS DETAIL
// =========================
$(document).on('click', '.btn-remove', function () {
    $(this).closest('tr').remove();
});

// =========================
// TAMBAH DATA
// =========================
$('#btnTambah').click(function () {
    mode = 'create';
    resetForm();
    $('.modal-title').text('Tambah Bahan Baku Masuk');
    $('#modalBahanMasuk').modal('show');
});

// =========================
// SIMPAN DATA (CREATE)
// =========================
$('#formBahanMasuk').submit(function (e) {
    e.preventDefault();

    let id = $('#edit_id').val();
    let url;
    let method;

    if (mode === 'edit') {
        url = "{{ url('gudang/bahan-baku-masuk') }}/" + id;
        method = 'PUT';
    } else {
        url = "{{ route('gudang.bahan-masuk.store') }}";
        method = 'POST';
    }

    $.ajax({
        url: url,
        type: method,
        data: $(this).serialize(),
        success: function (res) {
            Swal.fire('Berhasil', res.message, 'success');
            $('#modalBahanMasuk').modal('hide');
            table.ajax.reload(null, false); // ⬅️ TANPA RELOAD PAGE
        },
        error: function (xhr) {
            Swal.fire(
                'Gagal',
                xhr.responseJSON?.message ?? 'Terjadi kesalahan',
                'error'
            );
        }
    });
});

$(document).on('click', '.btn-delete', function () {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Yakin hapus?',
        text: 'Data dan stok akan dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('gudang/bahan-baku-masuk') }}/" + id,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    table.ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire('Gagal', 'Tidak dapat menghapus data', 'error');
                }
            });
        }
    });
});

// =========================
// EDIT DATA
// =========================
$(document).on('click', '.btn-edit', function () {
    let id = $(this).data('id');

    resetForm();
    mode = 'edit';

    $.get("{{ url('gudang/bahan-baku-masuk') }}/" + id, function (res) {

        $('#edit_id').val(res.id);
        $('#method').val('PUT');

        // isi header
       $('input[name="tanggal_transaksi"]').val(
    res.tanggal_transaksi.replace(' ', 'T').slice(0,16)
);
        $('select[name="id_toko"]').val(res.id_toko);
        $('input[name="no_nota"]').val(res.no_nota);

        // isi detail
        res.detail.forEach(function (d) {
            $('#btnAddRow').click();
            let idx = rowIndex - 1;

            $(`select[name="items[${idx}][id_bahan_baku]"]`).val(d.id_bahan_baku);
            $(`input[name="items[${idx}][jumlah]"]`).val(d.jumlah);
            $(`input[name="items[${idx}][nominal]"]`).val(d.nominal);
            $(`input[name="items[${idx}][satuan]"]`).val(d.satuan);
        });

        $('.modal-title').text('Edit Bahan Baku Masuk');
        $('#modalBahanMasuk').modal('show');
    });
});
$(document).on('click', '.btn-detail', function () {
    let id = $(this).data('id');

    $.get(`/gudang/bahan-baku-masuk/detail/${id}`, function (res) {
        console.log(res)
        $('#d_id').text(res.id);
        $('#d_tanggal').text(res.tanggal);
        if (res.nominal) {
            $('#d_nominal').text(
                'Rp ' + Number(res.nominal).toLocaleString('id-ID')
            );
        } else {
            $('#d_nominal').text('-');
        }

        let rows = '';
        res.detail.forEach((d, i) => {
            rows += `
            <tr>
                <td>${i+1}</td>
                <td>${d.nama}</td>
                <td>${d.jumlah}</td>
                <td>${d.satuan}</td>
            </tr>`;
        });

        $('#detail_items').html(rows);
        $('#modalDetailBahanMasuk').modal('show'); 
    });
});
</script>
@endpush
