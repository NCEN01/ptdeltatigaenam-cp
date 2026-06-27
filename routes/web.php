<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ServiceController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// Root → default locale
Route::get('/', fn () => redirect('/'.SetLocale::DEFAULT));

// Midtrans webhook (no locale, CSRF-exempt — see bootstrap/app.php). Filled in Stage 10.
// Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle']);

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

        Route::get('/kemitraan', [PartnershipController::class, 'index'])->name('partnership.index');
        Route::post('/kemitraan/daftar', [PartnershipController::class, 'store'])->name('partnership.store');

        Route::get('/kontak', [ContactController::class, 'index'])->name('contact.index');
        Route::post('/kontak', [ContactController::class, 'store'])->name('contact.store');
    });
