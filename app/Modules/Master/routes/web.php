<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Master\Http\Controllers\AkunController;
use App\Modules\Master\Http\Controllers\TokoController;
use App\Modules\Master\Http\Controllers\ProdukController;
use App\Modules\Master\Http\Controllers\JabatanController;
use App\Modules\Master\Http\Controllers\KaryawanController;
use App\Modules\Master\Http\Controllers\BahanBakuController;
use App\Modules\Master\Http\Controllers\DashboardController;
use App\Modules\Master\Http\Controllers\PelangganController;
use App\Modules\Master\Http\Controllers\SupplierController;
use App\Modules\Master\Http\Controllers\PengaturanSistemController;

Route::middleware(['web', 'auth:web', 'role:001,002'])
    ->prefix('master')
    ->name('master.')
    ->group(function () {

        //Dashboard
        Route::get('/beranda-admin', [DashboardController::class, 'index'])->name('master.beranda');
        Route::get('/dashboard/data/pesanan-lunas', [DashboardController::class, 'pesananLunas'])->name('data.pesananLunas');
        Route::get('/dashboard/data/penjualan-sales', [DashboardController::class, 'penjualanPerSales'])->name('data.penjualanSales');

        // daftar akun
        Route::get('/akun', [AkunController::class, 'index'])->name('daftarAkun');
        Route::get('/dataakun', [AkunController::class, 'data'])->name('dataAkun');
        Route::get('/datakaryawantanpaakun', [AkunController::class, 'karyawanTanpaAkun'])->name('karyawanTanpaAkun');
        Route::post('/simpanakun', [AkunController::class, 'simpanAkun'])->name('simpanAkun');
        Route::post('/updateakun/{id}', [AkunController::class, 'updateAkun'])->name('updateAkun');
        Route::post('/toggleakun/{id}', [AkunController::class, 'toggleStatus'])->name('toggleAkun');
        Route::delete('/akun/{id}', [AkunController::class, 'hapusAkun'])->name('hapusAkun');

        // KARYAWAN
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan');
        Route::get('/karyawan/data', [KaryawanController::class, 'data'])->name('karyawan.data');
        Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('tambahKaryawan');
        Route::post('/karyawan', [KaryawanController::class, 'store'])->name('simpanKaryawan');
        Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('simpanUpdateKaryawan');
        Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('hapusKaryawan');

        // jabatan
        Route::get('/jabatan', [JabatanController::class, 'index'])->name('daftarJabatan');
        Route::get('/datajabatan', [JabatanController::class, 'data'])->name('dataJabatan');
        Route::post('/simpanjabatan', [JabatanController::class, 'simpanJabatan'])->name('simpanJabatan');
        Route::post('/updatejabatan/{id}', [JabatanController::class, 'updateJabatan'])->name('updateJabatan');
        Route::delete('/jabatan/{id}', [JabatanController::class, 'hapusJabatan'])->name('hapusJabatan');

        //PELANGGAN
        Route::get('/pelanggan', [PelangganController::class, 'index'])->name('daftarPelanggan');
        Route::get('/dataPelanggan', [PelangganController::class, 'data'])->name('dataPelanggan');
        Route::post('/simpanPelanggan', [PelangganController::class, 'simpanPelanggan'])->name('simpanPelanggan');
        Route::post('/updatePelanggan/{id}', [PelangganController::class, 'updatePelanggan'])->name('updatePelanggan');
        Route::delete('/pelanggan/{id}', [PelangganController::class, 'hapusPelanggan'])->name('hapusPelanggan');

        //Toko
        Route::get('/toko', [TokoController::class, 'index'])->name('daftarToko');
        Route::get('/dataToko', [TokoController::class, 'data'])->name('dataToko');
        Route::post('/simpanToko', [TokoController::class, 'simpanToko'])->name('simpanToko');
        Route::post('/updateToko/{id}', [TokoController::class, 'updateToko'])->name('updateToko');
        Route::delete('/toko/{id}', [TokoController::class, 'hapusToko'])->name('hapusToko');

        // bahan-baku
        Route::get('/bahan-baku', [BahanBakuController::class, 'index'])->name('daftarbahan-baku');
        Route::get('/databahan-baku', [BahanBakuController::class, 'data'])->name('databahan-baku');
        Route::post('/simpanbahan-baku', [BahanBakuController::class, 'simpanBahanBaku'])->name('simpanbahan-baku');
        Route::post('/updatebahan-baku/{id}', [BahanBakuController::class, 'updateBahanBaku'])->name('updatebahan-baku');
        Route::delete('/bahan-baku/{id}', [BahanBakuController::class, 'hapusBahanBaku'])->name('hapusbahan-baku');

        //Supplier
        Route::get('/supplier', [SupplierController::class, 'index'])->name('daftarSupplier');
        Route::get('/datasupplier', [SupplierController::class, 'dt'])->name('dataSupplier');
        // Route::get('/datasupplier', [SupplierController::class, 'data'])->name('dataSupplier');
        Route::post('/simpansupplier', [SupplierController::class, 'simpanSupplier'])->name('simpanSupplier');
        Route::post('/updatesupplier/{id}', [SupplierController::class, 'updateSupplier'])->name('updateSupplier');
        Route::delete('/supplier/{id}', [SupplierController::class, 'hapusSupplier'])->name('hapusSupplier');

        // Produk
        Route::get('/produk', [ProdukController::class, 'index'])->name('daftar-produk');
        Route::get('/dataproduk', [ProdukController::class, 'data'])->name('dataproduk');
        Route::get('/produk/new-id', [ProdukController::class, 'getNewId'])->name('produk.newid');
        Route::post('/simpanproduk', [ProdukController::class, 'simpanProduk'])->name('simpanproduk');
        Route::post('/updateproduk/{id}', [ProdukController::class, 'updateProduk'])->name('updateproduk');
        Route::delete('/produk/{id}', [ProdukController::class, 'hapusProduk'])->name('hapusproduk');

        //Pengaturan Sistem
        Route::get('/pengaturan-sistem', [PengaturanSistemController::class, 'index'])->name('daftarPengaturanSistem');
        Route::get('/data-pengaturan-sistem', [PengaturanSistemController::class, 'dt'])->name('dataPengaturanSistem');
        // Route::get('/dataPengaturanSistem', [PengaturanSistemController::class, 'data'])->name('dataPengaturanSistem');
        Route::post('/simpan-pengaturan-sistem', [PengaturanSistemController::class, 'simpanPengaturanSistem'])->name('simpanPengaturanSistem');
        Route::post('/update-pengaturan-sistem/{id}', [PengaturanSistemController::class, 'updatePengaturanSistem'])->name('updatePengaturanSistem');
        Route::delete('/hapus-pengaturan-sistem/{id}', [PengaturanSistemController::class, 'hapusPengaturanSistem'])->name('hapusPengaturanSistem');


    });
