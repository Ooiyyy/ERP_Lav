<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Http\Controllers\AuthMobileController;
use App\Modules\Master\Http\Controllers\DashboardController;


// Route::middleware(['web','guest:web'])->group(function () {
//     Route::get('/login', [AuthWebController::class, 'showLoginForm'])->name('login');
//     Route::post('/login', [AuthWebController::class, 'authenticate'])->name('login.post');
// });

//     Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout')->middleware('auth:web');

// route untuk guest (belum login)

    Route::post('/login', [AuthMobileController::class, 'authenticate']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthMobileController::class, 'logout'])->name('api.logout');


// Route::get('/beranda', function () {
//     return view('beranda'); // ini akan membuka resources/views/admin.blade.php
// })->middleware('auth');

