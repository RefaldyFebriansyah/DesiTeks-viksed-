<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Gudang;
use App\Http\Controllers\Kasir;
use Illuminate\Support\Facades\Route;

// ─── ROOT ──────────────────────────────────────────────────────────────────
Route::get('/', function() {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'gudang' => redirect()->route('gudang.dashboard'),
            'kasir'  => redirect()->route('kasir.dashboard'),
            default  => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// ─── AUTH ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── ADMIN ─────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Data Kain
    Route::resource('fabrics', Admin\FabricController::class);

    // Kategori
    Route::resource('categories', Admin\CategoryController::class)->except(['show']);

    // Supplier
    Route::resource('suppliers', Admin\SupplierController::class)->except(['show']);

    // Pelanggan
    Route::resource('customers', Admin\CustomerController::class);

    // Stok
    Route::get('/stocks',                    [Admin\StockController::class, 'index'])->name('stocks.index');
    Route::post('/stocks/{fabric}/adjust',   [Admin\StockController::class, 'adjust'])->name('stocks.adjust');

    // Riwayat Stok
    Route::get('/stock-movements', [Admin\StockMovementController::class, 'index'])->name('stock-movements.index');

    // Barang Masuk
    Route::get('/incoming-goods',         [Admin\IncomingGoodsController::class, 'index'])->name('incoming-goods.index');
    Route::get('/incoming-goods/create',  [Admin\IncomingGoodsController::class, 'create'])->name('incoming-goods.create');
    Route::post('/incoming-goods',        [Admin\IncomingGoodsController::class, 'store'])->name('incoming-goods.store');
    Route::get('/incoming-goods/{incomingGood}', [Admin\IncomingGoodsController::class, 'show'])->name('incoming-goods.show');

    // POS / Penjualan
    Route::get('/sales/pos',               [Admin\SaleController::class, 'pos'])->name('sales.pos');
    Route::post('/sales',                  [Admin\SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}/success',    [Admin\SaleController::class, 'success'])->name('sales.success');
    Route::get('/sales/{sale}/receipt',    [Admin\SaleController::class, 'receipt'])->name('sales.receipt');

    // Transaksi
    Route::get('/transactions',             [Admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{sale}',      [Admin\TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{sale}/cancel', [Admin\TransactionController::class, 'cancel'])->name('transactions.cancel');

    // Laporan
    Route::get('/reports',            [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [Admin\ReportController::class, 'exportCsv'])->name('reports.export-csv');

    // Pengguna
    Route::resource('users', Admin\UserController::class)->except(['show']);

    // Audit Log
    Route::get('/audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

    // Pengaturan Aplikasi
    Route::get('/settings',  [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});

// ─── GUDANG ────────────────────────────────────────────────────────────────
Route::prefix('gudang')->name('gudang.')->middleware(['auth', 'gudang'])->group(function () {

    Route::get('/dashboard', [Gudang\DashboardController::class, 'index'])->name('dashboard');

    // Data Kain & Stok (read-only untuk gudang)
    Route::get('/fabrics',        [Gudang\StockController::class, 'index'])->name('fabrics.index');
    Route::get('/stocks',         [Gudang\StockController::class, 'index'])->name('stocks.index');

    // Supplier (Gudang dapat mengelola supplier)
    Route::resource('suppliers', Gudang\SupplierController::class)->except(['show']);

    // Barang Masuk
    Route::get('/incoming-goods',         [Gudang\IncomingGoodsController::class, 'index'])->name('incoming-goods.index');
    Route::get('/incoming-goods/create',  [Gudang\IncomingGoodsController::class, 'create'])->name('incoming-goods.create');
    Route::post('/incoming-goods',        [Gudang\IncomingGoodsController::class, 'store'])->name('incoming-goods.store');
    Route::get('/incoming-goods/{incomingGood}', [Gudang\IncomingGoodsController::class, 'show'])->name('incoming-goods.show');
});

// ─── KASIR ─────────────────────────────────────────────────────────────────
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'kasir'])->group(function () {

    Route::get('/dashboard', [Kasir\DashboardController::class, 'index'])->name('dashboard');

    // POS
    Route::get('/sales/pos',               [Kasir\SaleController::class, 'pos'])->name('sales.pos');
    Route::post('/sales',                  [Kasir\SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}/success',    [Kasir\SaleController::class, 'success'])->name('sales.success');
    Route::get('/sales/{sale}/receipt',    [Kasir\SaleController::class, 'receipt'])->name('sales.receipt');

    // Transaksi hari ini
    Route::get('/transactions',        [Kasir\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{sale}', [Kasir\TransactionController::class, 'show'])->name('transactions.show');

    // Stok (read-only)
    Route::get('/stocks', [Kasir\StockController::class, 'index'])->name('stocks.index');

    // Pendapatan hari ini
    Route::get('/income', [Kasir\IncomeController::class, 'index'])->name('income.index');
});
