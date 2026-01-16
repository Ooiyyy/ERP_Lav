<?php

// use Illuminate\Support\Facades\Route;
// use App\Modules\Akun\Http\Controllers\AkunController;

// Route::middleware(['web', 'auth:web'])->prefix('akun')->group(function () {
//     Route::get('/manajemen-akun', [AkunController::class, 'index'])->name('akun.manajemen-akun');
//     Route::get('/karyawan', [AkunController::class, 'karyawan'])->name('akun.karyawan');
//     Route::get('/jabatan', [AkunController::class, 'jabatan'])->name('akun.jabatan')->middleware(['role:001,002']);
//     Route::get('/pengaturan', [AkunController::class, 'pengaturan'])->name('akun.pengaturan');
//     Route::get('/jabatan/create', [AkunController::class, 'viewTambah'])->name('akun.tambahJabatan');
//     Route::post('/jabatan', [AkunController::class, 'tambahJabatan'])->name('akun.simpanJabatan');
//     Route::get('/jabatan/{id}/edit', [AkunController::class, 'editJabatan'])->name('akun.editJabatan');
//     Route::put('/jabatan/{id}', [AkunController::class, 'updateJabatan'])->name('akun.simpanUpdate');
//     Route::delete('/jabatan/{id}', [AkunController::class, 'hapusJabatan'])->name('akun.hapusJabatan');
// });

use Illuminate\Support\Facades\Route;
use App\Modules\Akun\Http\Controllers\AkunController;

// Semua route perlu login via web
Route::middleware(['web', 'auth:web',])->prefix('akun')->group(function () {

    /**
     * ===========================
     *  VIEW ROUTES (ADMIN ONLY)
     * ===========================
     * Superadmin = 001
     * Admin      = 002
     */
    Route::middleware(['role:001,002'])->group(function () {

        // Halaman utama manajemen
        Route::get('/manajemen-akun', [AkunController::class, 'index'])
            ->name('akun.manajemen-akun');

        Route::get('/karyawan', [AkunController::class, 'karyawan'])
            ->name('akun.karyawan');

        Route::get('/jabatan', [AkunController::class, 'jabatan'])
            ->name('akun.jabatan');

        Route::get('/pengaturan', [AkunController::class, 'pengaturan'])
            ->name('akun.pengaturan');


        /**
         * ===========================
         *  CRUD JABATAN (ADMIN ONLY)
         * ===========================
         */
        Route::get('/jabatan/create', [AkunController::class, 'viewTambahJabatan'])
            ->name('akun.tambahJabatan');
        Route::get('/karyawan/create', [AkunController::class, 'viewTambahKaryawan'])
            ->name('akun.tambahKaryawan');
        Route::get('/akun/create', [AkunController::class, 'viewTambahAkun'])
            ->name('akun.tambahAkun');

        Route::post('/jabatan', [AkunController::class, 'tambahJabatan'])
            ->name('akun.simpanJabatan');
        Route::post('/karyawan', [AkunController::class, 'tambahKaryawan'])
            ->name('akun.simpanKaryawan');
        Route::post('/akun', [AkunController::class, 'tambahAkun'])
            ->name('akun.simpanAkun');



        Route::get('/jabatan/{id}/edit', [AkunController::class, 'editJabatan'])
            ->name('akun.editJabatan');
        Route::get('/karyawan/{id}/edit', [AkunController::class, 'editKaryawan'])
            ->name('akun.editKaryawan');
        Route::get('/akun/{id_karyawan}/edit', [AkunController::class, 'editAkun'])
            ->name('akun.editAkun');
            Route::get('/karyawan/data', [AkunController::class, 'dataKaryawan'])->name('akun.karyawan.data');


        Route::put('/jabatan/{id}', [AkunController::class, 'updateJabatan'])
            ->name('akun.simpanUpdateJabatan');
        Route::put('/karyawan/{id}', [AkunController::class, 'updateKaryawan'])
            ->name('akun.simpanUpdateKaryawan');
        Route::put('/akun/{id_karyawan}', [AkunController::class, 'updateAkun'])
            ->name('akun.simpanUpdateAkun');

        Route::delete('/jabatan/{id}', [AkunController::class, 'hapusJabatan'])
            ->name('akun.hapusJabatan');
        Route::delete('/karyawan/{id}', [AkunController::class, 'hapusKaryawan'])
            ->name('akun.hapusKaryawan');
        Route::delete('/akun/{id_karyawan}', [AkunController::class, 'hapusAkun'])
            ->name('akun.hapusAkun');


        Route::patch('/akun/{id_karyawan}/toggle-status', [AkunController::class, 'toggleStatusAkun'])
            ->name('akun.toggleStatusAkun');
    });
});
