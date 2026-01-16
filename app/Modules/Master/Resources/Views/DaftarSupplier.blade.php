@extends('layouts.app')

@push('title')
    Daftar Supplier
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 text-gray-800">Daftar Supplier</h1>
        </div>

        <!-- Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Supplier</h6>
                <button type="button" class="btn btn-sm btn-primary" id="btnModalTambah">
                    <i class="fas fa-plus fa-sm"></i> Tambah Supplier
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel-supplier" class="table table-bordered table-hover" width="100%" cellspacing="1">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>ID Supplier</th>
                                <th>Toko</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        {{-- <tbody> di-generate DataTables lewat AJAX --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL TAMBAH Toko --}}
    <div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formTambahSupplier" method="POST" action="{{ route('master.simpanSupplier') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow mb-4">
                            <div class="card-body">

                                {{-- ID Supplier (preview, readonly) --}}
                                <div class="form-group">
                                    <label for="id">ID Supplier</label>
                                    <input type="text" name="id" id="id" value="" class="form-control"
                                        maxlength="20" readonly>
                                </div>

                                {{-- Nama Toko --}}
                                <div class="form-group">
                                    <label for="nama_toko">Nama Toko</label>
                                    <input type="text" name="nama_toko" id="nama_toko" value="{{ old('nama_toko') }}"
                                        class="form-control @error('nama') is-invalid @enderror" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Alamat --}}
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div> {{-- end modal-body --}}

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT Supplier --}}
    <div class="modal fade" id="modalEditSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formEditSupplier" method="POST" action="" enctype="multipart/form-data">
                @csrf
                {{-- action akan di-set oleh JS (update route dengan {id}) --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow mb-4">
                            <div class="card-body">

                                {{-- ID Supplier --}}
                                <div class="form-group">
                                    <label for="edit_id">ID Supplier</label>
                                    <input type="text" name="id" id="edit_id" value=""
                                        class="form-control" maxlength="20" readonly>
                                </div>

                                {{-- Nama Toko --}}
                                <div class="form-group">
                                    <label for="edit_nama_toko">Nama Toko</label>
                                    <input type="text" name="nama_toko" id="edit_nama_toko"
                                        class="form-control @error('nama') is-invalid @enderror" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Alamat --}}
                                <div class="form-group">
                                    <label for="edit_alamat">Alamat</label>
                                    <textarea name="alamat" id="edit_alamat" class="form-control @error('alamat') is-invalid @enderror"></textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div> {{-- end modal-body --}}

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // 1. DataTable AJAX
            let table = $('#tabel-supplier').DataTable({
                ajax: {
                    url: "{{ route('master.dataSupplier') }}",
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'nama_toko'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                    <button class="btn btn-warning btn-sm btn-edit"
                        data-id="${row.id}"
                        data-nama_toko="${row.nama_toko || ''}"
                        data-alamat="${row.alamat || ''}">
                        <i class="fas fa-edit"></i>
                    </button>

                    <button class="btn btn-danger btn-sm btn-hapus"
                        data-id="${row.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                        }
                    }
                ]
            });



            // Tombol TAMBAH buka modal
            $('#btnModalTambah').on('click', function() {
                $('#formTambahSupplier')[0].reset();
                $('#modalTambahSupplier').modal('show');

                $('#formTambahSupplier')[0].reset();

                // Ambil semua data yang sudah di-load DataTables
                let allData = table.rows().data().toArray();

                // Cari ID terbesar
                let maxId = 0;
                allData.forEach(row => {
                    let num = parseInt(row.id.substring(4)); // ambil angka setelah CUST
                    if (num > maxId) {
                        maxId = num;
                    }
                });

                // Buat ID baru
                let newNum = maxId + 1;
                let idBaru = 'SPL-' + newNum.toString().padStart(3, '0');

                // Set ke input
                $('#id').val(idBaru);

            });

            // SUBMIT FORM TAMBAH via AJAX
            $('#formTambahSupplier').on('submit', function(e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#modalTambahSupplier').modal('hide');
                        form.reset();
                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Supplier berhasil ditambahkan.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON?.errors || {};
                            let messages = [];
                            Object.keys(errors).forEach(function(field) {
                                messages.push(errors[field][0]);
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi gagal',
                                html: messages.join('<br>'),
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menyimpan data.',
                            });
                        }
                    }
                });
            });

            // Edut Modal
            $('#tabel-supplier').on('click', '.btn-edit', function() {
                let btn = $(this);
                let id = btn.data('id');

                // Set URL action form ke route update (yang pakai {id})
                let url = "{{ route('master.updateSupplier', ':id') }}".replace(':id', id);
                $('#formEditSupplier').attr('action', url);

                // Isi semua field dari data-*
                $('#edit_id').val(id);
                $('#edit_nama_toko').val(btn.data('nama_toko'));
                $('#edit_alamat').val(btn.data('alamat'));

                $('#modalEditSupplier').modal('show');
            });
            //Edit simpan
            $('#formEditSupplier').on('submit', function(e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST', // tetap POST, karena kita pakai _method=PUT
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#modalEditSupplier').modal('hide');
                        table.ajax.reload(null, false); // refresh data

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Data Supplier berhasil diperbarui.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON?.errors || {};
                            let messages = [];
                            Object.keys(errors).forEach(function(field) {
                                messages.push(errors[field][0]);
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi gagal',
                                html: messages.join('<br>'),
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menyimpan data.',
                            });
                        }
                    }
                });
            });


            // HAPUS
            $('#tabel-supplier').on('click', '.btn-hapus', function(e) {
                e.preventDefault();

                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data Supllier akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    // kalau user batal → stop
                    if (!result.isConfirmed) {
                        return;
                    }

                    // kalau user klik "Ya, hapus" → eksekusi AJAX delete
                    $.ajax({
                        url: "{{ route('master.hapusSupplier', ':id') }}".replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            // refresh DataTables tanpa reset halaman
                            table.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: res.message ||
                                    'Supplier berhasil dihapus.',
                                timer: 1800,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menghapus data.'
                            });
                        }
                    });
                });
            });

        });
    </script>
@endpush
