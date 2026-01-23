@extends('layouts.app')

@push('title')
    Daftar Bahan Baku
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 text-gray-800">Daftar Bahan Baku</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Bahan Baku</h6>
                <button type="button" class="btn btn-sm btn-primary" id="btnModalTambah">
                    <i class="fas fa-plus fa-sm"></i> Tambah
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel-bahan" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>ID Bahan Baku</th>
                                <th>Nama</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Deskripsi</th>
                                <th>Pembuat</th>
                                <th>Pengubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambah" method="POST" action="{{ route('master.simpanbahan-baku') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Bahan Baku</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="id_bahan">ID Bahan Baku</label>
                            <input type="text" name="id" id="id_bahan" class="form-control" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan</label>
                            <input type="text" name="satuan" id="satuan" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button id="btnSimpanTambah" type="button" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEdit" method="POST" action="">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Bahan Baku</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="edit_id_hidden" name="id">

                        <div class="form-group">
                            <label for="edit_id">ID Bahan Baku</label>
                            <input type="text" id="edit_id" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="edit_nama">Nama</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_satuan">Satuan</label>
                            <input type="text" name="satuan" id="edit_satuan" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_deskripsi">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button id="btnSimpanEdit" type="button" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // set CSRF for all AJAX (safest)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // helper escape
            function escapeHtml(text) {
                if (!text && text !== 0) return '';
                return String(text).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;')
                    .replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }

            // DataTable
            let table = $('#tabel-bahan').DataTable({
                ajax: {
                    url: "{{ route('master.databahan-baku') }}",
                    dataSrc: 'data'
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'stok_bahan_baku'
                    },
                    {
                        data: 'satuan'
                    },
                    {
                        data: 'deskripsi'
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
                        render: function(row) {
                            return `
                        <button class="btn btn-warning btn-sm btn-edit" data-id="${escapeHtml(row.id)}"
                            data-nama="${escapeHtml(row.nama)}"
                            data-stok="${escapeHtml(row.stok_bahan_baku)}"
                            data-satuan="${escapeHtml(row.satuan)}"
                            data-deskripsi="${escapeHtml(row.deskripsi)}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-hapus" data-id="${escapeHtml(row.id)}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                        }
                    }
                ]
            });

            // buka modal tambah + generate ID sederhana BB-001
            $('#btnModalTambah').on('click', function() {
                $('#formTambah')[0].reset();

                let allData = table.rows().data().toArray();
                let maxNum = 0;
                allData.forEach(row => {
                    let parts = String(row.id).split('-');
                    let num = parseInt(parts[1]) || 0;
                    if (num > maxNum) maxNum = num;
                });
                let next = (maxNum + 1).toString().padStart(3, '0');
                $('#id_bahan').val('BB-' + next);

                $('#modalTambah').modal('show');
            });

            // klik tombol simpan tambah (click-based)
            $('#btnSimpanTambah').on('click', function() {
                let $btn = $(this);
                let $form = $('#formTambah');

                $btn.prop('disabled', true).text('Menyimpan...');

                $.post($form.attr('action'), $form.serialize())
                    .done(function(res) {
                        $('#modalTambah').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Tersimpan',
                            timer: 1400,
                            showConfirmButton: false
                        });
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors || {};
                            let html = Object.values(errors).map(e => e[0]).join('<br>');
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi gagal',
                                html: html
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Gagal menyimpan'
                            });
                        }
                    })
                    .always(function() {
                        $btn.prop('disabled', false).text('Simpan');
                    });
            });

            // buka modal edit, isi field, set action
            $('#tabel-bahan').on('click', '.btn-edit', function() {
                let btn = $(this);
                let id = btn.data('id');

                $('#edit_id').val(id);
                $('#edit_id_hidden').val(id);
                $('#edit_nama').val(btn.data('nama'));
                $('#edit_satuan').val(btn.data('satuan'));
                $('#edit_deskripsi').val(btn.data('deskripsi'));

                // set action url untuk update (route menerima POST /updatebahan-baku/{id})
                let url = "{{ route('master.updatebahan-baku', ':id') }}".replace(':id', encodeURIComponent(
                    id));
                $('#formEdit').attr('action', url);

                $('#modalEdit').modal('show');
            });

            // klik tombol simpan edit (click-based)
            $('#btnSimpanEdit').on('click', function() {
                let $btn = $(this);
                let $form = $('#formEdit');
                let url = $form.attr('action');

                $btn.prop('disabled', true).text('Menyimpan...');

                $.post(url, $form.serialize())
                    .done(function(res) {
                        $('#modalEdit').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Diperbarui',
                            timer: 1400,
                            showConfirmButton: false
                        });
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors || {};
                            let html = Object.values(errors).map(e => e[0]).join('<br>');
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi gagal',
                                html: html
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Gagal update'
                            });
                        }
                    })
                    .always(function() {
                        $btn.prop('disabled', false).text('Simpan Perubahan');
                    });
            });

            // hapus
            $('#tabel-bahan').on('click', '.btn-hapus', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Data akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('master.hapusbahan-baku', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        }
                    }).done(function(res) {
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus',
                            text: res.message || 'Berhasil',
                            timer: 1400,
                            showConfirmButton: false
                        });
                    }).fail(function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal menghapus'
                        });
                    });
                });
            });

        });
    </script>
@endpush
