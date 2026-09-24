<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryOrder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Halaman Beranda / Dashboard Terpadu Mitra Supplier MitraSeratBuana
     */
    public function index()
    {
        $user = Auth::user();
        $supplier = $user?->supplier;

        // Ambil data admin & cabang utama
        $admin = Schema::hasTable('users') 
            ? User::where('role', 'admin')->with('branch')->first() 
            : null;

        if ($admin && $admin->branch) {
            $branches = collect([$admin->branch]);
        } elseif (Schema::hasTable('branches')) {
            $mainBranch = Branch::where('is_main', true)->first() ?? Branch::first();
            $branches = $mainBranch ? collect([$mainBranch]) : collect();
        } else {
            $branches = collect();
        }

        $storeSettings = Schema::hasTable('settings') 
            ? Setting::pluck('value', 'key')->toArray() 
            : [];

        if ($user) {
            $query = DeliveryOrder::query();
            if ($supplier) {
                $query->where('supplier_id', $supplier->id);
            } else {
                $query->where('user_id', $user->id);
            }

            $totalSuratJalan = (clone $query)->count();
            $sedangDikirim   = (clone $query)->where('status', 'dikirim')->count();
            $diterima        = (clone $query)->where('status', 'diterima')->count();
            $ditolak         = (clone $query)->where('status', 'ditolak')->count();

            $totalRol   = (clone $query)->where('status', 'diterima')->sum('total_rol');
            $totalMeter = (clone $query)->where('status', 'diterima')->sum('total_meter');

            $recentDeliveries = (clone $query)
                ->with(['branch', 'receivedBy'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            // Tampilan publik / perkenalan portal supplier MitraSeratBuana (Guest Mode)
            $totalSuratJalan = 0;
            $sedangDikirim   = 0;
            $diterima        = 0;
            $ditolak         = 0;

            $totalRol   = 0;
            $totalMeter = 0;

            $recentDeliveries = collect();
        }

        return view('supplier.dashboard', compact(
            'user',
            'supplier',
            'admin',
            'branches',
            'storeSettings',
            'totalSuratJalan',
            'sedangDikirim',
            'diterima',
            'ditolak',
            'totalRol',
            'totalMeter',
            'recentDeliveries'
        ));
    }

    public function landing()
    {
        return $this->index();
    }
}
