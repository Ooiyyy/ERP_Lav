@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Laporan Harian Gudang</h1>

    <!-- FILTER TANGGAL -->
    <form method="GET" class="form-inline mb-3">
        <label class="mr-2">Tanggal</label>
        <input type="date" name="tanggal"
               class="form-control mr-2"
               value="{{ $tanggal }}">
        <button class="btn btn-primary">Filter</button>
    </form>

    <!-- ================= BAHAN MASUK ================= -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            Bahan Baku Masuk
        </div>
        <div class="card-body">
            <table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Total Masuk</th>
            <th>Total Nominal</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($masuk as $m)
        <tr>
            <td>{{ $m->nama_bahan }}</td>
            <td>{{ $m->satuan }}</td>
            <td>{{ $m->total_jumlah }}</td>
            <td>
                Rp {{ number_format($m->total_nominal, 0, ',', '.') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">
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
            <table class="table table-bordered">
    <thead>
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
