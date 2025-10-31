<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Admin\ProductController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     if (Auth::check()) {
//         return redirect()->route('dashboard'); // atau admin/kasir sesuai role
//     }
//     return redirect()->route('login');
// });

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role;
    return $role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('kasir.dashboard');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Dashboard berdasarkan role
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('role:admin');
    Route::get('/admin/manageUser', [AdminDashboardController::class, 'manageUser'])
        ->name('admin.manageUser')
        ->middleware('role:admin');
    Route::get('/admin/listPelanggan', [AdminDashboardController::class, 'listPelanggan'])
        ->name('admin.listPelanggan')
        ->middleware('role:admin');
    Route::get('/admin/manageProduct', [AdminProductController::class, 'manageProduct'])
        ->name('admin.manageProduct')
        ->middleware('role:admin');
    Route::post('/admin/products', [AdminProductController::class, 'store'])
        ->name('admin.products.store');
    
    Route::delete('/admin/products/{id}', [AdminProductController::class, 'destroyProduct'])
        ->name('admin.product.destroy');

Route::put('/admin/products/{id}', [ProductController::class, 'update'])->name('admin.product.update');

    Route::get('/admin/user/{id}/edit', [AdminDashboardController::class, 'editUser'])
        ->name('admin.user.edit');
    Route::delete('/admin/user/{id}', [AdminDashboardController::class, 'destroyUser'])
        ->name('admin.user.destroy');
    Route::post('/admin/user', [AdminDashboardController::class, 'storeUser'])
        ->name('admin.user.store');
    Route::put('/admin/user/{id}', [AdminDashboardController::class, 'updateUser'])
        ->name('admin.user.update');
    // Route::get('/admin/products', [ProductController::class, 'index'])
    //     ->name('admin.products.index');
    Route::get('/kasir/dashboard', [KasirDashboardController::class, 'index'])
        ->name('kasir.dashboard')
        ->middleware('role:cashier');
});
