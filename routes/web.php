<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\AdminOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DiamartController;
use App\Http\Controllers\DiamartProductController;
use App\Http\Controllers\MinimarketController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductMinimarketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\RadityaController;
use App\Http\Controllers\RadityaProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;

// Public Routes
Route::get('/', [WelcomeController::class, 'home'])->name('home');
Route::get('/gadget', [RadityaController::class, 'index'])->name('gadget.index');
Route::get('/gadget/{product}', [RadityaController::class, 'show'])
    ->name('gadget.show');
Route::get('/minimarket', [MinimarketController::class, 'index'])->name('minimarket.index');
Route::get('/minimarket/{id}', [MinimarketController::class, 'show'])->name('minimarket.show');

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth', 'check.role:super_admin,admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    // Route::resource('products', ProductController::class);
    // Route::resource('minimarket-products', ProductMinimarketController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('unit-kerja', UnitKerjaController::class);
    Route::resource('users', UserController::class);

    // Route::delete(
    //     'products/images/{image}',
    //     [ProductController::class, 'destroyImage']
    // )->name('products.images.destroy');

    // Route::patch(
    //     'products/images/{image}/primary',
    //     [ProductController::class, 'setPrimaryImage']
    // )->name('products.images.primary');

    Route::get('/qr-scan', function () {
        return view('admin.qrscan.index');
    })->name('admin.qr.scan.view');

    Route::post('/qr-scan/validate', [QrController::class, 'validateQr'])
        ->name('qr.validate');

    Route::post('/qr-scan/transaction', [QrController::class, 'processTransaction'])
        ->name('qr.transaction');

    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    Route::get('/admin/orders', [AdminOrderController::class, 'index'])
        ->name('admin.orders.index');
    Route::get('/admin/orders/{id}', [AdminOrderController::class, 'detailOrder'])
        ->name('admin.orders.detail');

    // Route::get(
    //     '/admin/orders/{order}/download-csv',
    //     [AdminOrderController::class, 'downloadOrderCsv']
    // )->name('admin.orders.downloadCsv');
});

Route::prefix('diamart')->group(function () {
    Route::get('/', [DiamartController::class, 'index'])->name('front.diamart.index');
    Route::get('/product/{id}', [DiamartController::class, 'show'])->name('front.diamart.show');
});
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // =============================================================
    // 1. UNIT BISNIS: RADITYA (Gadget, Elektronik, Furniture)
    // =============================================================

    // A. Custom Routes untuk Images (Harus diletakkan SEBELUM resource)
    Route::delete('raditya/images/{image}', [RadityaProductController::class, 'destroyImage'])
        ->name('raditya.images.destroy');

    Route::patch('raditya/images/{image}/primary', [RadityaProductController::class, 'setPrimaryImage'])
        ->name('raditya.images.primary');

    // B. Resource Route Utama
    // URL: /admin/raditya
    // Route Names: raditya.index, raditya.create, dst.
    Route::resource('raditya', RadityaProductController::class)
        ->parameters(['raditya' => 'product']); // Agar di function controller tetap pakai variabel $product


    // ROUTE PUBLIC (CUSTOMER)
    Route::prefix('diamart')->group(function () {
        // Halaman Utama Diamart
        Route::get('/', [DiamartController::class, 'index'])->name('front.diamart.index');

        // Halaman Detail Produk
        Route::get('/product/{id}', [DiamartController::class, 'show'])->name('front.diamart.show');
    });
    // =============================================================
    // 2. UNIT BISNIS: DIAMART (Sembako, Minimarket)
    // =============================================================

    // A. Custom Routes untuk Images
    Route::delete('diamart/images/{image}', [DiamartProductController::class, 'destroyImage'])
        ->name('diamart.images.destroy');

    Route::patch('diamart/images/{image}/primary', [DiamartProductController::class, 'setPrimaryImage'])
        ->name('diamart.images.primary');

    // B. Resource Route Utama
    // URL: /admin/diamart
    // Route Names: diamart.index, diamart.create, dst.
    Route::resource('diamart', DiamartProductController::class)
        ->parameters(['diamart' => 'product']); // Agar di function controller tetap pakai variabel $product

});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');
});

Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {

    Route::get('/', [CartController::class, 'index'])
        ->name('index');

    Route::post('/add/{id}', [CartController::class, 'add'])
        ->name('add');

    Route::post('/update/{itemId}', [CartController::class, 'update'])
        ->name('update');

    Route::delete('/remove/{itemId}', [CartController::class, 'remove'])
        ->name('remove');
});


Route::middleware('auth')->group(function () {

    // PAYMENT
    Route::get('/payment', [PaymentController::class, 'index'])
        ->name('payment.index');

    Route::post('/payment/process', [PaymentController::class, 'process'])
        ->name('payment.process');

    // Download CSV mock
    Route::get('/payment/download-csv', [PaymentController::class, 'downloadCsv'])
        ->name('payment.downloadCsv');

    // CHECKOUT
    Route::get('/checkout', [CartController::class, 'checkout'])
        ->name('checkout.index');

    Route::post('/checkout', [CartController::class, 'process'])
        ->name('checkout.process');
});
