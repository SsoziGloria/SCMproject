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
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/dashboard', [SupplierController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::get('/dashboard', [InventoryController::class, 'dashboard'])->middleware('auth')->name('dashboard');
