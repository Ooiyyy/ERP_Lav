@extends('layouts.app')

@push('title')
    Daftar Akun
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-gray-800">Daftar Akun</h4>
            <button id="btnModalTambah" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Akun</button>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel-akun" class="table table-bordered table-hover" style="width:100%">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>ID Karyawan</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Status Login</th>
                                <th>Status Akun</th>
                                <th>Pembuat</th>
                                <th>Pengubah</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambah" method="POST"
                action="{{ Route::has('master.simpanAkun') ? route('master.simpanAkun') : url('/akun/simpanakun') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Akun</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Id Karyawan</label>
                            <select name="id_karyawan" class="form-control @error('id_karyawan') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawanTanpaAkun as $j)
                                    <option value="{{ $j->id }}"
                                        {{ old('id_karyawan') == $j->id ? 'selected' : '' }}>
                                        {{ $j->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_karyawan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="t_username">Username</label>
                            <input type="text" id="t_username" name="username" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="t_password">Password</label>
                            <input type="password" id="t_password" name="password" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button id="btnSimpanTambah" type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEdit" method="POST" action="">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Akun</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="e_id_karyawan" name="id_karyawan">

                        <div class="form-group">
                            <label for="e_username">Username</label>
                            <input type="text" id="e_username" name="username" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="e_password">Password (kosong = tidak diubah)</label>
                            <input type="password" id="e_password" name="password" class="form-control">
                        </div>

                        {{-- <div class="form-group">
                        <label for="e_status">Status Login</label>
                        <select id="e_status" name="status_login" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div> --}}
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button id="btnSimpanEdit" type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // endpoints (route or fallback)
            const urlData = {!! \Illuminate\Support\Js::from(
                Route::has('master.dataAkun') ? route('master.dataAkun') : url('/master/dataakun'),
            ) !!};
            const urlStore = {!! \Illuminate\Support\Js::from(
                Route::has('master.simpanAkun') ? route('master.simpanAkun') : url('/master/simpanakun'),
            ) !!};
            const urlUpdateTemplate = {!! \Illuminate\Support\Js::from(
                Route::has('master.updateAkun') ? route('master.updateAkun', ['id' => ':id']) : url('/master/updateakun/:id'),
            ) !!};
            const urlToggleTemplate = {!! \Illuminate\Support\Js::from(
                Route::has('master.toggleAkun') ? route('master.toggleAkun', ['id' => ':id']) : url('/master/toggleakun/:id'),
            ) !!};
            const urlDeleteTemplate = {!! \Illuminate\Support\Js::from(
                Route::has('master.hapusAkun') ? route('master.hapusAkun', ['id' => ':id']) : url('/master/akun/:id'),
            ) !!};

            if (typeof $.fn.DataTable === 'undefined') {
                $('.container-fluid').first().prepend(
                    '<div class="alert alert-warning">DataTables belum ter-load. Periksa layout.</div>');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            function esc(s) {
                if (s === null || s === undefined) return '';
                return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g,
                    '&lt;').replace(/>/g, '&gt;');
            }

            function blurActive() {
                try {
                    if (document.activeElement) document.activeElement.blur();
                } catch (e) {}
            }

            function reloadTable() {
                if (window.tableAkun && window.tableAkun.ajax) window.tableAkun.ajax.reload(null, false);
                else location.reload();
            }

            // init DataTable
            window.tableAkun = $('#tabel-akun').DataTable({
                ajax: {
                    url: urlData,
                    dataSrc: 'data'
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1,
                        width: '40px'
                    },
                    {
                        data: 'id_karyawan',
                        width: '110px'
                    },
                    {
                        data: 'username',
                        defaultContent: ''
                    },
                    {
                        data: 'password',
                        defaultContent: ''
                    },
                    {
                        data: 'status_login',
                        width: '90px',
                        defaultContent: ''
                    },
                    {
                        data: null,
                        width: '120px',
                        orderable: false,
                        searchable: false,
                        render: function(row) {
                            const id = esc(row.id_karyawan);
                            // status from row.karyawan.status or row.status_akun (server must provide one)
                            const status = (row.karyawan && row.karyawan.status) ? row.karyawan
                                .status : (row.status_akun ?? '');
                            const checked = status === 'Y' ? 'checked' : '';
                            return `<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input status-toggle" id="sw-${id}" data-id="${id}" ${checked}>
                        <label class="custom-control-label" for="sw-${id}">${esc(status)}</label>
                    </div>`;
                        }
                    },
                    {
                        data: 'nama_pembuat',
                        defaultContent: '-'
                    },
                    {
                        data: 'nama_pengubah',
                        defaultContent: '-'
                    },
                    {
                        data: 'tanggal_buat',
                        width: '160px',
                        defaultContent: '-'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        width: '130px',
                        render: function(row) {
                            const id = esc(row.id_karyawan);
                            return `
                        <button class="btn btn-warning btn-sm btn-edit" data-id="${id}" data-username="${esc(row.username)}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-hapus" data-id="${id}" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                        }
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                responsive: true,
                language: {
                    emptyTable: "Belum ada akun."
                }
            });

            // show add modal
            $('#btnModalTambah').on('click', function() {
                blurActive();
                $('#formTambah')[0].reset();
                $('#modalTambah').modal('show');
            });

            // submit add
            $('#formTambah').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSimpanTambah');
                $btn.prop('disabled', true).text('Menyimpan...');
                const fd = new FormData(this);
                $.ajax({
                    url: urlStore,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false
                }).done(function(res) {
                    $('#modalTambah').modal('hide');
                    $('#formTambah')[0].reset();
                    reloadTable();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Tersimpan',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }).fail(function(xhr) {
                    if (xhr.status === 422) {
                        const html = Object.values(xhr.responseJSON.errors || {}).map(x => x[0])
                            .join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi',
                            html
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal menyimpan'
                        });
                    }
                }).always(function() {
                    $btn.prop('disabled', false).text('Simpan');
                });
            });

            // open edit modal
            $('#tabel-akun').on('click', '.btn-edit', function() {
                blurActive();
                const $b = $(this);
                const id = $b.data('id');
                $('#e_id_karyawan').val(id);
                $('#e_username').val($b.data('username') || '');
                $('#e_password').val('');
                const url = urlUpdateTemplate.replace(':id', encodeURIComponent(id));
                $('#formEdit').attr('action', url);
                $('#modalEdit').modal('show');
            });

            // submit edit
            $('#formEdit').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSimpanEdit');
                $btn.prop('disabled', true).text('Menyimpan...');
                const fd = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false
                }).done(function(res) {
                    $('#modalEdit').modal('hide');
                    $('#formEdit')[0].reset();
                    reloadTable();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Diperbarui',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }).fail(function(xhr) {
                    if (xhr.status === 422) {
                        const html = Object.values(xhr.responseJSON.errors || {}).map(x => x[0])
                            .join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi',
                            html
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal update'
                        });
                    }
                }).always(function() {
                    $btn.prop('disabled', false).text('Simpan Perubahan');
                });
            });

            // toggle account status (Y/N)
            $('#tabel-akun').on('change', '.status-toggle', function() {
                const $ch = $(this);
                const id = $ch.data('id');
                const status = $ch.prop('checked') ? 'Y' : 'N';

                const me = "{{ auth('web')->id() ?? '' }}";
                if (String(me) === String(id)) {
                    $ch.prop('checked', !$ch.prop('checked'));
                    return Swal.fire({
                        icon: 'error',
                        title: 'Dilarang',
                        text: 'Tidak boleh mengubah status karyawan sendiri.'
                    });
                }

                $ch.prop('disabled', true);
                const url = urlToggleTemplate.replace(':id', encodeURIComponent(id));
                $.post(url, {
                        status: status
                    })
                    .done(function(res) {
                        const lbl = $(`#sw-${id}`).closest('.custom-control').find(
                            '.custom-control-label');
                        if (lbl.length) lbl.text(status);
                        reloadTable();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Status diperbarui',
                            timer: 900,
                            showConfirmButton: false
                        });
                    })
                    .fail(function(xhr) {
                        $ch.prop('checked', !$ch.prop('checked'));
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal mengubah status'
                        });
                    })
                    .always(function() {
                        $ch.prop('disabled', false);
                    });
            });

            // delete
            $('#tabel-akun').on('click', '.btn-hapus', function() {
                const id = $(this).data('id');
                if (!id) return;
                const me = "{{ auth('web')->id() ?? '' }}";
                if (String(me) === String(id)) {
                    return Swal.fire({
                        icon: 'error',
                        title: 'Dilarang',
                        text: 'Tidak boleh menghapus akun sendiri.'
                    });
                }

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Akun akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    const url = urlDeleteTemplate.replace(':id', encodeURIComponent(id));
                    $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            }
                        })
                        .done(function(res) {
                            reloadTable();
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: res.message || 'Berhasil',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        })
                        .fail(function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Gagal hapus'
                            });
                        });
                });
            });

        }); // end dom ready
    </script>
@endpush
