<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    CategoryController,
    ProductController,
    BrandController,
    CartController,
    DiamartController,
    DiamartProductController,
    MinimarketController,
    PaymentController,
    ProfileController,
    QrController,
    RadityaController,
    RadityaProductController,
    TransactionController,
    UnitKerjaController,
    UserController,
    WelcomeController,
    AdminOrderController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'home'])->name('home');

// Gadget (Raditya) - Public
Route::get('/gadget', [RadityaController::class, 'index'])->name('gadget.index');
Route::get('/gadget/{product}', [RadityaController::class, 'show'])->name('gadget.show');

// Minimarket (Diamart) - Public
Route::get('/minimarket', [MinimarketController::class, 'index'])->name('minimarket.index');
Route::get('/minimarket/{id}', [MinimarketController::class, 'show'])->name('minimarket.show');


// Auth
Route::get('/login', fn() => view('auth.login'))->name('login');


// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Semua User Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

        // Route update yang sudah ada biarkan saja
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // Cart Management
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');

        // Add items (Logic berbeda antara Diamart & Raditya)
        Route::post('/add/diamart/{id}', [CartController::class, 'addDiamart'])->name('add.diamart');
        Route::post('/add/raditya/{id}', [CartController::class, 'addRaditya'])->name('add.raditya');

        // Common Cart Actions
        Route::post('/update/{itemId}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{itemId}', [CartController::class, 'remove'])->name('remove');
    });

    // Checkout & Payment
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout', [CartController::class, 'process'])->name('checkout.process');

    Route::controller(PaymentController::class)->prefix('payment')->name('payment.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/process', 'process')->name('process');
        Route::get('/download-csv', 'downloadCsv')->name('downloadCsv');
    });
    Route::get('/transaction/success', [TransactionController::class, 'success'])->name('transaction.success');
});


/*
|--------------------------------------------------------------------------
| Admin & Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:super_admin,admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('unit-kerja', UnitKerjaController::class);
    Route::resource('users', UserController::class);

    // QR Scan & Transactions
    Route::get('/qr-scan', fn() => view('admin.qrscan.index'))->name('admin.qr.scan.view');
    Route::post('/qr-scan/validate', [QrController::class, 'validateQr'])->name('qr.validate');
    Route::post('/qr-scan/transaction', [QrController::class, 'processTransaction'])->name('qr.transaction');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/history', [TransactionController::class, 'history'])->name('history.index');

    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'detailOrder'])->name('admin.orders.detail');

    // --- UNIT BISNIS: RADITYA (Gadget/Elektronik) ---
    Route::prefix('raditya')->name('raditya.')->group(function () {
        Route::delete('images/{image}', [RadityaProductController::class, 'destroyImage'])->name('images.destroy');
        Route::patch('images/{image}/primary', [RadityaProductController::class, 'setPrimaryImage'])->name('images.primary');
    });
    Route::resource('raditya', RadityaProductController::class)->parameters(['raditya' => 'product']);

    // --- UNIT BISNIS: DIAMART (Sembako/Minimarket) ---
    Route::prefix('diamart')->name('diamart.')->group(function () {
        Route::delete('images/{image}', [DiamartProductController::class, 'destroyImage'])->name('images.destroy');
        Route::patch('images/{image}/primary', [DiamartProductController::class, 'setPrimaryImage'])->name('images.primary');
    });
    Route::resource('diamart', DiamartProductController::class)->parameters(['diamart' => 'product']);
});

// Front-end Diamart (Customer View)
Route::prefix('diamart')->name('front.diamart.')->group(function () {
    Route::get('/', [DiamartController::class, 'index'])->name('index');
    Route::get('/product/{id}', [DiamartController::class, 'show'])->name('show');
});
