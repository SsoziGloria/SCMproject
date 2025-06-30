<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::resource('inventories', InventoryController::class)->middleware('auth');

//Dashboard and Inventory routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        switch ($role) {
            case 'admin':
                return view('dashboard.admin');
            case 'supplier':
                return redirect()->route('dashboard.supplier');
            case 'retailer':
                return app(InventoryController::class)->dashboard();
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
Route::resource('inventories', InventoryController::class);

//Search functionality
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

//Error handling
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
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



Route::get('/orders/incoming', [OrderController::class, 'index'])->name('orders');
Route::middleware('auth')->get('/orders/incoming', [OrderController::class, 'index'])->name('orders.incoming');
Route::middleware('auth')->get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
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