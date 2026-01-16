@extends('layouts.app')

@push('title')
    Daftar Supplier
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 text-gray-800">Daftar Pengaturan Sistem</h1>
        </div>

        <!-- Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Supplier</h6>
                <button type="button" class="btn btn-sm btn-primary" id="btnModalTambah">
                    <i class="fas fa-plus fa-sm"></i> Tambah Konfigurasi
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel-pengaturan-sistem" class="table table-bordered table-hover" width="100%" cellspacing="1">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>ID Konfigurasi</th>
                                <th>Ketentuan</th>
                                <th>Keterangan</th>
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
    <div class="modal fade" id="modal-tambah-pengaturanSistem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="form-tambah-pengaturanSistem" method="POST" action="{{ route('master.simpanPengaturanSistem') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Konfigurasi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow mb-4">
                            <div class="card-body">

                                {{-- ID Supplier (preview, readonly) --}}
                                <div class="form-group">
                                    <label for="id">ID Konfigurasi</label>
                                    <input type="text" name="id" id="id" value="" class="form-control"
                                        maxlength="20" readonly>
                                </div>

                                {{-- Nama Toko --}}
                                <div class="form-group">
                                    <label for="ketentuan">Ketentuan</label>
                                    <input type="text" name="ketentuan" id="ketentuan" value="{{ old('ketentuan') }}"
                                        class="form-control @error('nama') is-invalid @enderror" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- keterangan --}}
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
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
    <div class="modal fade" id="modal-edit-pengaturanSistem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="form-pengaturanSistem" method="POST" action="" enctype="multipart/form-data">
                @csrf
                {{-- action akan di-set oleh JS (update route dengan {id}) --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Pengaturan Sistem</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow mb-4">
                            <div class="card-body">

                                {{-- ID Supplier --}}
                                <div class="form-group">
                                    <label for="edit_id">ID Konfigurasi</label>
                                    <input type="text" name="id" id="edit_id" value=""
                                        class="form-control" maxlength="20" readonly>
                                </div>

                                {{-- Nama Toko --}}
                                <div class="form-group">
                                    <label for="edit_ketentuan">Ketentuan</label>
                                    <input type="text" name="ketentuan" id="edit_ketentuan"
                                        class="form-control @error('ketentuan') is-invalid @enderror" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- keterangan --}}
                                <div class="form-group">
                                    <label for="edit_keterangan">Keterangan</label>
                                    <textarea name="keterangan" id="edit_keterangan" class="form-control @error('keterangan') is-invalid @enderror"></textarea>
                                    @error('keterangan')
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
            let table = $('#tabel-pengaturan-sistem').DataTable({
                ajax: {
                    url: "{{ route('master.dataPengaturanSistem') }}",
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'ketentuan'
                    },
                    {
                        data: 'keterangan'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                    <button class="btn btn-warning btn-sm btn-edit"
                        data-id="${row.id}"
                        data-ketentuan="${row.ketentuan || ''}"
                        data-keterangan="${row.keterangan || ''}">
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
                $('#form-tambah-pengaturanSistem')[0].reset();
                $('#modal-tambah-pengaturanSistem').modal('show');

                $('#form-tambah-pengaturanSistem')[0].reset();

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
                let idBaru = 'KFG-' + newNum.toString().padStart(3, '0');

                // Set ke input
                $('#id').val(idBaru);

            });

            // SUBMIT FORM TAMBAH via AJAX
            $('#form-tambah-pengaturanSistem').on('submit', function(e) {
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
                        $('#modal-tambah-pengaturanSistem').modal('hide');
                        form.reset();
                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Pengaturan Sistem berhasil ditambahkan.',
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
            $('#tabel-pengaturan-sistem').on('click', '.btn-edit', function() {
                let btn = $(this);
                let id = btn.data('id');

                // Set URL action form ke route update (yang pakai {id})
                let url = "{{ route('master.updatePengaturanSistem', ':id') }}".replace(':id', id);
                $('#form-pengaturanSistem').attr('action', url);

                // Isi semua field dari data-*
                $('#edit_id').val(id);
                $('#edit_ketentuan').val(btn.data('ketentuan'));
                $('#edit_keterangan').val(btn.data('keterangan'));

                $('#modal-edit-pengaturanSistem').modal('show');
            });
            //Edit simpan
            $('#form-pengaturanSistem').on('submit', function(e) {
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
                        $('#modal-edit-pengaturanSistem').modal('hide');
                        table.ajax.reload(null, false); // refresh data

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Data Pengaturan Sistem berhasil diperbarui.',
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
            $('#tabel-pengaturan-sistem').on('click', '.btn-hapus', function(e) {
                e.preventDefault();

                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data Pengaturan Sistem akan dihapus permanen.',
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
                        url: "{{ route('master.hapusPengaturanSistem', ':id') }}".replace(':id', id),
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
                                    'Pengaturan Sistem berhasil dihapus.',
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
