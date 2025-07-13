<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
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
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\VendorValidationController;
use App\Http\Controllers\API\VendorValidationAPIController;

use App\Http\Controllers\SearchController;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Supplier routes
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
Route::get('/supplier/approved', [SupplierController::class, 'approved'])->name('supplier.approved');
Route::get('/supplier/requests', [SupplierController::class, 'requests'])->name('supplier.requests');
Route::get('/supplier/orders', [SupplierController::class, 'orders'])->name('supplier.orders');
Route::get('/supplier/messages', [SupplierController::class, 'messages'])->name('supplier.messages');
Route::get('/supplier/register', [SupplierController::class, 'showRegisterForm'])->name('suppliers.register.form');
Route::post('/supplier/register', [SupplierController::class, 'register'])->name('suppliers.register');

// Inventory routes (auth required)
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

// Dashboard routes
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
    Route::get('/dashboard-s', [InventoryController::class, 'dashboard'])->name('dashboard.supplier');
    Route::get('/check-stock-alert', [InventoryController::class, 'checkStockAlert'])->name('check.stock.alert');
    Route::get('/home', function () {
        return view('dashboard.user');
    })->name('dashboard.user');
});

// Search functionality
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/advanced', [SearchController::class, 'advanced'])->name('search.advanced');

// Error handling
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// Home route
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Redirect to dashboard or login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('welcome');
});

// Test mail
Route::get('/test-mail', function () {
    Mail::raw('This is a stock alert test email from Gmail SMTP!', function ($message) {
        $message->to('irenemargi256@gmail.com')
            ->subject('Stock Notification Test');
    });
    return 'Email sent!';
});

// Supplier create view
Route::get('/create', function () {
    return view('supplier.create');
})->name('supplier.create');

// Admin user management (resourceful, except create/store/show)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
});
Route::get('/admin/users/role/{role}', [UserController::class, 'byRole'])->name('admin.users.byRole');
Route::get('/user-management', [UserController::class, 'index'])->name('users');

// Profile routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

// Chat routes
Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');

// Email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/');
    })->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', function () {
        $user = auth()->user();
        if (!$user)
            abort(403, 'Not authenticated');
        if ($user->hasVerifiedEmail())
            return redirect('/');
        $user->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// FAQ
Route::get('/faq', function () {
    return view('faq.pages-faq');
})->name('faq');

// Products & Categories
Route::get('/products/export', function (Request $request) {
    $filters = $request->only(['category', 'supplier', 'stock']);
    return Excel::download(new ProductsExport($filters), 'products.xlsx');
})->name('products.export');

// Product Catalog routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

// Product CRUD routes

Route::resource('products', ProductController::class);
Route::resource('categories', CategoryController::class);
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');

// Product reviews
Route::get('/product-reviews', [ProductReviewController::class, 'index'])->name('productReviews.index');
Route::get('/product-reviews/create', [ProductReviewController::class, 'create'])->name('productReviews.create');
Route::post('/product-reviews', [ProductReviewController::class, 'store'])->name('productReviews.store');

// Stock levels
Route::get('/stock-levels', [InventoryController::class, 'stockLevels'])->name('stockLevels.index');

// Orders (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/in-progress', [OrderController::class, 'inProgress'])->name('orders.inProgress');
    Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/cancelled', [OrderController::class, 'cancelled'])->name('orders.cancelled');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

    Route::resource('orders', OrderController::class);

});

// Shop
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/product/{id}', [ShopController::class, 'show'])->name('shop.product');
Route::post('/shop/product/{id}/review', [ShopController::class, 'storeReview'])->name('shop.product.review');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

// Customer segments (ML)
Route::get('/admin/customer_segments', [CustomerSegmentController::class, 'index'])->name('customer-segments.index');

// Retailer dashboard (ML)
Route::get('/retailer/dashboard', [RetailerDashboardController::class, 'index'])->name('retailer.dashboard');

// Shipments (auth required)
Route::middleware(['auth'])->group(function () {
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


// Workers and workforce
Route::resource('workers', App\Http\Controllers\WorkerController::class);
Route::resource('workforce', App\Http\Controllers\WorkforceController::class);

// Analytics
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
Route::get('/analytics/revenue', [AnalyticsController::class, 'revenueDetails'])->name('analytics.revenue');
Route::get('/analytics/products', [AnalyticsController::class, 'productAnalytics'])->name('analytics.products');
Route::get('/analytics/users', [AnalyticsController::class, 'userAnalytics'])->name('analytics.users');

Route::middleware('auth')->prefix('api/admin')->group(function () {
    Route::get('/analytics/revenue-data', [AnalyticsController::class, 'getRevenueData']);
    Route::get('/analytics/order-status-data', [AnalyticsController::class, 'getOrderStatusData']);
});


// Admin user management (full resourceful, auth required)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
});

// Vendor Validation UI Routes
Route::middleware('auth')->group(function () {
    // Form display
    Route::get('/admin/vendor-validation', [VendorValidationController::class, 'showValidationForm'])
        ->name('admin.vendor-validation');

    // File download (if needed)
    Route::get('/admin/vendor-validation/download/{id}', [VendorValidationController::class, 'downloadValidationDocument'])
        ->name('admin.vendor-validation.download');

    // History view
    Route::get('/admin/vendor-validation/history', [VendorValidationController::class, 'validationHistory'])
        ->name('admin.vendor-validation.history');
});

// =====================================================================
//  API ROUTES (Served from web.php)
// =====================================================================
// This group handles all API requests. The Route::prefix('api') ensures
// that all URLs inside are correctly prefixed with '/api/', matching
// the calls made by the frontend JavaScript.
// =====================================================================

Route::prefix('api')->name('api.')->group(function () {

    /**
     * HEALTH CHECK PROXY
     * Securely checks the health of the Java service from the frontend.
     * URL: GET /api/service-health/vendor-validation
     */
    Route::get('/service-health/vendor-validation', function () {
        try {
            $javaUrl = config('services.vendor_validation.url', 'http://localhost:8080');
            $response = Http::timeout(5)->get($javaUrl . '/api/v1/vendor/health');
            return response()->json(['status' => $response->json('status', 'DOWN')]);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Health check proxy failed: ' . $e->getMessage());
            return response()->json(['status' => 'DOWN', 'error' => 'Service unavailable'], 503);
        }
    })->name('health-check');


    /**
     * VENDOR VALIDATION API ENDPOINTS
     * All routes related to the core document validation functionality.
     */
    Route::prefix('vendor-validation')->name('vendor-validation.')->group(function () {

        // POST /api/vendor-validation/validate
        // Handles the main document upload and validation request.
        Route::post('/validate', [VendorValidationAPIController::class, 'validateDocument'])->name('validate');

        // GET /api/vendor-validation/vendor/{vendorId}/history
        // Fetches validation history for a specific vendor.
        Route::get('/vendor/{vendorId}/history', [VendorValidationAPIController::class, 'history'])->name('history');

        // GET /api/vendor-validation/validation/{id}
        // Fetches the details of a single validation record.
        Route::get('/validation/{id}', [VendorValidationAPIController::class, 'show'])->name('show');

        // POST /api/vendor-validation/validation/{id}/revalidate
        // Triggers a revalidation of a previously uploaded document.
        Route::post('/validation/{id}/revalidate', [VendorValidationAPIController::class, 'revalidate'])->name('revalidate');

    });

});
