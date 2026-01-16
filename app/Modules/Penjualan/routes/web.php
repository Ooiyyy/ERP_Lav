<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MIDTRANS WEBHOOK (PUBLIC - TANPA LOGIN)
|--------------------------------------------------------------------------
|
| Route ini HARUS di luar middleware auth.
| Digunakan Midtrans untuk callback status pembayaran.
|
*/

use App\Http\Controllers\MidtransWebhookController;

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);
Route::get('/midtrans/webhook-test', function () {
    return 'WEBHOOK OK';
});

/*
|--------------------------------------------------------------------------
| ROUTE PENJUALAN (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/

use App\Modules\Penjualan\Http\Controllers\OrderController;
use App\Modules\Penjualan\Http\Controllers\PembayaranController;
use App\Modules\Penjualan\Http\Controllers\HistoryController;
use App\Modules\Master\Http\Controllers\ProdukController;

Route::prefix('api')->group(function () {
    // Route::post('/login', [AuthMobileController::class, 'authenticate']);
    Route::middleware('auth:sanctum')->post('/pembayaran/create-snap', [PembayaranController::class, 'createSnapToken']);

});



Route::middleware(['web', 'auth:web', 'role:001,003'])
    ->prefix('penjualan')
    ->name('penjualan.')
    ->group(function () {

        // ===============================
        // API LIST PRODUK
        // ===============================
        Route::get('/api/products', [ProdukController::class, 'apiList'])
            ->name('products.api');

        // ===============================
        // PESANAN
        // ===============================
        Route::get('/pesanan', [OrderController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/data', [OrderController::class, 'data'])->name('pesanan.data');
        Route::get('/pesanan/dataModal', [OrderController::class, 'dataModal']);
        Route::post('/pesanan/store', [OrderController::class, 'store'])->name('pesanan.store');
        Route::get('/pesanan/{id}/edit', [OrderController::class, 'edit'])->name('pesanan.edit');
        Route::put('/pesanan/{id}', [OrderController::class, 'update'])->name('pesanan.update');
        Route::delete('/pesanan/{id}', [OrderController::class, 'destroy'])->name('pesanan.destroy');
        Route::get('/pesanan/{id}/detail', [OrderController::class, 'detail'])->name('pesanan.detail');
        Route::get('/pesanan/{id}/summary', [OrderController::class, 'summary'])->name('pesanan.summary');

        // ===============================
        // AJAX DETAIL & PEMBAYARAN
        // ===============================
        Route::get('/orders/{id}/payments', [OrderController::class, 'payments'])
            ->name('orders.payments');

        // ===============================
        // PEMBAYARAN
        // ===============================
        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('/pembayaran/data', [PembayaranController::class, 'data'])->name('pembayaran.data');
        Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->name('pembayaran.show');

        // Cash
        Route::post('/pembayaran/cash', [PembayaranController::class, 'storeCash'])
            ->name('pembayaran.cash');

        // Midtrans SNAP
        Route::post('/pembayaran/create-snap', [PembayaranController::class, 'createSnapToken'])
            ->name('pembayaran.createSnap');

        // ===============================
        // HISTORY
        // ===============================
        Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
        Route::get('/history/data', [HistoryController::class, 'data'])->name('history.data');
        Route::get('/history/{id}', [HistoryController::class, 'show'])->name('history.show');
        //tambahan roiy
        Route::get('/history/filters/sales', [\App\Modules\Penjualan\Http\Controllers\HistoryController::class, 'filterSales']);
        Route::get('/history/filters/produk', [\App\Modules\Penjualan\Http\Controllers\HistoryController::class, 'filterProduk']);
        Route::get('/history/filters/toko', [\App\Modules\Penjualan\Http\Controllers\HistoryController::class, 'filterToko']);
        Route::get('/history/nota/{id}', [\App\Modules\Penjualan\Http\Controllers\HistoryController::class, 'notaPdf'])->name('history.nota');
    });
        Route::get(
        '/penjualan/pesanan/toko-by-sales/{id_sales}',
        [OrderController::class, 'tokoBySales']
    );
