@php
    $role = auth('web')->user()->karyawan->id_jabatan;
    $isAdmin = in_array($role, ['001']);
    $isGudang = in_array($role, ['002']);
@endphp
@if ($isAdmin)

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">Dashboard Admin</h1>

        {{-- Cards --}}
        <div class="row">
            @foreach ($cards as $card)
                <div class="col-xl-3 col-md-6 mb-4">
                    <a href="{{ $card['route'] }}" class="text-decoration-none">
                        <div class="card border-left-{{ $card['color'] }} shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-{{ $card['color'] }} text-uppercase mb-1">
                                            {{ $card['label'] }}
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ number_format($card['count']) }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas {{ $card['icon'] }} fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Date range filter --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label>From</label>
                <input type="date" id="chart_from" class="form-control"
                    value="{{ \Carbon\Carbon::now()->startOfMonth()->toDateString() }}">
            </div>
            <div class="col-md-4">
                <label>To</label>
                <input type="date" id="chart_to" class="form-control"
                    value="{{ \Carbon\Carbon::now()->endOfMonth()->toDateString() }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button id="btn_refresh_charts" class="btn btn-primary">Refresh Charts</button>
            </div>
        </div>

        {{-- Charts --}}
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Pesanan Lunas (Jumlah per hari)</div>
                    <div class="card-body">
                        <div id="chart_pesanan_lunas" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Total Penjualan per Sales</div>
                    <div class="card-body">
                        <div id="chart_penjualan_sales" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@endif

@push('scripts')
    <!-- Highcharts via CDN -->
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var chartLunas = Highcharts.chart('chart_pesanan_lunas', {
                title: {
                    text: 'Pesanan Lunas'
                },
                xAxis: {
                    categories: []
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Pesanan'
                    }
                },
                series: [{
                    name: 'Pesanan Lunas',
                    data: []
                }]
            });

            var chartSales = Highcharts.chart('chart_penjualan_sales', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Penjualan per Sales'
                },
                xAxis: {
                    categories: []
                },
                yAxis: {
                    title: {
                        text: 'Total (Rp)'
                    }
                },
                series: [{
                    name: 'Total',
                    data: []
                }]
            });

            function ajaxGet(url, params, successCb, errorCb) {
                console.log('[AJAX] GET', url, params);
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: params,
                    dataType: 'json',
                    complete: function(jqXHR, textStatus) {
                        console.log('[AJAX] complete', url, 'status:', jqXHR.status, 'textStatus:',
                            textStatus);
                    },
                    success: function(json, textStatus, jqXHR) {
                        console.log('[AJAX] success', url, 'HTTP', jqXHR.status, json);
                        successCb(json);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('[AJAX] error', url, 'HTTP', jqXHR.status, errorThrown);
                        console.error('responseText:', jqXHR.responseText);
                        if (errorCb) errorCb(jqXHR, textStatus, errorThrown);
                    }
                });
            }

            function loadPesananLunas() {
                var dateFrom = $('#chart_from').val();
                var dateTo = $('#chart_to').val();
                var url = "{{ route('master.data.pesananLunas') }}";
                ajaxGet(url, {
                    date_from: dateFrom,
                    date_to: dateTo
                }, function(json) {
                    if (json.status === 'ok') {
                        console.log('pesananLunas data', json);
                        chartLunas.xAxis[0].setCategories(json.categories || []);
                        chartLunas.series[0].setData(json.data || []);
                    } else {
                        console.warn('pesananLunas respon bukan ok', json);
                        alert('Gagal memuat data pesanan lunas. Periksa console.');
                    }
                }, function() {
                    alert('Terjadi kesalahan saat memuat pesanan lunas. Periksa console/network.');
                });
            }

            function loadPenjualanSales() {
                var dateFrom = $('#chart_from').val();
                var dateTo = $('#chart_to').val();
                var url = "{{ route('master.data.penjualanSales') }}";
                ajaxGet(url, {
                    date_from: dateFrom,
                    date_to: dateTo
                }, function(json) {
                    if (json.status === 'ok') {
                        console.log('penjualanSales data', json);
                        chartSales.xAxis[0].setCategories(json.categories || []);
                        var data = (json.data || []).map(function(v) {
                            return parseFloat(v) || 0;
                        });
                        chartSales.series[0].setData(data);
                    } else {
                        console.warn('penjualanSales respon bukan ok', json);
                        alert('Gagal memuat data penjualan per sales. Periksa console.');
                    }
                }, function() {
                    alert('Terjadi kesalahan saat memuat penjualan per sales. Periksa console/network.');
                });
            }

            $('#btn_refresh_charts').on('click', function() {
                loadPesananLunas();
                loadPenjualanSales();
            });

            // initial load
            loadPesananLunas();
            loadPenjualanSales();
        });
    </script>
@endpush