<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    CategoryController,
    ProductController, // Jika dipakai untuk parent class
    BrandController,
    CartController,
    DiamartController,         // Public Controller Diamart
    DiamartProductController,  // Admin Controller Diamart
    MinimarketController,      // Alias lain untuk Diamart? (Sesuaikan jika perlu)
    PaymentController,
    ProfileController,
    QrController,
    RadityaController,         // Public Controller Raditya
    RadityaProductController,  // Admin Controller Raditya
    TransactionController,     // Transaksi Umum/Cash
    UnitKerjaController,
    UserController,
    WelcomeController,
    AdminOrderController,
    CreditTransactionController, // Transaksi Kredit, // Transaksi Kredit
    TransactionReportController
};

/*
|--------------------------------------------------------------------------
| Public Routes (Bisa Diakses Tanpa Login)
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'home'])->name('home');

// --- UNIT BISNIS: GADGET (Raditya) ---
Route::get('/gadget', [RadityaController::class, 'index'])->name('gadget.index');
Route::get('/gadget/{product}', [RadityaController::class, 'show'])->name('gadget.show');

// --- UNIT BISNIS: MINIMARKET (Diamart) ---
Route::get('/minimarket', [MinimarketController::class, 'index'])->name('minimarket.index');
Route::get('/minimarket/{id}', [MinimarketController::class, 'show'])->name('minimarket.show');
// Front-end Diamart tambahan
Route::prefix('diamart')->name('front.diamart.')->group(function () {
    Route::get('/', [DiamartController::class, 'index'])->name('index');
    Route::get('/product/{id}', [DiamartController::class, 'show'])->name('show');
});

// --- AUTHENTICATION ---
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // --- PROFILE MANAGEMENT ---
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/', 'updateProfile')->name('update');
        Route::put('/password', 'updatePassword')->name('password');
    });

    // --- CART SYSTEM ---
    Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add', 'addToCart')->name('add'); // Penting untuk tombol "Beli"
        Route::post('/update/{itemId}', 'update')->name('update');
        Route::delete('/remove/{itemId}', 'remove')->name('remove');
    });

    // --- CHECKOUT & PAYMENT ---
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout', [CartController::class, 'process'])->name('checkout.process');

    Route::controller(PaymentController::class)->prefix('payment')->name('payment.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/process', 'process')->name('process');
        Route::get('/download-csv', 'downloadCsv')->name('downloadCsv');
    });

    Route::get('/transaction/success', [TransactionController::class, 'success'])->name('transaction.success');
    Route::get('/transactions/{id}/print', [TransactionController::class, 'printInvoice'])->name('transactions.print_invoice');

    // --- FITUR KREDIT (USER SIDE) ---
    // 1. AJAX Simulasi (Hitung cicilan di halaman produk)
    Route::post('/ajax/credit-simulation', [RadityaProductController::class, 'simulateCredit'])
        ->name('ajax.credit.simulate');

    // 2. Submit Pengajuan Kredit
    Route::post('/credit-transactions', [CreditTransactionController::class, 'store'])
        ->name('credit.store');
});


/*
|--------------------------------------------------------------------------
| Admin & Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.role:super_admin,admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- MASTER DATA ---
    Route::resources([
        'categories' => CategoryController::class,
        'brands'     => BrandController::class,
        'unit-kerja' => UnitKerjaController::class,
        'users'      => UserController::class,
    ]);

    // --- 1. RIWAYAT TRANSAKSI UMUM (CASH) ---
    // Menu: "Riwayat Transaksi"
    Route::controller(TransactionController::class)->prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', 'index')->name('index');     // List Transaksi Cash
        Route::get('/history', 'history')->name('history'); // History Log
    });

    // --- 2. TANGGUNGAN TENOR (KREDIT) ---
    // Menu: "Tanggungan Tenor"
    // PERBAIKAN: Menggunakan prefix 'credits' agar nama route beda dengan transaksi umum
    Route::resource('credits', CreditTransactionController::class)
        ->parameters(['credits' => 'creditTransaction']) // Agar parameter di URL enak dibaca
        ->names([
            'index' => 'credits.index', // admin.credits.index
            'show'  => 'credits.show',  // admin.credits.show
            'store' => 'credits.store', // admin.credits.store
        ]);

    // --- ORDER MANAGEMENT ---
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'detailOrder'])->name('admin.orders.detail');

    // --- QR SCAN ---
    Route::controller(QrController::class)->prefix('qr-scan')->name('admin.qr.')->group(function () {
        Route::get('/', fn() => view('admin.qrscan.index'))->name('scan.view');
        Route::post('/validate', 'validateQr')->name('validate');
        Route::post('/transaction', 'processTransaction')->name('transaction');
    });

    // --- MANAJEMEN PRODUK RADITYA ---
    Route::prefix('raditya')->name('raditya.')->group(function () {
        Route::delete('images/{image}', [RadityaProductController::class, 'destroyImage'])->name('images.destroy');
        Route::patch('images/{image}/primary', [RadityaProductController::class, 'setPrimaryImage'])->name('images.primary');
    });
    Route::resource('raditya', RadityaProductController::class)->parameters(['raditya' => 'product']);

    // --- MANAJEMEN PRODUK DIAMART ---
    Route::prefix('diamart')->name('diamart.')->group(function () {
        Route::delete('images/{image}', [DiamartProductController::class, 'destroyImage'])->name('images.destroy');
        Route::patch('images/{image}/primary', [DiamartProductController::class, 'setPrimaryImage'])->name('images.primary');
        Route::post('/bulk-action', [DiamartController::class, 'bulkAction'])->name('bulk-action');
    });
    Route::resource('diamart', DiamartProductController::class)->parameters(['diamart' => 'product']);

    // --- LAPORAN TRANSAKSI ---
    Route::get('/reports/monthly', [TransactionReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/pdf/{user}/{bulan}/{tahun}', [TransactionReportController::class, 'downloadPdf'])->name('report.pdf');
});
