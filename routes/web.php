<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductReviewController;


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

// Chat routes
Route::group(['prefix' => ''], function () {
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chats', [App\Http\Controllers\ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{id}', [App\Http\Controllers\ChatController::class, 'show'])->name('chats.show');
});

// Order routes
Route::middleware('auth')->group(function () {
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/in-progress', [OrderController::class, 'inProgress'])->name('orders.inProgress');
    Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/cancelled', [OrderController::class, 'cancelled'])->name('orders.cancelled');
    Route::resource('orders', OrderController::class)->middleware('auth');
});


Route::get('/dashboard', [OrderController::class, 'dashboard'])->name('dashboard');


// Product Catalog routes


Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/product-reviews', [ProductReviewController::class, 'index'])->name('productReviews.index');
Route::get('/stock-levels', [InventoryController::class, 'index'])->name('stockLevels.index');

// Product CRUD routes
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');