@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Laporan Keluar & Masuk Bahan Baku
    </h1>

    <!-- ================= FILTER ================= -->
   <form method="GET" class="form-inline mb-3 d-flex align-items-center flex-wrap">

    <label class="mr-2">Dari Tanggal</label>
    <input type="date"
           name="start_date"
           class="form-control mr-3"
           value="{{ request('start_date') }}">

    <label class="mr-2">Sampai Tanggal</label>
    <input type="date"
           name="end_date"
           class="form-control mr-3"
           value="{{ request('end_date') }}">

    <button type="submit" class="btn btn-primary mr-2">
        Filter
    </button>

    <a href="{{ url()->current() }}" class="btn btn-secondary mr-2">
        Reset
    </a>

    <a href="{{ route('gudang.laporan-harian.pdf', request()->query()) }}"
       class="btn btn-success">
        Download PDF
    </a>
</form>

    <!-- =============== END FILTER =============== -->

    <!-- ================= BAHAN MASUK ================= -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            Bahan Baku Masuk
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm" id="table-masuk">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Total Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($masuk as $m)
                    <tr>
                        <td>{{ $m->nama_bahan }}</td>
                        <td>{{ $m->satuan }}</td>
                        <td>{{ $m->total_jumlah }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">
                            Tidak ada data
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= BAHAN KELUAR ================= -->
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            Bahan Baku Keluar
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm" id="table-keluar">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Total Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($keluar as $k)
                    <tr>
                        <td>{{ $k->nama_bahan }}</td>
                        <td>{{ $k->satuan }}</td>
                        <td>{{ $k->total_jumlah }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">
                            Tidak ada data
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

{{-- ================= DATATABLES ================= --}}
@push('scripts')

<!-- DataTables CSS -->
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {

    $('#table-masuk').DataTable({
        paging: true,
        searching: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            zeroRecords: "No matching records found",
            paginate: {
                previous: "Previous",
                next: "Next"
            }
        }
    });

    $('#table-keluar').DataTable({
        paging: true,
        searching: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
       language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            zeroRecords: "No matching records found",
            paginate: {
                previous: "Previous",
                next: "Next"
            }
        }
    });

});
</script>
@endpush
