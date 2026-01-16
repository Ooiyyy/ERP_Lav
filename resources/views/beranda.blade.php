@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Dashboard Admin</h1>

    @php
        // data statis untuk presentasi
        $count = [
            'karyawan'   => 24,
            'akun'       => 18,
            'produk'     => 134,
            'bahan_baku' => 52,
            'toko'       => 3,
            'pelanggan'  => 98,
        ];
    @endphp

    <div class="row">

        {{-- Karyawan --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Karyawan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['karyawan'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Akun --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Akun</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['akun'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Produk --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Produk</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['produk'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-box-open fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Bahan Baku --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Bahan Baku</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['bahan_baku'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-cubes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Toko --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Toko</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['toko'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-store fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Pelanggan --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Pelanggan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['pelanggan'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection
