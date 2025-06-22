<?php
use App\Http\Controllers\AuthController;

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        switch ($role) {
            case 'admin':
                return view('dashboard.admin');
            case 'supplier':
                return view('dashboard.supplier');
            case 'retailer':
                return view('dashboard.retailer');
            case 'user':
            default:
                return view('dashboard.user');
        }
    })->name('dashboard');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');