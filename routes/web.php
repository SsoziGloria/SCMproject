<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RetailerDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CustomerSegmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ShipmentController;


use App\Http\Controllers\SearchController;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

//for mySupplier
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
Route::get('/supplier/approved', [SupplierController::class, 'approved'])->name('supplier.approved');
Route::get('/supplier/requests', [SupplierController::class, 'requests'])->name('supplier.requests');
Route::get('/supplier/orders', [SupplierController::class, 'orders'])->name('supplier.orders');
Route::get('/supplier/messages', [SupplierController::class, 'messages'])->name('supplier.messages');
Route::get('/supplier/register', [SupplierController::class, 'showRegisterForm'])->name('suppliers.register.form');
Route::post('/supplier/register', [SupplierController::class, 'register'])->name('suppliers.register');

Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');

// Inventory routes 
Route::group(['middleware' => 'auth'], function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::put('/inventory/{inventory}/status', [InventoryController::class, 'updateStatus'])->name('inventory.update-status');
    Route::get('/inventory/{inventory}/history', [InventoryController::class, 'history'])->name('inventory.history');
    Route::get('/inventory/export', [InventoryController::class, 'export'])->name('inventory.export');

    Route::get('/inventories/reorders', [InventoryController::class, 'reorders'])->name('inventories.reorders');
    Route::get('/inventories/adjustments', [InventoryController::class, 'adjustments'])->name('inventories.adjustments');
    Route::get('/inventories/adjustments/create', [InventoryController::class, 'createAdjustment'])->name('inventories.adjustments.create');
    Route::post('/inventories/adjustments', [InventoryController::class, 'storeAdjustment'])->name('inventories.adjustments.store');
});

Route::resource('inventories', InventoryController::class)->middleware('auth');

//Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'supplier':
                return redirect()->route('dashboard.supplier');
            case 'retailer':
                return app(InventoryController::class)->dashboard();
            case 'user':
            default:
                return redirect()->route('dashboard.user');
        }

    })->name('dashboard');

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard-s', [InventoryController::class, 'dashboard'])
        ->name('dashboard.supplier');
    Route::get('/check-stock-alert', [InventoryController::class, 'checkStockAlert'])->middleware('auth')->name('check.stock.alert');
    Route::get('/home', function () {
        return view('dashboard.user');
    })->name('dashboard.user');
});


//Search functionality
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/advanced', [SearchController::class, 'advanced'])->name('search.advanced');

//Error handling
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

//home route
Route::get('/welcome', function () {
    return view('welcome');
});

//Redirect to dashboard or login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard') :
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


Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

// Chat routes
//Route::group(['prefix' => ''], function () {
//    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
//    Route::get('/chats', [App\Http\Controllers\ChatController::class, 'index'])->name('chats.index');
//    Route::get('/chats/{id}', [App\Http\Controllers\ChatController::class, 'show'])->name('chats.show');
//});
Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');



//Route::get('/orders/incoming', [OrderController::class, 'index'])->name('orders');
//Route::middleware('auth')->get('/orders/incoming', [OrderController::class, 'index'])->name('orders.incoming');
//Route::middleware('auth')->get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
// Route::middleware('auth')->get('/orders/incoming', [OrderController::class, 'incoming'])->name('orders.incoming');

Route::middleware('auth')->group(function () {
    // The page users see after clicking the verification link
    Route::get('/email/verify', function () {
        return view('auth.verify-email'); // Create this view
    })->name('verification.notice');

    // The link clicked in the email
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/'); // Or redirect to /dashboard, etc.
    })->middleware(['signed'])->name('verification.verify');

    // To resend the verification link
    Route::post('/email/verification-notification', function () {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Not authenticated');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Route to faq page
Route::get('/faq', function () {
    return view('faq.pages-faq');
})->name('faq');

//Products routes
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');

//Export routes
Route::get('/products/export', function (Request $request) {
    $filters = $request->only(['category', 'supplier', 'stock']);
    return Excel::download(new ProductsExport($filters), 'products.xlsx');
})->name('products.export');

// Product Catalog routes

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/product-reviews', [ProductReviewController::class, 'index'])->name('productReviews.index');
Route::get('/product-reviews/create', [ProductReviewController::class, 'create'])->name('productReviews.create');
Route::post('/product-reviews', [ProductReviewController::class, 'store'])->name('productReviews.store');
Route::get('/stock-levels', [InventoryController::class, 'index'])->name('stockLevels.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');

// Product CRUD routes

Route::resource('products', ProductController::class);

//category
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
Route::resource('categories', CategoryController::class);

//for stock levels
Route::get('/stock-levels', [InventoryController::class, 'stockLevels'])->name('stockLevels.index');

// Order routes
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/in-progress', [OrderController::class, 'inProgress'])->name('orders.inProgress');
    Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/cancelled', [OrderController::class, 'cancelled'])->name('orders.cancelled');
    Route::resource('orders', OrderController::class)->middleware('auth');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

     
});

// Shop routes
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/product/{id}', [ShopController::class, 'show'])->name('shop.product');
Route::post('/shop/product/{id}/review', [ShopController::class, 'storeReview'])->name('shop.product.review');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');


//for customer segments under ml
Route::get('/admin/customer_segments', [App\Http\Controllers\CustomerSegmentController::class, 'index'])->name('customer-segments.index');

//For machine learning with retailer blade
Route::get('/retailer/dashboard', [RetailerDashboardController::class, 'index'])->name('retailer.dashboard');


// Shipment Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update');
    Route::put('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy');
    Route::get('/shipments/export', [ShipmentController::class, 'export'])->name('shipments.export');
});
