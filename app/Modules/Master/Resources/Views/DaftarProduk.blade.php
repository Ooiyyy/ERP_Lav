@extends('layouts.app')

@push('title')
    Daftar Produk
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 text-gray-800">Daftar Produk</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Produk</h6>
                <button type="button" class="btn btn-sm btn-primary" id="btnModalTambah">
                    <i class="fas fa-plus fa-sm"></i> Tambah
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel-produk" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>ID Produk</th>
                                <th>Nama</th>
                                <th>Foto</th>
                                <th>Harga</th>
                                <th>Satuan</th>
                                <th>Deskripsi</th>
                                <th>Stok</th>
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
        <div class="modal-dialog modal-lg">
            <form id="formTambah" method="POST" action="{{ route('master.simpanproduk') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Produk</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="id_produk">ID Produk</label>
                                <input type="text" name="id" id="id_produk" class="form-control" readonly required>
                            </div>
                            <div class="form-group col-md-8">
                                <label for="nama">Nama</label>
                                <input type="text" name="nama" id="nama" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="foto1">Foto 1 (utama)</label>
                                <input type="file" name="foto1" id="foto1" accept=".jpg,.jpeg"
                                    class="form-control-file">
                                <small id="foto1-error" class="text-danger d-none"></small>
                                <img id="foto1-preview" src="#" class="img-thumbnail mt-2 d-none"
                                    style="max-width:140px;">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="foto2">Foto 2</label>
                                <input type="file" name="foto2" id="foto2" accept=".jpg,.jpeg"
                                    class="form-control-file">
                                <small id="foto2-error" class="text-danger d-none"></small>
                                <img id="foto2-preview" src="#" class="img-thumbnail mt-2 d-none"
                                    style="max-width:140px;">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="foto3">Foto 3</label>
                                <input type="file" name="foto3" id="foto3" accept=".jpg,.jpeg"
                                    class="form-control-file">
                                <small id="foto3-error" class="text-danger d-none"></small>
                                <img id="foto3-preview" src="#" class="img-thumbnail mt-2 d-none"
                                    style="max-width:140px;">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="harga">Harga</label>
                                <input type="number" name="harga" id="harga" class="form-control" value="0"
                                    required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="satuan">Satuan</label>
                                <input type="text" name="satuan" id="satuan" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="stok">Stok</label>
                                <input type="number" name="stok" id="stok" class="form-control" value="0"
                                    required>
                            </div>
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
        <div class="modal-dialog modal-lg">
            <form id="formEdit" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Produk</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="edit_id_hidden" name="id">

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="edit_id">ID Produk</label>
                                <input type="text" id="edit_id" class="form-control" readonly>
                            </div>
                            <div class="form-group col-md-8">
                                <label for="edit_nama">Nama</label>
                                <input type="text" name="nama" id="edit_nama" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="edit_foto1">Foto 1 (utama)</label>
                                <input type="file" name="foto1" id="edit_foto1" accept=".jpg,.jpeg"
                                    class="form-control-file">
                                <small id="edit_foto1_error" class="text-danger d-none"></small>
                                <img id="edit_foto1_preview" src="#" class="img-thumbnail mt-2 d-none"
                                    style="max-width:140px;">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="edit_foto2">Foto 2</label>
                                <input type="file" name="foto2" id="edit_foto2" accept=".jpg,.jpeg"
                                    class="form-control-file">
                                <small id="edit_foto2_error" class="text-danger d-none"></small>
                                <img id="edit_foto2_preview" src="#" class="img-thumbnail mt-2 d-none"
                                    style="max-width:140px;">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="edit_foto3">Foto 3</label>
                                <input type="file" name="foto3" id="edit_foto3" accept=".jpg,.jpeg"
                                    class="form-control-file">
                                <small id="edit_foto3_error" class="text-danger d-none"></small>
                                <img id="edit_foto3_preview" src="#" class="img-thumbnail mt-2 d-none"
                                    style="max-width:140px;">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="edit_harga">Harga</label>
                                <input type="number" name="harga" id="edit_harga" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="edit_satuan">Satuan</label>
                                <input type="text" name="satuan" id="edit_satuan" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="edit_stok">Stok</label>
                                <input type="number" name="stok" id="edit_stok" class="form-control" required>
                            </div>
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

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
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

            let table = $('#tabel-produk').DataTable({
                ajax: {
                    url: "{{ route('master.dataproduk') }}",
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
                        data: 'foto',
                        render: function(d) {
                            if (!d) return '';
                            // Tambahkan timestamp untuk mencegah caching
                            const cacheBuster = '?v=' + new Date().getTime();
                            return `<img src="{{ asset('storage') }}/${d}${cacheBuster}" style="width:60px;height:60px;object-fit:cover;">`;
                        }
                    },
                    {
                        data: 'harga',
                        render: d => d ? d.toString() : '0'
                    },
                    {
                        data: 'satuan'
                    },
                    {
                        data: 'deskripsi'
                    },
                    {
                        data: 'stok'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(row) {
                            return `
                    <button class="btn btn-warning btn-sm btn-edit"
                        data-id="${esc(row.id)}"
                        data-nama="${esc(row.nama)}"
                        data-foto="${esc(row.foto)}"
                        data-harga="${esc(row.harga)}"
                        data-satuan="${esc(row.satuan)}"
                        data-deskripsi="${esc(row.deskripsi)}"
                        data-stok="${esc(row.stok)}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-hapus" data-id="${esc(row.id)}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                        }
                    }
                ]
            });

            // file preview helper
            const maxBytes = 1 * 1024 * 1024;

            function attachFileHandler(inputSel, previewSel, errSel) {
                $(inputSel).on('change', function() {
                    $(errSel).addClass('d-none').text('');
                    $(previewSel).addClass('d-none').attr('src', '#');
                    const file = this.files[0];
                    if (!file) return;
                    const name = file.name.toLowerCase();
                    const ext = name.split('.').pop();
                    if (ext !== 'jpg' && ext !== 'jpeg') {
                        $(errSel).removeClass('d-none').text('Format foto harus JPG.');
                        $(this).val('');
                        return;
                    }
                    if (file.size > maxBytes) {
                        $(errSel).removeClass('d-none').text('Ukuran foto maksimal 1MB.');
                        $(this).val('');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewSel).attr('src', e.target.result).removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                });
            }

            // attach for tambah
            attachFileHandler('#foto1', '#foto1-preview', '#foto1-error');
            attachFileHandler('#foto2', '#foto2-preview', '#foto2-error');
            attachFileHandler('#foto3', '#foto3-preview', '#foto3-error');
            // attach for edit
            attachFileHandler('#edit_foto1', '#edit_foto1_preview', '#edit_foto1_error');
            attachFileHandler('#edit_foto2', '#edit_foto2_preview', '#edit_foto2_error');
            attachFileHandler('#edit_foto3', '#edit_foto3_preview', '#edit_foto3_error');

            // buka modal tambah -> minta id server-side untuk menghindari race
            $('#btnModalTambah').on('click', function() {
                $('#formTambah')[0].reset();
                $('#foto1-preview,#foto2-preview,#foto3-preview').addClass('d-none').attr('src', '#');
                $('#foto1-error,#foto2-error,#foto3-error').addClass('d-none').text('');

                $.get("{{ route('master.produk.newid') }}").done(function(res) {
                    $('#id_produk').val(res.id_baru);
                    $('#modalTambah').modal('show');
                }).fail(function() {
                    // fallback client-side simple
                    let all = table.rows().data().toArray();
                    let max = 0;
                    all.forEach(r => {
                        let parts = String(r.id).split('-');
                        let n = parseInt(parts[1]) || 0;
                        if (n > max) max = n;
                    });
                    $('#id_produk').val('PRD-' + String(max + 1).padStart(3, '0'));
                    $('#modalTambah').modal('show');
                });
            });

            // simpan tambah (FormData)
            $('#btnSimpanTambah').on('click', function() {
                let $btn = $(this);
                let $form = $('#formTambah');
                let url = $form.attr('action');

                let fd = new FormData($form[0]);

                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false
                }).done(function(res) {
                    $('#modalTambah').modal('hide');
                    $form[0].reset();
                    $('#foto1-preview,#foto2-preview,#foto3-preview').addClass('d-none').attr('src',
                        '#');
                    $('#foto1-error,#foto2-error,#foto3-error').addClass('d-none').text('');
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Tersimpan',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }).fail(function(xhr) {
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
                }).always(function() {
                    $btn.prop('disabled', false).text('Simpan');
                });
            });

            // helper: coba load image, tampilkan jika berhasil, sembunyikan jika error
            function loadPreviewIfExists(url, $imgEl) {
                $imgEl.addClass('d-none').attr('src', '#');
                if (!url) return;
                const img = new Image();
                img.onload = function() {
                    $imgEl.attr('src', url).removeClass('d-none');
                };
                img.onerror = function() {
                    // tetap sembunyikan
                    $imgEl.addClass('d-none').attr('src', '#');
                };
                // cache-busting little trick if perlu: img.src = url + '?_=' + Date.now();
                img.src = url;
            }

            // buka modal edit: isi field + show existing foto1/2/3 as preview if exists
            $('#tabel-produk').on('click', '.btn-edit', function() {
                let btn = $(this);
                let id = btn.data('id');

                $('#edit_id').val(id);
                $('#edit_id_hidden').val(id);
                $('#edit_nama').val(btn.data('nama'));
                $('#edit_harga').val(btn.data('harga'));
                $('#edit_satuan').val(btn.data('satuan'));
                $('#edit_deskripsi').val(btn.data('deskripsi'));
                $('#edit_stok').val(btn.data('stok'));

                // reset preview/errors
                $('#edit_foto1_preview,#edit_foto2_preview,#edit_foto3_preview').addClass('d-none').attr(
                    'src', '#');
                $('#edit_foto1_error,#edit_foto2_error,#edit_foto3_error').addClass('d-none').text('');

                // base path: if btn.data('foto') exists it's produk/{id}/1.jpg (relative)
                // build full URLs for foto1, foto2, foto3
                let foto1Rel = btn.data('foto'); // e.g. produk/PRD-001/1.jpg  OR null
                // if not provided, still try conventional paths
                let foto1Url = foto1Rel ? '{{ asset('storage') }}/' + foto1Rel :
                    '{{ asset('storage') }}/produk/' + encodeURIComponent(id) + '/1.jpg';
                let foto2Url = '{{ asset('storage') }}/produk/' + encodeURIComponent(id) + '/2.jpg';
                let foto3Url = '{{ asset('storage') }}/produk/' + encodeURIComponent(id) + '/3.jpg';

                // try load each preview; loadPreviewIfExists will hide on error
                loadPreviewIfExists(foto1Url, $('#edit_foto1_preview'));
                loadPreviewIfExists(foto2Url, $('#edit_foto2_preview'));
                loadPreviewIfExists(foto3Url, $('#edit_foto3_preview'));

                // set action
                // adjust route name if needed; keep same as your project
                let url = "{{ route('master.updateproduk', ':id') }}".replace(':id', encodeURIComponent(id));
                $('#formEdit').attr('action', url);
                $('#modalEdit').modal('show');
            });

            // simpan edit (FormData)
            $('#btnSimpanEdit').on('click', function() {
                let $btn = $(this);
                let $form = $('#formEdit');
                let url = $form.attr('action');

                let fd = new FormData($form[0]);

                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false
                }).done(function(res) {
                    $('#modalEdit').modal('hide');
                    $form[0].reset();
                    $('#edit_foto1_preview,#edit_foto2_preview,#edit_foto3_preview').addClass(
                        'd-none').attr('src', '#');
                    $('#edit_foto1_error,#edit_foto2_error,#edit_foto3_error').addClass('d-none')
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Diperbarui',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }).fail(function(xhr) {
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
                }).always(function() {
                    $btn.prop('disabled', false).text('Simpan Perubahan');
                });
            });

            // hapus produk
            $('#tabel-produk').on('click', '.btn-hapus', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus?',
                    text: 'Semua foto akan dihapus dan data akan hilang permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('master.hapusproduk', ':id') }}".replace(':id', id),
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
