<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::resource('inventories', InventoryController::class)->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        switch ($role) {
            case 'admin':
                return view('dashboard.admin');
            case 'supplier':
                return redirect()->route('dashboard.supplier');
            case 'retailer':
                return view('dashboard.retailer');
            case 'user':
            default:
                return view('dashboard.user');
        }

    })->name('dashboard');
    Route::get('/dashboard-s', [InventoryController::class, 'dashboard'])
        ->name('dashboard.supplier');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');