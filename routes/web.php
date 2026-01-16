<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/login', [LoginController::class, 'login']);
// Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route::get('/beranda', function () {
//     return view('beranda'); // ini akan membuka resources/views/admin.blade.php
// })->middleware('auth');

Route::get('/_debug_kernel', function () {
    return [
        'kernel_class' => get_class(app(\Illuminate\Contracts\Http\Kernel::class)),
        'middleware_aliases' => (new \ReflectionClass(app(\Illuminate\Contracts\Http\Kernel::class)))->getProperty('middlewareAliases')->getValue(app(\Illuminate\Contracts\Http\Kernel::class)),
        'config_cache_default' => config('cache.default'),
    ];
});