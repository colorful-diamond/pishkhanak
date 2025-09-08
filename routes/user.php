<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\UserDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Models\User;

Route::prefix('user')->middleware('require.auth')->group(function () {
    // Login routes (with guest middleware to redirect if already logged in)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('app.auth.login');
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('auth.rate.limit:login')
            ->name('app.auth.login.submit');
        
        // Register
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('app.auth.register');
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('auth.rate.limit:register')
            ->name('app.auth.register.submit');

        // OTP Routes
        Route::post('/send-otp', [AuthController::class, 'sendOtp'])
            ->middleware('auth.rate.limit:otp')
            ->name('app.auth.send-otp');
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])
            ->middleware('auth.rate.limit:verify-otp')
            ->name('app.auth.verify-otp');
        Route::post('/register-without-otp', [AuthController::class, 'registerWithoutOtp'])
            ->middleware('auth.rate.limit:register')
            ->name('app.auth.register-without-otp');
        
        // Password Reset
        Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('app.password.request');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
            ->middleware('auth.rate.limit:password-reset')
            ->name('app.password.email');
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('app.password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->middleware('auth.rate.limit:password-reset')
            ->name('app.password.update');
    });
    
    // Authenticated routes (require authentication)
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('app.auth.logout');
        
        // User pages (require authentication)
        Route::get('/history', [UserController::class, 'history'])->name('app.user.history');
        Route::get('/wallet', [UserController::class, 'wallet'])->name('app.user.wallet');
        
        // Dashboard routes
        Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('app.user.dashboard');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('app.user.profile');
        Route::match(['GET', 'POST'], '/profile', [UserDashboardController::class, 'updateProfile'])->name('app.user.profile.update');
        Route::post('/profile/password', [UserDashboardController::class, 'updatePassword'])->name('app.user.profile.password');
        
        // Mobile verification routes
        Route::post('/profile/mobile/request-otp', [UserDashboardController::class, 'requestMobileOtp'])->name('app.user.profile.mobile.otp');
        Route::post('/profile/mobile/verify-otp', [UserDashboardController::class, 'verifyMobileOtp'])->name('app.user.profile.mobile.verify');
        
        // Tickets
        Route::get('/tickets', [UserDashboardController::class, 'tickets'])->name('app.user.tickets.index');
        Route::get('/tickets/create', [UserDashboardController::class, 'createTicket'])->name('app.user.tickets.create');
        Route::post('/tickets', [UserDashboardController::class, 'storeTicket'])->name('app.user.tickets.store');
        Route::get('/tickets/{ticket:ticket_hash}', [UserDashboardController::class, 'showTicket'])->name('app.user.tickets.show');
        Route::post('/tickets/{ticket:ticket_hash}/messages', [UserDashboardController::class, 'addMessage'])->name('app.user.tickets.messages.store');
        Route::post('/tickets/{ticket:ticket_hash}/close', [UserDashboardController::class, 'closeTicket'])->name('app.user.tickets.close');
        Route::get('/tickets/attachments/{attachment}/download', [UserDashboardController::class, 'downloadAttachment'])->name('app.user.tickets.attachment.download');
        
        // Wallet refund route - requires authentication
        Route::post('/wallet/refund', [UserController::class, 'refundWallet'])->name('app.user.wallet.refund');
    });
});

// Wallet charge route - available for both authenticated and non-authenticated users
Route::post('/user/wallet/charge', [UserController::class, 'chargeWallet'])->name('app.user.wallet.charge');
