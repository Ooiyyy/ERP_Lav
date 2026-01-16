@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Dashboard Gudang</h1>

    @php
        $count = [
            'masuk'      => 12,
            'keluar'     => 9,
            'bahan_baku' => 52,
        ];
    @endphp

    <div class="row">

        {{-- Bahan Masuk --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Bahan Masuk</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['masuk'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Bahan Keluar --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Bahan Keluar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['keluar'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Stok Bahan Baku --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <card class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stok Bahan Baku</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['bahan_baku'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-cubes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </card>
            </a>
        </div>

    </div>
</div>
@endsection
