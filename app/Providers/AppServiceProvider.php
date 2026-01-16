<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    // auto register all module view namespaces
    foreach (glob(app_path('Modules/*')) as $modPath) {
        $modName = strtolower(basename($modPath));
        $viewsPath = $modPath . '/Resources/Views';
        if (is_dir($viewsPath)) {
            \Illuminate\Support\Facades\View::addNamespace($modName, $viewsPath);
        }
    }

    // Auto-load route file web.php untuk semua modul (jika ada)
    // foreach (glob(app_path('Modules/*')) as $modPath) {
    //     $routePath = $modPath . '/routes/web.php';
    //     if (file_exists($routePath)) {
    //         // gunakan loadRoutesFrom supaya Laravel memproses route dengan benar
    //         $this->loadRoutesFrom($routePath);
    //     }
    // }
    // 
foreach (glob(app_path('Modules/*')) as $modPath) {

    // WEB ROUTES
    $webRoutes = $modPath . '/routes/web.php';
    if (file_exists($webRoutes)) {
        $this->loadRoutesFrom($webRoutes);
    }

    // API ROUTES (DISAMAKAN DENGAN WEB)
    $apiRoutes = $modPath . '/routes/api.php';
    if (file_exists($apiRoutes)) {
        Route::prefix('api')
            ->middleware('api')
            ->group($apiRoutes);
    }
}
    //     foreach (glob(app_path('Modules/*')) as $modPath) {

    //     // WEB ROUTES
    //     $webRoutes = $modPath . '/routes/web.php';
    //     if (file_exists($webRoutes)) {
    //         Route::middleware('web')
    //             ->group($webRoutes);
    //     }

    //     // API ROUTES
    //     $apiRoutes = $modPath . '/routes/api.php';
    //     if (file_exists($apiRoutes)) {
    //         Route::prefix('api')
    //             ->middleware('api')
    //             ->group($apiRoutes);
    //     }
    // }
    }
}
