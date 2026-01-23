@extends('layouts.app')

@push('title')
    Daftar Toko
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 text-gray-800">Daftar Toko</h1>
        </div>

        <!-- Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Toko</h6>
                <button type="button" class="btn btn-sm btn-primary" id="btnModalTambah">
                    <i class="fas fa-plus fa-sm"></i> Tambah Toko
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel-toko" class="table table-bordered table-hover" width="130%" cellspacing="1">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>ID Toko</th>
                                <th>Sales</th>
                                <th>Pelanggan</th>
                                <th>Nama Toko</th>
                                <th>Alamat</th>
                                <th>Lintang</th>
                                <th>Bujur</th>
                                <th>Pembuat</th>
                                <th>Pengubah</th>
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
    <div class="modal fade" id="modalTambahToko" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formTambahToko" method="POST" action="{{ route('master.simpanToko') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Toko</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow mb-4">
                            <div class="card-body">

                                {{-- ID Toko (preview, readonly) --}}
                                <div class="form-group">
                                    <label for="id">ID Toko</label>
                                    <input type="text" name="id" id="id" value="" class="form-control"
                                        maxlength="20" readonly>
                                </div>

                                {{-- Sales (select) --}}
                                <div class="form-group">
                                    <label for="id_karyawan">Sales Pembawa</label>
                                    <select name="id_karyawan" id="id_karyawan" class="form-control" required>
                                        <option value="">-- Pilih Sales --</option>
                                        @foreach ($sales as $j)
                                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- CS / Pelanggan (select) --}}
                                <div class="form-group">
                                    <label for="id_pelanggan">CP Toko</label>
                                    <select name="id_pelanggan" id="id_pelanggan" class="form-control" required>
                                        <option value="">-- Pilih Pelanggan --</option>
                                        @foreach ($pelanggan as $j)
                                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                        @endforeach
                                        {{-- opsi akan diisi oleh JS dari response 'pelanggan' --}}
                                    </select>
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

                                {{-- No HP --}}
                                {{-- <div class="form-group">
                                <label for="no_hp">No. HP (kontak)</label>
                                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" class="form-control @error('no_hp') is-invalid @enderror">
                                @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div> --}}

                                {{-- Alamat --}}
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Lintang --}}
                                <div class="form-group">
                                    <label for="lintang">Lintang</label>
                                    <input type="text" name="lintang" id="lintang" value="{{ old('lintang') }}"
                                        class="form-control @error('lintang') is-invalid @enderror">
                                    @error('lintang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Bujur --}}
                                <div class="form-group">
                                    <label for="bujur">Bujur</label>
                                    <input type="text" name="bujur" id="bujur" value="{{ old('bujur') }}"
                                        class="form-control @error('bujur') is-invalid @enderror">
                                    @error('bujur')
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

    {{-- MODAL EDIT Toko --}}
    <div class="modal fade" id="modalEditToko" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formEditToko" method="POST" action="" enctype="multipart/form-data">
                @csrf
                {{-- action akan di-set oleh JS (update route dengan {id}) --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Toko</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow mb-4">
                            <div class="card-body">

                                {{-- ID Toko --}}
                                <div class="form-group">
                                    <label for="edit_id">ID Toko</label>
                                    <input type="text" name="id" id="edit_id" value=""
                                        class="form-control" maxlength="20" readonly>
                                </div>

                                {{-- Sales (select) --}}
                                <div class="form-group">
                                    <label for="edit_id_karyawan">Sales Pembawa</label>
                                    <select name="id_karyawan" id="edit_id_karyawan" class="form-control" required>
                                        @foreach ($sales as $j)
                                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- CS / Pelanggan (select) --}}
                                <div class="form-group">
                                    <label for="edit_id_pelanggan">CS Toko (Pelanggan)</label>
                                    <select name="id_pelanggan" id="edit_id_pelanggan" class="form-control" required>
                                        @foreach ($pelanggan as $j)
                                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                        @endforeach
                                    </select>
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

                                {{-- Lintang --}}
                                <div class="form-group">
                                    <label for="edit_lintang">Lintang</label>
                                    <input type="text" name="lintang" id="edit_lintang"
                                        class="form-control @error('lintang') is-invalid @enderror">
                                    @error('lintang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Bujur --}}
                                <div class="form-group">
                                    <label for="edit_bujur">Bujur</label>
                                    <input type="text" name="bujur" id="edit_bujur"
                                        class="form-control @error('bujur') is-invalid @enderror">
                                    @error('bujur')
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
            let table = $('#tabel-toko').DataTable({
                ajax: {
                    url: "{{ route('master.dataToko') }}",
                    dataSrc: function(json) {
                        window._tokoMeta = {
                            sales: json.sales || [],
                            pelanggan: json.pelanggan || []
                        };
                        return json.data || [];
                    }
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'nama_karyawan'
                    },
                    {
                        data: 'nama_pelanggan'
                    },
                    {
                        data: 'nama_toko'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'lintang'
                    },
                    {
                        data: 'bujur'
                    },
                    {
                        data: 'nama_pembuat'
                    },
                    {
                        data: 'nama_pengubah'
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
                        data-alamat="${row.alamat || ''}"
                        data-lintang="${row.lintang || ''}"
                        data-bujur="${row.bujur || ''}"
                        data-id_karyawan="${row.id_karyawan || ''}"
                        data-id_pelanggan="${row.id_pelanggan || ''}">
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
                $('#formTambahToko')[0].reset();
                $('#modalTambahToko').modal('show');

                $('#formTambahToko')[0].reset();

                // Ambil semua data yang sudah di-load DataTables
                let allData = table.rows().data().toArray();

                // Cari ID terbesar
                let maxId = 0;
                allData.forEach(row => {
                    let num = parseInt(row.id.substring(5)); // ambil angka setelah CUST
                    if (num > maxId) {
                        maxId = num;
                    }
                });

                // Buat ID baru
                let newNum = maxId + 1;
                let idBaru = 'TOKO-' + newNum.toString().padStart(3, '0');

                // Set ke input
                $('#id').val(idBaru);

            });

            // SUBMIT FORM TAMBAH via AJAX
            $('#formTambahToko').on('submit', function(e) {
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
                        $('#modalTambahToko').modal('hide');
                        form.reset();
                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Toko berhasil ditambahkan.',
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
            $('#tabel-toko').on('click', '.btn-edit', function() {
                let btn = $(this);
                let id = btn.data('id');

                // Set URL action form ke route update (yang pakai {id})
                let url = "{{ route('master.updateToko', ':id') }}".replace(':id', id);
                $('#formEditToko').attr('action', url);

                // Isi semua field dari data-*
                $('#edit_id').val(id);
                $('#edit_nama_toko').val(btn.data('nama_toko'));
                $('#edit_alamat').val(btn.data('alamat'));
                $('#edit_lintang').val(btn.data('lintang'));
                $('#edit_bujur').val(btn.data('bujur'));


                $('#modalEditToko').modal('show');
            });
            //Edit simpan
            $('#formEditToko').on('submit', function(e) {
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
                        $('#modalEditToko').modal('hide');
                        table.ajax.reload(null, false); // refresh data

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Data Toko berhasil diperbarui.',
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
            $('#tabel-toko').on('click', '.btn-hapus', function(e) {
                e.preventDefault();

                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data Toko akan dihapus permanen.',
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
                        url: "{{ route('master.hapusToko', ':id') }}".replace(':id', id),
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
                                    'Toko berhasil dihapus.',
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
