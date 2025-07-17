<?php
// filepath: /Users/user/chocolate-scm/routes/web.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryAdjustmentController;
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
use App\Http\Controllers\API\VendorValidationProxyController;
use App\Http\Controllers\RetailerSalesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkforceController;
use App\Http\Controllers\VendorController;
use App\Exports\ProductsExport;
use App\Helpers\LocationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home/Landing page routes
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('welcome');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

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
    Route::get('/home', function () {
        return view('dashboard.user');
    })->name('dashboard.user');
    Route::get('/retailer/dashboard', [RetailerDashboardController::class, 'index'])->name('retailer.dashboard');
});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});

/*
|--------------------------------------------------------------------------
| Inventory Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth', 'prefix' => 'inventory'], function () {
    // Basic inventory CRUD
    Route::get('/', [InventoryController::class, 'index'])->name('inventories.index');
    Route::get('/create', [InventoryController::class, 'create'])->name('inventories.create');
    Route::post('/', [InventoryController::class, 'store'])->name('inventories.store');
    Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventories.edit');
    Route::put('/{inventory}', [InventoryController::class, 'update'])->name('inventories.update');
    Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('inventories.destroy');

    // Additional inventory functionality
    Route::put('/{inventory}/status', [InventoryController::class, 'updateStatus'])->name('inventories.update-status');
    Route::get('/{inventory}/history', [InventoryController::class, 'history'])->name('inventories.history');
    Route::get('/export', [InventoryController::class, 'export'])->name('inventories.export');

    // Low stock and reorders
    Route::get('/reorders', [InventoryController::class, 'reorders'])->name('inventories.reorders');

    // Inventory adjustments
    Route::get('/adjustments', [InventoryController::class, 'adjustments'])->name('inventories.adjustments');
    Route::get('/adjustments/create', [InventoryController::class, 'createAdjustment'])->name('inventories.adjustments.create');
    Route::post('/adjustments', [InventoryController::class, 'storeAdjustment'])->name('inventories.adjustments.store');
});

// Inventory Adjustments
Route::group(['middleware' => 'auth', 'prefix' => 'inventory/adjustments'], function () {
    Route::get('/', [InventoryAdjustmentController::class, 'index'])->name('inventories.adjustments');
    Route::get('/create', [InventoryAdjustmentController::class, 'create'])->name('inventories.adjustments.create');
    Route::post('/', [InventoryAdjustmentController::class, 'store'])->name('inventories.adjustments.store');
    Route::get('/{adjustment}', [InventoryAdjustmentController::class, 'show'])->name('inventories.adjustments.show');
    Route::get('/report', [InventoryAdjustmentController::class, 'report'])->name('inventories.adjustments.report');
    Route::get('/export', [InventoryAdjustmentController::class, 'export'])->name('inventories.adjustments.export');
    Route::get('/analytics', [InventoryAdjustmentController::class, 'analytics'])->name('inventories.adjustments.analytics');
});

// Stock alerts and levels
Route::get('/check-stock-alert', [InventoryController::class, 'checkStockAlert'])->name('check.stock.alert')->middleware('auth');
Route::get('/stock-levels', [InventoryController::class, 'stockLevels'])->name('stockLevels.index')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Product & Category Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Product routes
    Route::resource('products', ProductController::class);
    Route::put('products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
    Route::put('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');

    // Category routes
    Route::resource('categories', CategoryController::class);

    // Product reviews
    Route::resource('productReviews', ProductReviewController::class);
    Route::get('products/{product}/review', [ProductReviewController::class, 'createForProduct'])
        ->name('products.review.create');
});

// Product export 
Route::get('/products/export', function (Request $request) {
    $filters = $request->only(['category', 'supplier', 'stock']);
    return Excel::download(new ProductsExport($filters), 'products.xlsx');
})->name('products.export')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Order Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'vendor.verified'])->group(function () {
    Route::resource('orders', OrderController::class);
    Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/in-progress', [OrderController::class, 'inProgress'])->name('orders.inProgress');
    Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/cancelled', [OrderController::class, 'cancelled'])->name('orders.cancelled');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
});

/*
|--------------------------------------------------------------------------
| Shop, Cart & Checkout Routes
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Location Helpers Routes
|--------------------------------------------------------------------------
*/

Route::post('/get-region', function (Request $request) {
    $region = LocationHelper::getRegionFromCity($request->city);
    return response()->json(['region' => $region]);
});

Route::post('/get-country', function (Request $request) {
    $country = LocationHelper::getCountryFromCity($request->city);
    return response()->json(['country' => $country]);
});

/*
|--------------------------------------------------------------------------
| Supplier Routes
|--------------------------------------------------------------------------
*/

Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
Route::get('/supplier/approved', [SupplierController::class, 'approved'])->name('supplier.approved');
Route::get('/supplier/requests', [SupplierController::class, 'requests'])->name('supplier.requests');
Route::get('/supplier/orders', [SupplierController::class, 'orders'])->name('supplier.orders');
Route::get('/supplier/messages', [SupplierController::class, 'messages'])->name('supplier.messages');
Route::get('/supplier/register', [SupplierController::class, 'showRegisterForm'])->name('suppliers.register.form');
Route::post('/supplier/register', [SupplierController::class, 'register'])->name('suppliers.register');
Route::get('/create', function () {
    return view('supplier.create');
})->name('supplier.create');

Route::middleware(['auth', 'vendor.verified'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::resource('products', \App\Http\Controllers\Supplier\ProductController::class);
});

/*
|--------------------------------------------------------------------------
| Shipment Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::resource('shipments', ShipmentController::class);
    Route::put('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');
    Route::get('/shipments/export', [ShipmentController::class, 'export'])->name('shipments.export');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // User management
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');

    // Customer segments
    Route::get('/customer_segments', [CustomerSegmentController::class, 'index'])->name('customer-segments.index');

    // Vendor validation
    Route::get('/vendor-validation', [VendorValidationController::class, 'showValidationForm'])
        ->name('vendor-validation');
    Route::get('/vendor-validation/download/{id}', [VendorValidationController::class, 'downloadValidationDocument'])
        ->name('vendor-validation.download');
    Route::get('/vendor-validation/history', [VendorValidationController::class, 'validationHistory'])
        ->name('vendor-validation.history');
    Route::put('/vendor-validation/{id}/status', [VendorValidationController::class, 'updateVendorStatus'])
        ->name('vendor-validation.update-status');

    // User management
    Route::get('/user-management', [UserController::class, 'index'])->name('users');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
});

Route::get('/admin/users/role/{role}', [UserController::class, 'byRole'])->name('admin.users.byRole')->middleware('auth');


/*--------------------------------------------------------------------------
| Vendor Verification Routes
|--------------------------------------------------------------------------*/

Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'role:admin,retailer,supplier'])->group(function () {
    Route::get('/verification', [VendorController::class, 'showVerificationForm'])->name('verification.form');
    Route::post('/verification', [VendorController::class, 'storeVerification'])->name('verification.store');
    Route::get('/verification/pending', [VendorController::class, 'showPendingStatus'])->name('verification.pending');
    Route::get('/verification/approved', [VendorController::class, 'showApprovedStatus'])->name('verification.approved');
});
/*
|--------------------------------------------------------------------------
| Analytics Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,retailer'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/revenue', [AnalyticsController::class, 'revenueDetails'])->name('analytics.revenue');
    Route::get('/analytics/products', [AnalyticsController::class, 'productAnalytics'])->name('analytics.products');
    Route::get('/analytics/users', [AnalyticsController::class, 'userAnalytics'])->name('analytics.users');
    Route::get('/sales', [RetailerSalesController::class, 'index'])->name('sales');
});

/*
|--------------------------------------------------------------------------
| Workers & Workforce Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::resource('workers', WorkerController::class);
    Route::resource('workforce', WorkforceController::class);
});

/*
|--------------------------------------------------------------------------
| Other Routes
|--------------------------------------------------------------------------
*/

// Search functionality
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/advanced', [SearchController::class, 'advanced'])->name('search.advanced');

// Chat routes
Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index')->middleware('auth');

// FAQ
Route::get('/faq', function () {
    return view('faq.pages-faq');
})->name('faq');

// Test mail
Route::get('/test-mail', function () {
    Mail::raw('This is a stock alert test email from Gmail SMTP!', function ($message) {
        $message->to('irenemargi256@gmail.com')
            ->subject('Stock Notification Test');
    });
    return 'Email sent!';
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Move these to routes/api.php instead of having them in web.php
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    // Health check
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

    // Vendor validation API
    Route::prefix('vendor-validation')->name('vendor-validation.')->group(function () {
        Route::post('/validate', [VendorValidationAPIController::class, 'validateDocument'])->name('validate');
        Route::get('/vendor/{vendorId}/history', [VendorValidationAPIController::class, 'history'])->name('history');
        Route::get('/validation/{id}', [VendorValidationAPIController::class, 'show'])->name('show');
        Route::post('/validation/{id}/revalidate', [VendorValidationAPIController::class, 'revalidate'])->name('revalidate');
        Route::post('/validate-existing/{vendorId}', [VendorValidationProxyController::class, 'validateExistingDocument'])
            ->name('validate-existing');
    });

    // Analytics API endpoints
    Route::middleware('auth')->prefix('admin')->group(function () {
        Route::get('/analytics/revenue-data', [AnalyticsController::class, 'getRevenueData']);
        Route::get('/analytics/order-status-data', [AnalyticsController::class, 'getOrderStatusData']);
    });
});

// Error handling - this should be at the end of your routes file
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});