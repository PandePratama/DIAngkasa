<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GadgetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;

// Public Routes
Route::get('/', [WelcomeController::class, 'home'])->name('home');
Route::get('/gadget', [GadgetController::class, 'index'])->name('gadget.index');
Route::get('/gadget/{product}', [GadgetController::class, 'show'])
    ->name('gadget.show');

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth', 'check.role:super_admin,admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('users', UserController::class);

    Route::delete(
        'products/images/{image}',
        [ProductController::class, 'destroyImage']
    )->name('products.images.destroy');

    Route::patch(
        'products/images/{image}/primary',
        [ProductController::class, 'setPrimaryImage']
    )->name('products.images.primary');

    Route::get('/qr-scan', function () {
        return view('admin.qrscan.index');
    })->name('admin.qr.scan.view');

    Route::post('/qr-scan/validate', [QrController::class, 'validateQr'])
        ->name('qr.validate');

    Route::post('/qr-scan/transaction', [QrController::class, 'processTransaction'])
        ->name('qr.transaction');

    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/update/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{product}', [CartController::class, 'remove'])->name('remove');
});

Route::middleware('auth')->group(function () {

    // CART
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart/add/{product}', [CartController::class, 'add'])
        ->name('cart.add');

    Route::post('/cart/update/{product}', [CartController::class, 'update'])
        ->name('cart.update');

    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])
        ->name('cart.remove');

    // PAYMENT
    Route::get('/payment', [CartController::class, 'payment'])
        ->name('payment.index');

    Route::post('/payment', [CartController::class, 'paymentProcess'])
        ->name('payment.process');

    // CHECKOUT
    Route::get('/checkout', [CartController::class, 'checkout'])
        ->name('checkout.index');

    Route::post('/checkout', [CartController::class, 'process'])
        ->name('checkout.process');
});
