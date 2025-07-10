<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

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

Route::get('/', [VendorController::class, 'index']); 
Route::get('/vendor/validate', [VendorController::class, 'showValidationForm']);
Route::post('/vendor/validate', [VendorController::class, 'validateViaJava']);
Route::get('/vendor/test-api', [VendorController::class, 'testJavaApi']);
Route::resource('vendors', VendorController::class);