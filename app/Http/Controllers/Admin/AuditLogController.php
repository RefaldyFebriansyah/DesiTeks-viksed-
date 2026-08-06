<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $query->where('aktivitas','like','%'.$request->search.'%')
                  ->orWhereHas('user', fn($q) => $q->where('name','like','%'.$request->search.'%'));
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $logs = $query->orderBy('created_at','desc')->paginate(10)->withQueryString();
        return view('admin.audit-logs.index', compact('logs'));
    }
}
