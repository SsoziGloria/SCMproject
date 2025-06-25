<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::resource('inventories', InventoryController::class)->middleware('auth');

//Dashboard and Inventory routes
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
                return redirect()->route('dashboard.user');
        }

    })->name('dashboard');
    Route::get('/dashboard-s', [InventoryController::class, 'dashboard'])
        ->name('dashboard.supplier');

    Route::get('/home', function () {
        return view('dashboard.user');
    })->name('dashboard.user');
});
Route::resource('inventories', InventoryController::class);

//Search functionality
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

//Error handling
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

//Redirect to dashboard or login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/create', function () {
    return view('supplier.create');
})->name('supplier.create');

Route::prefix('admin')->name('admin.')->group(function () {

    Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
});

Route::get('/admin/users/role/{role}', [App\Http\Controllers\Admin\UserController::class, 'byRole'])
    ->name('admin.users.byRole');

Route::get('/user-management', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');

Route::get('/profile', function () {
    return view('admin.profile');
})->middleware('auth')->name('profile');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');