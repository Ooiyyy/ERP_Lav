@extends('layouts.app')

@push('title')
    Daftar Jabatan
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-gray-800">Daftar Jabatan</h4>
        <button id="btnModalTambah" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="jabatanTable" class="table table-bordered table-hover" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama Jabatan</th>
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
        <form id="formTambah" method="POST" action="{{ route('master.simpanJabatan') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jabatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="t_id">ID (otomatis)</label>
                        <input type="text" id="t_id" name="id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="t_nama">Nama Jabatan</label>
                        <input type="text" id="t_nama" name="nama" class="form-control" required>
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
                    <h5 class="modal-title">Edit Jabatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="e_id" name="id">
                    <div class="form-group">
                        <label for="e_nama">Nama Jabatan</label>
                        <input type="text" id="e_nama" name="nama" class="form-control" required>
                    </div>
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
$(function(){
    // base URLs / routes (sesuaikan kalau beda)
    const urlData = "{{ route('master.dataJabatan') }}";
    const urlStore = "{{ route('master.simpanJabatan') }}";
    const urlUpdateTemplate = "{{ route('master.updateJabatan', ':id') }}"; // replace :id
    const urlDeleteTemplate = "{{ route('master.hapusJabatan', ':id') }}"; // replace :id

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    // helper escape
    function esc(s){ if (s===null||s===undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    // prevent DataTables default alert on error
    $.fn.dataTable.ext.errMode = 'none';
    $('#jabatanTable').on('error.dt', function (e, settings, techNote, message) {
        console.error('DataTables error:', techNote, message);
    });

    // init DataTable (client-side)
    const table = $('#jabatanTable').DataTable({
        ajax: { url: urlData, dataSrc: 'data' },
        columns: [
            { data: null, render: (d,t,r,m) => m.row + 1, width: '50px' },
            { data: 'id', width: '100px' },
            { data: 'nama' },
            { data: 'tanggal_buat', width: '160px' },
            {
                data: null,
                orderable: false,
                searchable: false,
                width: '120px',
                render: function(row) {
                    return `
                        <button class="btn btn-warning btn-sm btn-edit" data-id="${esc(row.id)}" data-nama="${esc(row.nama)}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm btn-hapus" data-id="${esc(row.id)}"><i class="fas fa-trash"></i></button>
                    `;
                }
            }
        ],
        order: [[1,'asc']],
        responsive: true,
        language: { emptyTable: "Belum ada data jabatan." }
    });

    function reloadTable() {
        if (table && table.ajax) table.ajax.reload(null, false);
        else location.reload();
    }

    // focus management to avoid aria-hidden warning
    function clearFocus() {
        try { if (document.activeElement) document.activeElement.blur(); } catch(e){}
    }
    $('#modalTambah').on('shown.bs.modal', function(){ $(this).find('input:visible:first').trigger('focus'); });
    $('#modalEdit').on('shown.bs.modal', function(){ $(this).find('input:visible:first').trigger('focus'); });

    // show modal tambah
    $('#btnModalTambah').on('click', function(){
        clearFocus();
        $('#formTambah')[0].reset();

        // generate candidate id from existing rows (not authoritative)
        try {
            const arr = table.rows().data().toArray();
            let max = 0;
            arr.forEach(r => {
                const m = String(r.id).match(/(\d+)$/);
                if (m) { const n = parseInt(m[1]) || 0; if (n > max) max = n; }
            });
            $('#t_id').val(max ? String(max+1).padStart(3,'0') : '');
        } catch(e){
            $('#t_id').val('');
        }

        $('#modalTambah').modal('show');
    });

    // submit tambah (form submit)
    $('#formTambah').on('submit', function(e){
        e.preventDefault();
        const $btn = $('#btnSimpanTambah');
        $btn.prop('disabled', true).text('Menyimpan...');

        const form = this;
        const fd = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function(res){
            $('#modalTambah').modal('hide');
            form.reset();
            reloadTable();
            Swal.fire({ icon:'success', title:'Berhasil', text: res.message || 'Tersimpan', timer:1200, showConfirmButton:false });
        }).fail(function(xhr){
            if (xhr.status === 422) {
                const errors = xhr.responseJSON?.errors || {};
                const html = Object.values(errors).map(x=> x[0]).join('<br>');
                Swal.fire({ icon:'error', title:'Validasi', html });
            } else {
                Swal.fire({ icon:'error', title:'Error', text: xhr.responseJSON?.message || 'Gagal menyimpan' });
            }
        }).always(function(){ $btn.prop('disabled', false).text('Simpan'); });
    });

    // buka edit
    $('#jabatanTable').on('click', '.btn-edit', function(){
        clearFocus();
        const $b = $(this);
        const id = $b.data('id');
        const nama = $b.data('nama') || '';

        $('#e_id').val(id);
        $('#e_nama').val(nama);

        // set action (use named route template replacement)
        const url = urlUpdateTemplate.replace(':id', encodeURIComponent(id));
        $('#formEdit').attr('action', url);

        $('#modalEdit').modal('show');
    });

    // submit edit
    $('#formEdit').on('submit', function(e){
        e.preventDefault();
        const $btn = $('#btnSimpanEdit');
        $btn.prop('disabled', true).text('Menyimpan...');

        const form = this;
        const fd = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function(res){
            $('#modalEdit').modal('hide');
            form.reset();
            reloadTable();
            Swal.fire({ icon:'success', title:'Berhasil', text: res.message || 'Diperbarui', timer:1200, showConfirmButton:false });
        }).fail(function(xhr){
            if (xhr.status === 422) {
                const errors = xhr.responseJSON?.errors || {};
                const html = Object.values(errors).map(x=> x[0]).join('<br>');
                Swal.fire({ icon:'error', title:'Validasi', html });
            } else {
                Swal.fire({ icon:'error', title:'Error', text: xhr.responseJSON?.message || 'Gagal update' });
            }
        }).always(function(){ $btn.prop('disabled', false).text('Simpan Perubahan'); });
    });

    // hapus
    $('#jabatanTable').on('click', '.btn-hapus', function(){
        const id = $(this).data('id');
        if (!id) return;

        Swal.fire({
            title: 'Yakin hapus?',
            text: 'Data jabatan akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus'
        }).then(function(result){
            if (!result.isConfirmed) return;

            // delete via named route template
            const url = urlDeleteTemplate.replace(':id', encodeURIComponent(id));

            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' }
            }).done(function(res){
                reloadTable();
                Swal.fire({ icon:'success', title:'Terhapus', text: res.message || 'Berhasil', timer:1200, showConfirmButton:false });
            }).fail(function(xhr){
                Swal.fire({ icon:'error', title:'Gagal', text: xhr.responseJSON?.message || 'Gagal hapus' });
            });
        });
    });

    // accessibility: when modal hides, return focus to add button to avoid focus on hidden element
    $('#modalTambah, #modalEdit').on('hidden.bs.modal', function(){
        $('#btnModalTambah').trigger('focus');
    });
});
</script>
@endpush
