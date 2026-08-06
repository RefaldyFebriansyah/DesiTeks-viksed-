<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GudangMiddleware
{
    /**
     * Admin dan Gudang boleh akses route gudang.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $role = auth()->user()->role;

        if (!in_array($role, ['admin', 'gudang'])) {
            abort(403);
        }

        return $next($request);
    }
}
