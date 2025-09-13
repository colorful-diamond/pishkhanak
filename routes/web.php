<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\TestController;
use App\Http\Controllers\Web\ContactMessageController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\GuestPaymentController;
use App\Http\Controllers\ServicePreviewController;
use App\Http\Controllers\FinnotechSmsAuthController;
use App\Http\Controllers\Services\LoanInquiryController;
use Illuminate\Support\Facades\View;

Route::get('/', [PageController::class, 'showHome'])->name('app.page.home');
Route::get('/login', [PageController::class, 'showLogin'])->name('login');
Route::get('/logout', [PageController::class, 'logout'])->name('logout');
Route::get('/about', [PageController::class, 'showAbout'])->name('app.page.about');
Route::get('/contact', [PageController::class, 'showContact'])->name('app.page.contact');
Route::get('/privacy-policy', [PageController::class, 'showPrivacyPolicy'])->name('app.page.privacy');
Route::get('/terms-conditions', [PageController::class, 'showTermsConditions'])->name('app.page.terms');
Route::post('/contact', [ContactMessageController::class, 'store'])->name('app.contact.store');
Route::get('/captcha-image', [PageController::class, 'generateCaptchaImage'])->name('captcha.image');

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/category/{category:slug}', [ServiceController::class, 'showCategory'])->name('category');
    Route::get('/result/{id}', [ServiceController::class, 'showResult'])
        ->where(['id' => '[a-zA-Z0-9]{16}'])
        ->name('result');

    // Service preview routes (must be before dynamic service routes)
    Route::get('/preview/{service}/{hash?}', [ServicePreviewController::class, 'showGuestPreview'])
        ->where('hash', 'req_[A-Z0-9]{16}')
        ->name('preview.guest');
    Route::get('/preview/{service}/user/{hash?}', [ServicePreviewController::class, 'showUserPreview'])
        ->where('hash', 'req_[A-Z0-9]{16}')
        ->name('preview.user');

    // Progress page (GET and POST for OTP submission)
    Route::match(['GET', 'POST'], '/{service}/progress/{hash}', function ($service, $hash) {
        $serviceModel = \App\Models\Service::where('slug', $service)->first();
        if (!$serviceModel) {
            abort(404);
        }

        $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($serviceModel);
        if (!$controller) {
            abort(404);
        }

        // Handle both GET (show progress) and POST (submit OTP)
        if (request()->isMethod('post')) {
            // POST request - handle OTP submission
            if (method_exists($controller, 'handleOtpSubmission')) {
                return $controller->handleOtpSubmission(request(), $serviceModel);
            } else if (method_exists($controller, 'handle')) {
                return $controller->handle(request(), $serviceModel);
            }
        } else {
            // GET request - show progress page
            if (method_exists($controller, 'showProgress')) {
                return $controller->showProgress(request(), $serviceModel, $hash);
            }
        }
        
        abort(404);
    })->where(['service' => '[a-z0-9\-]+', 'hash' => 'req_[A-Z0-9]{16}'])
        ->name('progress');

    // SMS verification page (NEW - with unique hash identifiers)
    Route::get('/{service}/sms-verify/{hash}', function ($service, $hash) {
        $serviceModel = \App\Models\Service::where('slug', $service)->first();
        if (!$serviceModel) {
            abort(404);
        }

        $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($serviceModel);
        if (!$controller || !method_exists($controller, 'showSmsVerification')) {
            abort(404);
        }

        // @phpstan-ignore-next-line
        return $controller->showSmsVerification(request(), $serviceModel, $hash);
    })->where(['service' => '[a-z0-9\-]+', 'hash' => 'sms_[A-Z0-9]{16}'])
        ->name('sms-verification');

    // SMS verification submission (NEW - with unique hash identifiers)
    Route::post('/{service}/sms-verify/{hash}', function ($service, $hash) {
        $serviceModel = \App\Models\Service::where('slug', $service)->first();
        if (!$serviceModel) {
            abort(404);
        }

                 $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($serviceModel);
         if (!$controller || !method_exists($controller, 'handleSmsOtpVerification')) {
             abort(404);
         }

         // @phpstan-ignore-next-line
         return $controller->handleSmsOtpVerification(request(), $serviceModel, $hash);
    })->where(['service' => '[a-z0-9\-]+', 'hash' => 'sms_[A-Z0-9]{16}'])
        ->name('sms-verification.submit');

    // OTP verification page (legacy support - now redirects to progress)
    Route::get('/{service}/otp/{hash}', function ($service, $hash) {
        $serviceModel = \App\Models\Service::where('slug', $service)->first();
        if (!$serviceModel) {
            abort(404);
        }

        // Redirect to progress page which now handles OTP
        return redirect()->route('services.progress', [
            'service' => $service,
            'hash' => $hash
        ]);
    })->where(['service' => '[a-z0-9\-]+', 'hash' => 'req_[A-Z0-9]{16}'])
        ->name('progress.otp');

    // Result page  
    Route::get('/{service}/result/{hash}', function ($service, $hash) {
        $serviceModel = \App\Models\Service::where('slug', $service)->first();
        if (!$serviceModel) {
            abort(404);
        }

        $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($serviceModel);
        if (!$controller || !method_exists($controller, 'show')) {
            abort(404);
        }

        return $controller->show($hash, $serviceModel);
    })->where(['service' => '[a-z0-9\-]+', 'hash' => 'req_[A-Z0-9]{16}'])
        ->name('progress.result');

    // Legacy OTP verification route (now handled by progress route)
    Route::post('/{service}/verify-otp/{hash}', function ($service, $hash) {
        // Redirect POST to the progress route which handles OTP submission
        return redirect()->route('services.progress', [
            'service' => $service,
            'hash' => $hash
        ])->withInput();
    })->where(['service' => '[a-z0-9\-]+', 'hash' => 'req_[A-Z0-9]{16}'])
        ->name('progress.verify-otp');

    Route::get('/{service}/sms-result/{id}', function ($service, $id) {
        $serviceModel = \App\Models\Service::where('slug', $service)->first();
        if (!$serviceModel) {
            abort(404);
        }

        $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($serviceModel);
        if (!$controller || !method_exists($controller, 'showSmsResult')) {
            // Fallback to regular result page
            return redirect()->route('services.result', ['id' => $id]);
        }

        // @phpstan-ignore-next-line
        return $controller->showSmsResult(request(), $serviceModel, $id);
    })->where(['service' => '[a-z0-9\-]+', 'id' => '[a-zA-Z0-9]{16}'])
        ->name('progress.sms-result');

    // Static service preview routes (before dynamic routes)
    Route::get('/{service}/preview', function ($service) {
        $serviceModel = \App\Models\Service::where('slug', $service)->where('status', 'active')->first();
        if (!$serviceModel) {
            abort(404);
        }
        
        // Check if custom preview exists
        $previewView = "front.services.custom.{$service}.preview";
        if (View::exists($previewView)) {
            return view($previewView, compact('serviceModel'));
        }
        
        abort(404);
    })->where(['service' => '[a-z0-9\-]+'])->name('static-preview');

    // Comment routes for services
    Route::prefix('{service}/comments')->name('comments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ServiceCommentController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\ServiceCommentController::class, 'store'])->name('store');
        Route::post('/{comment}/vote', [\App\Http\Controllers\ServiceCommentController::class, 'vote'])->name('vote');
        Route::post('/{comment}/report', [\App\Http\Controllers\ServiceCommentController::class, 'report'])->name('report');
        Route::get('/{comment}/replies', [\App\Http\Controllers\ServiceCommentController::class, 'replies'])->name('replies');
    });

    // Dynamic service routes (must be last)
    Route::get('/{slug1}/{slug2?}', [ServiceController::class, 'show'])
        ->where(['slug1' => '[a-z0-9\-]+', 'slug2' => '[a-z0-9\-]+'])
        ->name('show');
    Route::post('/{slug1}/{slug2?}', [ServiceController::class, 'submit'])
        ->where(['slug1' => '[a-z0-9\-]+', 'slug2' => '[a-z0-9\-]+'])
        ->name('submit');
});

Route::prefix('blog')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('app.blog.index');
    Route::get('/search', [BlogController::class, 'search'])->name('app.blog.search');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('app.blog.category');
    Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('app.blog.tag');
    Route::get('/{post}', [BlogController::class, 'show'])->name('app.blog.show');
    Route::post('/{post}/comment', [BlogController::class, 'storeComment'])->name('app.blog.comment.store');
});

// Payment Gateway Web Routes
Route::prefix('payment')->name('payment.')->group(function () {
    // Public routes (for webhooks and callbacks)
    Route::post('webhook/{gateway}', [WebhookController::class, 'handleWebhook'])->name('webhook')->withoutMiddleware('web');
    Route::match(['get', 'post'], 'callback/{gateway}/{transaction?}', [PaymentController::class, 'handleCallback'])->name('callback')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    Route::get('health', [WebhookController::class, 'healthCheck'])->name('health');

    // Protected routes (require authentication)
    Route::middleware('auth')->group(function () {
        Route::post('initialize', [PaymentController::class, 'initializePayment'])->name('initialize');
        Route::get('status/{transactionId}', [PaymentController::class, 'checkStatus'])->name('status');
        Route::post('refund/{transactionId}', [PaymentController::class, 'processRefund'])->name('refund');

        // AJAX routes
        Route::get('gateways', [PaymentController::class, 'getAvailableGateways'])->name('gateways');
    });
});

// Payment System Routes (for backward compatibility and UI)
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('form', [PaymentController::class, 'showPaymentForm'])->name('form');
    Route::post('create', [PaymentController::class, 'createPayment'])->name('create');
    Route::get('success/{transactionId}', [PaymentController::class, 'showSuccess'])->name('success');
    Route::get('failed/{transactionId}', [PaymentController::class, 'showFailed'])->name('failed');
    // Only protect receipt for logged-in users
    Route::middleware('auth')->group(function () {
        Route::get('status/{transactionId}', [PaymentController::class, 'showStatus'])->name('status');
        Route::get('receipt/{transactionId}', [PaymentController::class, 'downloadReceipt'])->name('receipt');
        Route::post('refund/{transactionId}', [PaymentController::class, 'processRefund'])->name('refund');
    });
});

// Guest Payment Routes
Route::prefix('guest')->name('guest.')->group(function () {
    Route::prefix('payment')->name('payment.')->group(function () {
        // Show guest charge page
        Route::get('/charge/{service}', [GuestPaymentController::class, 'showChargePage'])
            ->name('charge.show');

        // Process guest payment
        Route::post('/charge', [GuestPaymentController::class, 'processCharge'])
            ->name('charge');

        // Guest payment callback
        Route::match(['get', 'post'], '/callback/{gateway}/{transaction?}', [GuestPaymentController::class, 'handleCallback'])
            ->name('callback')
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // Phone verification after payment
        Route::get('/verify-phone', [GuestPaymentController::class, 'showPhoneVerification'])
            ->name('verify.phone');
        Route::post('/send-verification', [GuestPaymentController::class, 'sendPhoneVerification'])
            ->name('send.verification');
        Route::post('/verify-otp', [GuestPaymentController::class, 'verifyPhoneOtp'])
            ->name('verify.otp');
    });
});

// Finnotech SMS Authorization Routes
Route::prefix('finnotech/sms-auth')->name('finnotech.sms-auth.')->group(function () {
    Route::get('/start', [FinnotechSmsAuthController::class, 'startSmsAuth'])->name('start');
    Route::get('/callback', [FinnotechSmsAuthController::class, 'handleSmsAuthCallback'])->name('callback');
    Route::get('/otp-verification', [FinnotechSmsAuthController::class, 'showOtpVerification'])->name('otp.show');
    Route::post('/otp-verification', [FinnotechSmsAuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/check-token', [FinnotechSmsAuthController::class, 'checkTokenStatus'])->name('check-token');
});
