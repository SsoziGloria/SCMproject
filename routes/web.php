<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Mail;

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::resource('inventories', InventoryController::class)->middleware('auth');


Route::get('/', function () {
    return auth()->check()
    ? redirect()->route('dashboard'):
    redirect()->route('login');
});

Route::get('/test-mail', function () {
    Mail::raw('This is a stock alert test email from Gmail SMTP!', function ($message) {
        $message->to('irenemargi256@gmail.com') 
                ->subject('Stock Notification Test');
    });

    return 'Email sent!';
});

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/dashboard', [InventoryController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::get('/check-stock-alert', [InventoryController::class, 'checkStockAlert'])->middleware('auth')->name('check.stock.alert');

Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
