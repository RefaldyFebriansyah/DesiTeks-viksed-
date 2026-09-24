<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupplierMiddleware
{
    /**
     * Membatasi akses hanya untuk pengguna dengan role supplier.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('info', 'Silakan masuk ke akun Mitra Supplier Anda terlebih dahulu untuk mengisi atau mengakses fitur ini.');
        }

        if (auth()->user()->role !== 'supplier') {
            abort(403, 'Akses terbatas hanya untuk akun Rekanan Supplier MitraSeratBuana.');
        }

        return $next($request);
    }
}
