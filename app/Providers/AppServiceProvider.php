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

        // Share $storeName, $storeNameDepan, $storeNameBelakang, $formattedStoreName, and $formattedStoreNameLight
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $depan = \App\Models\Setting::getVal('nama_depan_toko', '');
                $belakang = \App\Models\Setting::getVal('nama_belakang_toko', '');
                $full = \App\Models\Setting::getVal('nama_toko', 'KainKita');
            } catch (\Throwable $e) {
                $depan = 'Kain';
                $belakang = 'Kita';
                $full = 'KainKita';
            }

            if (empty($depan) && empty($belakang)) {
                $full = $full ?: 'KainKita';
                $depan = $full;
                $belakang = '';
            } else {
                if (!empty($belakang)) {
                    $belakang = preg_replace('/\s+/', '', $belakang);
                    $belakang = ucfirst($belakang);
                }
                $full = $depan . $belakang;
            }

            $depanSafe = e($depan);
            $belakangSafe = e($belakang);

            if (!empty($belakangSafe)) {
                $formattedStoreName = $depanSafe . '<span style="color: #d97706;">' . $belakangSafe . '</span>';
                $formattedStoreNameLight = $depanSafe . '<span style="color: #f59e0b;">' . $belakangSafe . '</span>';
            } else {
                $formattedStoreName = preg_replace('/(kain)/i', '<span style="color: #d97706;">$1</span>', $depanSafe);
                $formattedStoreNameLight = preg_replace('/(kain)/i', '<span style="color: #f59e0b;">$1</span>', $depanSafe);
            }

            try {
                $pengumumanSupplier = \App\Models\Setting::getVal('pengumuman_supplier', 'Harap periksa kelengkapan kain, jumlah rol, dan surat jalan sebelum pengiriman ke gudang.');
            } catch (\Throwable $e) {
                $pengumumanSupplier = 'Harap periksa kelengkapan kain, jumlah rol, dan surat jalan sebelum pengiriman ke gudang.';
            }

            $view->with('storeName', $full);
            $view->with('storeNameDepan', $depan);
            $view->with('storeNameBelakang', $belakang);
            $view->with('formattedStoreName', $formattedStoreName);
            $view->with('formattedStoreNameLight', $formattedStoreNameLight);
            $view->with('pengumumanSupplier', $pengumumanSupplier);
        });

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
