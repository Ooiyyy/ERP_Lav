<?php
// app/Modules/Akun/routes/web.php

use Illuminate\Support\Facades\Route;
use App\Modules\Gudang\Http\Controllers\BahanBakuMasukController;
use App\Modules\Gudang\Http\Controllers\BahanBakuKeluarController;
use App\Modules\Gudang\Http\Controllers\LaporanGudangController;
use App\Modules\Gudang\Http\Controllers\ProdukMasukController;
use App\Modules\Gudang\Http\Controllers\LaporanProdukController;

Route::middleware(['web', 'auth:web', 'role:001,002'])
    ->prefix('gudang')
    ->name('gudang.')
    ->group(function () {
        // datatable data (JSON)
        // view
        Route::get(
            '/bahan-baku-masuk',
            [BahanBakuMasukController::class, 'index']
        )->name('bahan-masuk.index');

        // datatable data (JSON)
        Route::get(
            '/bahan-baku-masuk/data',
            [BahanBakuMasukController::class, 'data']
        )->name('bahan-masuk.data');

        // store
        Route::post(
            '/bahan-baku-masuk/store',
            [BahanBakuMasukController::class, 'store']
        )->name('bahan-masuk.store');
        Route::get(
            '/bahan-baku-masuk/{id}',
            [BahanBakuMasukController::class, 'show']
        )->name('bahan-masuk.show');

        Route::put(
            '/bahan-baku-masuk/{id}',
            [BahanBakuMasukController::class, 'update']
        )->name('bahan-masuk.update');

        Route::delete(
            '/bahan-baku-masuk/{id}',
            [BahanBakuMasukController::class, 'destroy']
        )->name('bahan-masuk.destroy');

        // Bahan baku keluar
         Route::get('/bahan-baku-keluar',
            [BahanBakuKeluarController::class, 'index']
        )->name('bahan-keluar.index');

        // DATA (optional, untuk datatable)
        Route::get('/bahan-baku-keluar/data',
            [BahanBakuKeluarController::class, 'data']
        )->name('bahan-keluar.data');

        // STORE
        Route::post('/bahan-baku-keluar/store',
            [BahanBakuKeluarController::class, 'store']
        )->name('bahan-keluar.store');

        // SHOW (EDIT)
        Route::get('/bahan-baku-keluar/{id}',
            [BahanBakuKeluarController::class, 'show']
        )->name('bahan-keluar.show');

        // UPDATE
        Route::put('/bahan-baku-keluar/{id}',
            [BahanBakuKeluarController::class, 'update']
        )->name('bahan-keluar.update');

        // DELETE
        Route::delete('/bahan-baku-keluar/{id}',
            [BahanBakuKeluarController::class, 'destroy']
        )->name('bahan-keluar.destroy');

        Route::get('/laporan-harian', 
            [LaporanGudangController::class, 'index']
        )->name('laporan.harian');
        // halaman index produk masuk
        Route::get('/produk-masuk',
            [ProdukMasukController::class, 'index']
        )->name('produk-masuk.index');

        // simpan transaksi produk masuk
        Route::post('/produk-masuk/store',
            [ProdukMasukController::class, 'store']
        )->name('produk-masuk.store');
       // SHOW (EDIT)
        Route::get('/produk-masuk/{id}',
            [ProdukMasukController::class, 'show']
        )->name('produk-masuk.show');

        Route::get('/produk-masuk/{id}/edit',
            [ProdukMasukController::class, 'edit']
        )->name('produk-masuk.edit');

        // UPDATE
        Route::put('/produk-masuk/{id}',
            [ProdukMasukController::class, 'update']
        )->name('produk-masuk.update');

        // DELETE
        Route::delete('/produk-masuk/{id}',
            [ProdukMasukController::class, 'destroy']
        )->name('produk-masuk.destroy');
        Route::get(
                'bahan-baku-masuk/detail/{id}',
                [BahanBakuMasukController::class, 'detail']
            )->name('bahan-masuk.detail');
             Route::get(
        'bahan-baku-keluar/detail/{id}',
        [BahanBakuKeluarController::class, 'detail']
    )->name('bahan-keluar.detail');
// EXPORT PDF
Route::get(
    'gudang/laporan-harian/pdf',
    [LaporanGudangController::class, 'exportPdf']
)->name('laporan-harian.pdf');
Route::get(
    'laporan-produk',
    [LaporanProdukController::class, 'index']
)->name('laporan-produk');

Route::get(
    'laporan-produk/pdf',
    [LaporanProdukController::class, 'exportPdf']
)->name('laporan-produk.pdf');


    // Laporan Gudang (Masuk & Keluar)
    Route::get('/laporan-harian', [LaporanGudangController::class, 'index'])
        ->name('laporan-harian');

    // Stok Bahan Baku
    Route::get('/stok-bahan-baku', [BahanBakuController::class, 'stok'])
        ->name('stok-bahan-baku');

    });