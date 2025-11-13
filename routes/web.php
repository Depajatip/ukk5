<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ManageUserController as AdminManageUserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\admin\HistoryPesananController as AdminHistoryPesanan;
use App\Http\Controllers\admin\DataPelangganController as AdminDataPelangganController;
use App\Http\Controllers\admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\OrderMenuController as KasirorderMenuController;
use App\Http\Controllers\Kasir\listPesananController as KasirlistPesananController;
use App\Http\Controllers\Kasir\PaymentController as KasirPaymentController;
use App\Http\Controllers\Admin\ProductController;


Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role;
    return $role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('kasir.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('role:admin');
    Route::get('/admin/dashboard/data', [AdminDashboardController::class, 'data'])
        ->name('admin.dashboard.data')
        ->middleware('role:admin');
    Route::get('/admin/manageUser', [AdminManageUserController::class, 'manageUser'])
        ->name('admin.manageUser')
        ->middleware('role:admin');
    Route::get('/admin/dataPelanggan', [AdminDataPelangganController::class, 'index'])
        ->name('admin.dataPelanggan')
        ->middleware('role:admin');
    Route::get('/admin/dataPelanggan', [AdminDataPelangganController::class, 'dataPelanggan'])
        ->name('admin.dataPelanggan')
        ->middleware('role:admin');
    Route::get('/admin/manageProduct', [AdminProductController::class, 'manageProduct'])
        ->name('admin.manageProduct')
        ->middleware('role:admin');
    Route::post('/admin/products', [AdminProductController::class, 'store'])
        ->name('admin.products.store');

    Route::delete('/admin/products/{id}', [AdminProductController::class, 'destroyProduct'])
        ->name('admin.product.destroy');

    Route::put('/admin/products/{id}', [ProductController::class, 'update'])->name('admin.product.update');

    Route::get('/admin/user/{id}/edit', [AdminManageUserController::class, 'editUser'])
        ->name('admin.user.edit');
    Route::delete('/admin/user/{id}', [AdminManageUserController::class, 'destroyUser'])
        ->name('admin.user.destroy');
    Route::post('/admin/user', [AdminManageUserController::class, 'storeUser'])
        ->name('admin.user.store');
    Route::put('/admin/user/{id}', [AdminManageUserController::class, 'updateUser'])
        ->name('admin.user.update');
    // Route::get('/admin/products', [ProductController::class, 'index'])
    //     ->name('admin.products.index');
    Route::get('/kasir/dashboard', [KasirDashboardController::class, 'index'])
        ->name('kasir.dashboard')
        ->middleware('role:cashier');
    Route::get('/kasir/orderMenu', [KasirorderMenuController::class, 'index'])
        ->name('kasir.orderMenu')
        ->middleware('role:cashier');
    Route::get('/kasir/listPesanan', [KasirlistPesananController::class, 'index'])
    ->name('kasir.listPesanan')
    ->middleware('role:cashier');
    Route::post('/kasir/orderMenu', [KasirorderMenuController::class, 'store'])
    ->name('kasir.orderMenu.store');
    Route::get('/kasir/penjualan/{penjualanID}', function ($penjualanID) {
    // Sementara: redirect ke list dulu
    return redirect()->route('kasir.listPesanan')->with('warning', 'Fitur detail belum tersedia.');
        })->name('penjualan.detail');

    // History pesanan (status: paid, cancelled)
Route::get('/admin/historyPesanan', [AdminHistoryPesanan::class, 'index'])
    ->name('admin.historyPesanan')
->middleware('role:admin');
Route::get('/admin/historyPesanan/{penjualanID}', [AdminHistoryPesanan::class, 'show'])
    ->name('admin.pesanan.show')
->middleware('role:admin');

// Ambil data pesanan untuk modal payment
Route::get('/kasir/pesanan/{penjualanID}', [KasirPaymentController::class, 'show'])
    ->name('kasir.pesanan.show');

// Proses pembayaran
Route::post('/kasir/pesanan/{penjualanID}/bayar', [KasirPaymentController::class, 'bayar'])
    ->name('kasir.pesanan.bayar');
});
