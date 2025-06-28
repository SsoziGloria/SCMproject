<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;

use Illuminate\Support\Facades\Mail;
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

//for mySupplier
Route::get('/supplier/approved', [SupplierController::class, 'approved'])->name('supplier.approved');
Route::get('/supplier/requests', [SupplierController::class, 'requests'])->name('supplier.requests');
Route::get('/supplier/orders', [SupplierController::class, 'orders'])->name('supplier.orders');
Route::get('/supplier/messages', [SupplierController::class, 'messages'])->name('supplier.messages');


//for reorders
Route::get('/inventories/reorders', [InventoryController::class, 'reorders'])->name('inventories.reorders');


//for adjustments
Route::get('/inventories/adjustments', [InventoryController::class, 'adjustments'])->name('inventories.adjustments');
Route::get('/inventories/adjustments/create', [InventoryController::class, 'createAdjustment'])->name('inventories.adjustments.create');
Route::post('/inventories/adjustments', [InventoryController::class, 'storeAdjustment'])->name('inventories.adjustments.store');

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
  Route::get('/check-stock-alert', [InventoryController::class, 'checkStockAlert'])->middleware('auth')->name('check.stock.alert');


    Route::get('/home', function () {
        return view('dashboard.user');
    })->name('dashboard.user');
});


//Search functionality
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

//Error handling
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

//Redirect to dashboard or login
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

// Product Catalog routes

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/product-reviews', [ProductReviewController::class, 'index'])->name('productReviews.index');
Route::get('/product-reviews/create', [ProductReviewController::class, 'create'])->name('productReviews.create');
Route::post('/product-reviews', [ProductReviewController::class, 'store'])->name('productReviews.store');
Route::get('/stock-levels', [InventoryController::class, 'index'])->name('stockLevels.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

// Product CRUD routes

Route::resource('products', ProductController::class);

//category
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

//for stock levels
Route::get('/stock-levels', [InventoryController::class, 'stockLevels'])->name('stockLevels.index');
