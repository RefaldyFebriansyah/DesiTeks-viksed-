<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect()->route('kasir.sales.pos');
    }
}
