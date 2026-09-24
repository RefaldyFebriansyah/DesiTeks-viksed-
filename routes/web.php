<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Gudang;
use App\Http\Controllers\Kasir;
use Illuminate\Support\Facades\Route;

// ─── ROOT ──────────────────────────────────────────────────────────────────
Route::get('/', function() {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'kasir'    => redirect()->route('kasir.sales.pos'),
            'gudang'   => redirect()->route('gudang.stocks.index'),
            default    => app(App\Http\Controllers\Supplier\DashboardController::class)->landing(),
        };
    }
    return app(App\Http\Controllers\Supplier\DashboardController::class)->landing();
})->name('home');

// ─── AUTH ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',             [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login/staff',       [LoginController::class, 'showStaffLoginForm'])->name('login.staff');
    Route::post('/login',            [LoginController::class, 'login'])->name('login.post');
    Route::get('/login/verify-2fa',  [LoginController::class, 'show2faForm'])->name('login.verify-2fa');
    Route::post('/login/verify-2fa', [LoginController::class, 'verify2fa'])->name('login.verify-2fa.post');

    Route::get('/register',          [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',         [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── NOTIFICATIONS (AUTH) ──────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markRead');
});

// ─── ADMIN ─────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [Admin\DashboardController::class, 'getChartData'])->name('dashboard.chartData');

    // Data Kain
    Route::resource('fabrics', Admin\FabricController::class);

    // Kategori
    Route::resource('categories', Admin\CategoryController::class)->except(['show']);

    // Supplier
    Route::resource('suppliers', Admin\SupplierController::class)->except(['show']);

    // Pelanggan
    Route::resource('customers', Admin\CustomerController::class);

    // Stok
    Route::get('/stocks',                             [Admin\StockController::class, 'index'])->name('stocks.index');
    Route::post('/stocks/{fabric}/adjust',            [Admin\StockController::class, 'adjust'])->name('stocks.adjust');
    Route::post('/stocks/{fabric}/update-max-stock',  [Admin\StockController::class, 'updateMaxStock'])->name('stocks.update-max-stock');
    Route::post('/stocks/update-total-max-stock',     [Admin\StockController::class, 'updateTotalMaxStock'])->name('stocks.update-total-max-stock');

    // Riwayat Stok
    Route::get('/stock-movements', [Admin\StockMovementController::class, 'index'])->name('stock-movements.index');

    // Barang Masuk (Admin)
    Route::get('/incoming-goods',                 [Admin\IncomingGoodsController::class, 'index'])->name('incoming-goods.index');
    Route::get('/incoming-goods/create',          [Admin\IncomingGoodsController::class, 'create'])->name('incoming-goods.create');
    Route::post('/incoming-goods',                 [Admin\IncomingGoodsController::class, 'store'])->name('incoming-goods.store');
    Route::get('/incoming-goods/{incomingGood}', [Admin\IncomingGoodsController::class, 'show'])->name('incoming-goods.show');

    // Surat Jalan Online (Admin)
    Route::get('/delivery-orders',                               [Admin\DeliveryOrderController::class, 'index'])->name('delivery-orders.index');
    Route::get('/delivery-orders-check-all-status',              [Admin\DeliveryOrderController::class, 'checkAllStatus'])->name('delivery-orders.check-all-status');
    Route::get('/delivery-orders/{deliveryOrder}',               [Admin\DeliveryOrderController::class, 'show'])->name('delivery-orders.show');
    Route::get('/delivery-orders/{deliveryOrder}/print',         [Admin\DeliveryOrderController::class, 'print'])->name('delivery-orders.print');
    Route::get('/delivery-orders/{deliveryOrder}/check-status',  [Admin\DeliveryOrderController::class, 'checkStatus'])->name('delivery-orders.check-status');
    Route::post('/delivery-orders/{deliveryOrder}/approve-admin', [Admin\DeliveryOrderController::class, 'approveByAdmin'])->name('delivery-orders.approve-admin');
    Route::post('/delivery-orders/{deliveryOrder}/accept',        [Admin\DeliveryOrderController::class, 'accept'])->name('delivery-orders.accept');
    Route::post('/delivery-orders/{deliveryOrder}/reject',        [Admin\DeliveryOrderController::class, 'reject'])->name('delivery-orders.reject');

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
    Route::get('/dashboard/chart-data', [Gudang\DashboardController::class, 'getChartData'])->name('dashboard.chartData');

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

    // Surat Jalan Online Masuk / Stok Sedang Dikirim
    Route::get('/delivery-orders',                               [Gudang\DeliveryOrderController::class, 'index'])->name('delivery-orders.index');
    Route::get('/delivery-orders-check-all-status',              [Gudang\DeliveryOrderController::class, 'checkAllStatus'])->name('delivery-orders.check-all-status');
    Route::get('/delivery-orders/{deliveryOrder}',               [Gudang\DeliveryOrderController::class, 'show'])->name('delivery-orders.show');
    Route::get('/delivery-orders/{deliveryOrder}/print',         [Gudang\DeliveryOrderController::class, 'print'])->name('delivery-orders.print');
    Route::get('/delivery-orders/{deliveryOrder}/check-status',  [Gudang\DeliveryOrderController::class, 'checkStatus'])->name('delivery-orders.check-status');
    Route::post('/delivery-orders/{deliveryOrder}/approve-admin', [Gudang\DeliveryOrderController::class, 'approveByAdmin'])->name('delivery-orders.approve-admin');
    Route::post('/delivery-orders/{deliveryOrder}/accept',        [Gudang\DeliveryOrderController::class, 'accept'])->name('delivery-orders.accept');
    Route::post('/delivery-orders/{deliveryOrder}/reject',        [Gudang\DeliveryOrderController::class, 'reject'])->name('delivery-orders.reject');
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

// ─── SUPPLIER ──────────────────────────────────────────────────────────────
Route::prefix('supplier')->name('supplier.')->group(function () {
    // Halaman Depan / Landing Page Web Modern Supplier (Akses Publik / Tanpa Login Dulu)
    Route::get('/', [App\Http\Controllers\Supplier\DashboardController::class, 'landing'])->name('landing');

    // Fitur yang mewajibkan login supplier (saat mau isi atau akses surat jalan & profil)
    Route::middleware(['auth', 'supplier'])->group(function () {
        Route::get('/dashboard',                            [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');

        // Surat Jalan Online
        Route::get('/delivery-orders',                      [App\Http\Controllers\Supplier\DeliveryOrderController::class, 'index'])->name('delivery-orders.index');
        Route::get('/delivery-orders-check-all-status',     [App\Http\Controllers\Supplier\DeliveryOrderController::class, 'checkAllStatus'])->name('delivery-orders.check-all-status');
        Route::get('/delivery-orders/create',               [App\Http\Controllers\Supplier\DeliveryOrderController::class, 'create'])->name('delivery-orders.create');
        Route::post('/delivery-orders',                     [App\Http\Controllers\Supplier\DeliveryOrderController::class, 'store'])->name('delivery-orders.store');
        Route::get('/delivery-orders/{deliveryOrder}',      [App\Http\Controllers\Supplier\DeliveryOrderController::class, 'show'])->name('delivery-orders.show');
        Route::get('/delivery-orders/{deliveryOrder}/check-status', [App\Http\Controllers\Supplier\DeliveryOrderController::class, 'checkStatus'])->name('delivery-orders.check-status');
        Route::post('/delivery-orders/{deliveryOrder}/ship',[App\Http\Controllers\Supplier\DeliveryOrderController::class, 'ship'])->name('delivery-orders.ship');
        Route::get('/delivery-orders/{deliveryOrder}/print',[App\Http\Controllers\Supplier\DeliveryOrderController::class, 'print'])->name('delivery-orders.print');

        // Profil Supplier
        Route::get('/profile',                              [App\Http\Controllers\Supplier\ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile',                             [App\Http\Controllers\Supplier\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password',                    [App\Http\Controllers\Supplier\ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    });
});
