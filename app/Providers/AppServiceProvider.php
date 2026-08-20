<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();

        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) || isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
            
            // Deteksi apakah request berasal dari lokal dev biasa
            $isLocal = ($host === 'localhost:8000' || $host === '127.0.0.1:8000' || $host === 'localhost' || $host === '127.0.0.1');
            
            // Jika diakses dari luar (tunnel/IP publik), paksa skema HTTPS untuk menghindari Mixed Content block
            $proto = 'http';
            if (!$isLocal) {
                $proto = 'https';
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                $proto = $_SERVER['HTTP_X_FORWARDED_PROTO'];
            } elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                $proto = 'https';
            }
            
            \Illuminate\Support\Facades\URL::forceRootUrl($proto . '://' . $host);
            if ($proto === 'https') {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }
    }
}
