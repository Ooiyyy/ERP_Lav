<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Penjualan\Http\Controllers\PembayaranController;

    // Route::post('/login', [AuthMobileController::class, 'authenticate']);
    Route::post('/pembayaran/create-snap', [PembayaranController::class, 'createSnapToken']);
	
