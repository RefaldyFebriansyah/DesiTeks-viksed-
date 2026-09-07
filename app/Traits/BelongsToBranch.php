<?php

namespace App\Traits;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::creating(function ($model) {
            if (!$model->branch_id) {
                $branchId = session('active_branch_id') ?? auth()->user()?->branch_id;
                if (!$branchId) {
                    $mainBranch = Branch::where('is_main', true)->first() ?? Branch::first();
                    $branchId = $mainBranch?->id ?? 1;
                }
                $model->branch_id = $branchId;
            }
        });

        static::addGlobalScope('branch', function (Builder $builder) {
            $branchId = session('active_branch_id') ?? auth()->user()?->branch_id;
            if ($branchId) {
                $table = $builder->getQuery()->from;
                $builder->where(function ($q) use ($table, $branchId) {
                    $q->where($table . '.branch_id', $branchId)
                      ->orWhereNull($table . '.branch_id');
                });
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
