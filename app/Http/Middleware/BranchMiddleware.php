<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            $branchId = $user->branch_id;

            // Fallback to main branch if user has no branch_id
            if (!$branchId) {
                $mainBranch = Branch::where('is_main', true)->first() ?? Branch::first();
                $branchId = $mainBranch?->id ?? 1;
                $user->update(['branch_id' => $branchId]);
            }

            session(['active_branch_id' => $branchId]);

            // Share active branch name/object with views
            $activeBranch = Branch::find($branchId);
            view()->share('activeBranch', $activeBranch);
        }

        return $next($request);
    }
}
