<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Auth\CustomerEmailVerificationController;
use App\Http\Controllers\Auth\CustomerPasswordController;
use App\Http\Controllers\Auth\CustomerSessionController;
use App\Http\Controllers\Auth\RegisteredCustomerController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ServiceController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// Root → default locale
Route::get('/', fn () => redirect('/'.SetLocale::DEFAULT));

// SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Midtrans webhook (no locale prefix, CSRF-exempt — see bootstrap/app.php).
Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])->name('midtrans.callback');

Route::prefix('{locale}')
    ->where(['locale' => 'id|en'])
    ->middleware('locale')
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/about', [PageController::class, 'about'])->name('about');

        Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/layanan/{slug}', [ServiceController::class, 'show'])->name('services.show');

        Route::get('/portofolio', [PortfolioController::class, 'index'])->name('portfolio.index');
        Route::get('/portofolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

        Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');

        Route::get('/kemitraan', [PartnershipController::class, 'index'])->name('partnership.index');
        Route::post('/kemitraan/daftar', [PartnershipController::class, 'store'])->name('partnership.store');

        Route::get('/kontak', [ContactController::class, 'index'])->name('contact.index');
        Route::post('/kontak', [ContactController::class, 'store'])->name('contact.store');

        /* ---------- Customer auth (guard: customer) ---------- */
        Route::middleware('guest:customer')->group(function () {
            Route::get('/daftar', [RegisteredCustomerController::class, 'show'])->name('register');
            Route::post('/daftar', [RegisteredCustomerController::class, 'store']);
            Route::get('/masuk', [CustomerSessionController::class, 'show'])->name('login');
            Route::post('/masuk', [CustomerSessionController::class, 'store']);
            Route::get('/lupa-password', [CustomerPasswordController::class, 'request'])->name('password.request');
            Route::post('/lupa-password', [CustomerPasswordController::class, 'email'])->name('password.email');
            Route::get('/reset-password/{token}', [CustomerPasswordController::class, 'reset'])->name('password.reset');
            Route::post('/reset-password', [CustomerPasswordController::class, 'update'])->name('password.update');
        });

        Route::post('/keluar', [CustomerSessionController::class, 'destroy'])
            ->middleware('auth:customer')->name('logout');

        // Email verification
        Route::get('/verifikasi-email/{id}/{hash}', [CustomerEmailVerificationController::class, 'verify'])
            ->middleware('signed')->name('verification.verify');

        Route::middleware('auth:customer')->group(function () {
            Route::get('/verifikasi-email', [CustomerEmailVerificationController::class, 'notice'])->name('verification.notice');
            Route::post('/verifikasi-email/kirim-ulang', [CustomerEmailVerificationController::class, 'resend'])
                ->middleware('throttle:6,1')->name('verification.resend');

            Route::middleware('verified.customer')->group(function () {
                Route::get('/akun', [AccountController::class, 'profile'])->name('account.profile');
                Route::patch('/akun', [AccountController::class, 'update'])->name('account.update');
                Route::get('/akun/pesanan', [AccountController::class, 'orders'])->name('account.orders');

                // Checkout (requires verified customer)
                Route::get('/checkout/{schedule}', [CheckoutController::class, 'create'])->name('checkout.create');
                Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
                Route::get('/checkout/{order:order_number}/bayar', [CheckoutController::class, 'pay'])->name('checkout.pay');
                Route::get('/checkout/{order:order_number}/status', [CheckoutController::class, 'finish'])->name('checkout.finish');
            });
        });
    });
