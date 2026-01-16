<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Http\Controllers\AuthWebController;
use App\Modules\Master\Http\Controllers\DashboardController;


// Route::middleware(['web','guest:web'])->group(function () {
//     Route::get('/login', [AuthWebController::class, 'showLoginForm'])->name('login');
//     Route::post('/login', [AuthWebController::class, 'authenticate'])->name('login.post');
// });

//     Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout')->middleware('auth:web');

// route untuk guest (belum login)
Route::middleware(['web', 'guest:web'])->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthWebController::class, 'authenticate'])->name('login.post');
});

// route untuk user login
Route::middleware(['web', 'auth:web'])->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('web.logout');

    Route::get('/beranda-admin', [DashboardController::class, 'index'])
    ->name('dashboard');
    Route::get('/gudang', function () {
        return view('gudang'); // membuka resources/views/beranda.blade.php
    })->name('dashboardGudang');
});
// Route::get('/beranda', function () {
//     return view('beranda'); // ini akan membuka resources/views/admin.blade.php
// })->middleware('auth');

