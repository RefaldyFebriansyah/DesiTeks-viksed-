<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware aliases
        $middleware->alias([
            'admin'    => \App\Http\Middleware\AdminMiddleware::class,
            'gudang'   => \App\Http\Middleware\GudangMiddleware::class,
            'kasir'    => \App\Http\Middleware\KasirMiddleware::class,
            'supplier' => \App\Http\Middleware\SupplierMiddleware::class,
            'branch'   => \App\Http\Middleware\BranchMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\BranchMiddleware::class,
        ]);

        // Redirect to /login with friendly flash message
        $middleware->redirectGuestsTo(function (Request $request) {
            session()->flash('info', 'Silakan masuk ke akun Anda terlebih dahulu untuk mengisi formulir atau mengakses fitur ini.');
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
