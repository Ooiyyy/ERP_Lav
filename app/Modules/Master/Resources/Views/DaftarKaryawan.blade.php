@extends('layouts.app')

@push('title')
    Daftar Karyawan
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 text-gray-800">Daftar Karyawan</h1>
        </div>

        <!-- Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Karyawan</h6>
                <button type="button" class="btn btn-sm btn-primary" id="btnModalTambah">
                    <i class="fas fa-plus fa-sm"></i> Tambah Karyawan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel-karyawan" class="table table-bordered table-hover" width="130%" cellspacing="1">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>ID</th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Alamat</th>
                                <th>Jenis Kelamin</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th>Tempat Lahir</th>
                                <th>Tanggal Lahir</th>
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

    {{-- MODAL TAMBAH KARYAWAN --}}
    <div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formTambahKaryawan" method="POST" action="{{ route('master.simpanKaryawan') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Karyawan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow mb-4">
                            <div class="card-body">

                                {{-- Foto --}}
                                <div class="form-group">
                                    <label for="foto">Foto (JPG maks 1MB)</label>
                                    <input type="file" name="foto" id="foto" accept="image/*"
                                        class="form-control @error('foto') is-invalid @enderror">
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <small id="foto-error" class="text-danger d-none"></small>

                                    <div class="mt-2">
                                        <img id="foto-preview" src="#" alt="Preview Foto"
                                            class="img-thumbnail d-none" style="max-width: 150px;">
                                    </div>
                                </div>

                                {{-- ID Karyawan --}}
                                <div class="form-group">
                                    <label for="id">ID Karyawan</label>
                                    <input type="text" name="id" id="id" value="" class="form-control"
                                        maxlength="20" readonly>
                                </div>

                                {{-- Nama Karyawan --}}
                                <div class="form-group">
                                    <label for="nama">Nama Karyawan</label>
                                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                                        class="form-control @error('nama') is-invalid @enderror" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jabatan --}}
                                <div class="form-group">
                                    <label>Jabatan</label>
                                    <select name="id_jabatan" class="form-control @error('id_jabatan') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach ($jabatan as $j)
                                            <option value="{{ $j->id }}"
                                                {{ old('id_jabatan') == $j->id ? 'selected' : '' }}>
                                                {{ $j->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Alamat --}}
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin"
                                        class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="Laki-laki"
                                            {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                            Laki-laki
                                        </option>
                                        <option value="Perempuan"
                                            {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                            Perempuan
                                        </option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- No HP --}}
                                <div class="form-group">
                                    <label>No. HP</label>
                                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                        class="form-control @error('no_hp') is-invalid @enderror" required>
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Y" {{ old('status') == 'Y' ? 'selected' : '' }}>Aktif</option>
                                        <option value="N" {{ old('status') == 'N' ? 'selected' : '' }}>Nonaktif
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tempat Lahir --}}
                                <div class="form-group">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                                        class="form-control @error('tempat_lahir') is-invalid @enderror" required>
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                        class="form-control @error('tanggal_lahir') is-invalid @enderror" required>
                                    @error('tanggal_lahir')
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
    {{-- MODAL EDIT KARYAWAN --}}
    <div class="modal fade" id="modalEditKaryawan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formEditKaryawan" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Karyawan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow mb-4">
                            <div class="card-body">

                                {{-- Foto --}}
                                <div class="form-group">
                                    <label for="edit_foto">Foto (JPG maks 1MB)</label>
                                    <input type="file" name="foto" id="edit_foto" accept="image/*"
                                        class="form-control @error('foto') is-invalid @enderror">
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <small id="edit-foto-error" class="text-danger d-none"></small>

                                    <div class="mt-2">
                                        <img id="edit-foto-preview" src="#" alt="Preview Foto"
                                            class="img-thumbnail d-none" style="max-width: 150px;">
                                    </div>
                                </div>

                                {{-- ID Karyawan --}}
                                <div class="form-group">
                                    <label for="edit_id">ID Karyawan</label>
                                    <input type="text" name="id" id="edit_id" value=""
                                        class="form-control" maxlength="20" readonly>
                                </div>

                                {{-- Nama Karyawan --}}
                                <div class="form-group">
                                    <label for="edit_nama">Nama Karyawan</label>
                                    <input type="text" name="nama" id="edit_nama"
                                        class="form-control @error('nama') is-invalid @enderror" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jabatan --}}
                                <div class="form-group">
                                    <label for="edit_id_jabatan">Jabatan</label>
                                    <select name="id_jabatan" id="edit_id_jabatan"
                                        class="form-control @error('id_jabatan') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach ($jabatan as $j)
                                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Alamat --}}
                                <div class="form-group">
                                    <label for="edit_alamat">Alamat</label>
                                    <textarea name="alamat" id="edit_alamat" class="form-control @error('alamat') is-invalid @enderror" required></textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div class="form-group">
                                    <label for="edit_jenis_kelamin">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="edit_jenis_kelamin"
                                        class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- No HP --}}
                                <div class="form-group">
                                    <label for="edit_no_hp">No. HP</label>
                                    <input type="text" name="no_hp" id="edit_no_hp"
                                        class="form-control @error('no_hp') is-invalid @enderror" required>
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="form-group">
                                    <label for="edit_status">Status</label>
                                    <select name="status" id="edit_status"
                                        class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Y">Aktif</option>
                                        <option value="N">Nonaktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tempat Lahir --}}
                                <div class="form-group">
                                    <label for="edit_tempat_lahir">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="edit_tempat_lahir"
                                        class="form-control @error('tempat_lahir') is-invalid @enderror" required>
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div class="form-group">
                                    <label for="edit_tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir"
                                        class="form-control @error('tanggal_lahir') is-invalid @enderror" required>
                                    @error('tanggal_lahir')
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
            // preview & validasi foto
            const $inputFoto = $('#foto');
            const $errorText = $('#foto-error');
            const $previewImg = $('#foto-preview');
            const maxSizeBytes = 1 * 1024 * 1024; // 1 MB

            $inputFoto.on('change', function() {
                $errorText.addClass('d-none').text('');
                $previewImg.addClass('d-none').attr('src', '#');

                const file = this.files[0];
                if (!file) return;

                const fileName = file.name;
                const fileSize = file.size;
                const parts = fileName.split('.');
                const ext = parts.length > 1 ? parts.pop().toLowerCase() : '';

                if (ext !== 'jpg' && ext !== 'jpeg') {
                    $errorText.text('Format foto harus JPG.').removeClass('d-none');
                    $inputFoto.val('');
                    return;
                }

                if (fileSize > maxSizeBytes) {
                    $errorText.text('Ukuran foto maksimal 1MB.').removeClass('d-none');
                    $inputFoto.val('');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $previewImg.attr('src', e.target.result).removeClass('d-none');
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // 1. DataTable AJAX
            let table = $('#tabel-karyawan').DataTable({
                processing: true,
                ajax: "{{ route('master.karyawan.data') }}",
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            return `<img src="/storage/foto_karyawan/${id}.jpg"
                                         width="55" height="55" style="object-fit:cover;" />`;
                        }
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'jabatan'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'jenis_kelamin'
                    },
                    {
                        data: 'no_hp'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'tempat_lahir'
                    },
                    {
                        data: 'tanggal_lahir'
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
                                <button
                                    class="btn btn-warning btn-sm btn-edit"
                                    data-id="${row.id}"
                                    data-nama="${row.nama ?? ''}"
                                    data-id_jabatan="${row.id_jabatan ?? ''}"
                                    data-alamat="${row.alamat ?? ''}"
                                    data-jenis_kelamin="${row.jenis_kelamin ?? ''}"
                                    data-no_hp="${row.no_hp ?? ''}"
                                    data-status="${row.status ?? ''}"
                                    data-tempat_lahir="${row.tempat_lahir ?? ''}"
                                    data-tanggal_lahir="${row.tanggal_lahir ?? ''}"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    class="btn btn-danger btn-sm btn-hapus"
                                    data-id="${row.id}"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });

            // Tombol TAMBAH buka modal
            $('#btnModalTambah').on('click', function() {
                $('#formTambahKaryawan')[0].reset();
                $('#foto-error').addClass('d-none').text('');
                $('#foto-preview').addClass('d-none').attr('src', '#');
                $('#modalTambahKaryawan').modal('show');

                $('#formTambahKaryawan')[0].reset();

                // Ambil semua data yang sudah di-load DataTables
                let allData = table.rows().data().toArray();

                // Cari ID terbesar
                let maxId = 0;
                allData.forEach(row => {
                    let num = parseInt(row.id.substring(1)); // ambil angka setelah K
                    if (num > maxId) {
                        maxId = num;
                    }
                });

                // Buat ID baru
                let newNum = maxId + 1;
                let idBaru = 'K' + newNum.toString().padStart(3, '0');

                // Set ke input
                $('#id').val(idBaru);
            });

            // SUBMIT FORM TAMBAH via AJAX
            $('#formTambahKaryawan').on('submit', function(e) {
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
                        $('#modalTambahKaryawan').modal('hide');
                        form.reset();
                        $('#foto-preview').addClass('d-none').attr('src', '#');
                        $('#foto-error').addClass('d-none').text('');
                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Karyawan berhasil ditambahkan.',
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
            $('#tabel-karyawan').on('click', '.btn-edit', function() {
                let btn = $(this);
                let id = btn.data('id');

                // Set URL action form ke route update (yang pakai {id})
                let url = "{{ route('master.simpanUpdateKaryawan', ':id') }}".replace(':id', id);
                $('#formEditKaryawan').attr('action', url);

                // Isi semua field dari data-*
                $('#edit_id').val(id);
                $('#edit_nama').val(btn.data('nama'));
                $('#edit_id_jabatan').val(btn.data('id_jabatan'));
                $('#edit_alamat').val(btn.data('alamat'));
                $('#edit_jenis_kelamin').val(btn.data('jenis_kelamin'));
                $('#edit_no_hp').val(btn.data('no_hp'));
                $('#edit_status').val(btn.data('status'));
                $('#edit_tempat_lahir').val(btn.data('tempat_lahir'));
                $('#edit_tanggal_lahir').val(btn.data('tanggal_lahir'));

                // Optional: set preview foto lama
                $('#edit-foto-preview')
                    .attr('src', `/storage/foto_karyawan/${id}.jpg`)
                    .removeClass('d-none');

                $('#modalEditKaryawan').modal('show');
            });
            //Edit simpan
            $('#formEditKaryawan').on('submit', function(e) {
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
                        $('#modalEditKaryawan').modal('hide');
                        table.ajax.reload(null, false); // refresh data

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Data karyawan berhasil diperbarui.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        // mirip handler error yang tambah
                    }
                });
            });


            // HAPUS
            $('#tabel-karyawan').on('click', '.btn-hapus', function(e) {
                e.preventDefault();

                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data karyawan akan dihapus permanen.',
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
                        url: "{{ route('master.hapusKaryawan', ':id') }}".replace(':id', id),
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
                                    'Karyawan berhasil dihapus.',
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
