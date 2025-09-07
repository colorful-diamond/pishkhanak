<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Otp;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Exception;

class AuthController extends Controller
{
    private SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
        
        // Rate limiting middleware
        $this->middleware('throttle:5,1')->only(['sendOtp', 'verifyOtp']);
        $this->middleware('throttle:10,1')->only(['login']);
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'string', 'max:15', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => Otp::formatMobile($request->mobile),
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            Auth::login($user);

            Log::info('User registered successfully', ['user_id' => $user->id]);

            return redirect()->route('verification.notice');
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'خطایی در ثبت نام رخ داده است. لطفاً مجدداً تلاش کنید.',
            ])->withInput($request->only('name', 'email', 'mobile'));
        }
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle unified login (traditional email/password and mobile-based)
     */
    public function login(Request $request)
    {
        try {
            // Check if this is a mobile-based smart login
            if ($request->has('mobile') && $request->has('step')) {
                return $this->handleSmartLogin($request);
            }
            
            // Traditional email/password login
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $credentials = $request->only('email', 'password');
            $remember = $request->filled('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                Log::info('Login successful', ['user_id' => Auth::id()]);
                
                return redirect()->intended(route('app.page.home'))->with('status', 'با موفقیت وارد شدید!');
            }

            Log::warning('Login failed', ['email' => $request->email]);
            
            throw ValidationException::withMessages([
                'email' => ['نام کاربری یا رمز عبور اشتباه است.'],
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput($request->only('email'));
        } catch (Exception $e) {
            Log::error('Login error', ['message' => $e->getMessage()]);
            return back()->withErrors([
                'error' => 'خطای غیرمنتظره‌ای رخ داده است. لطفاً مجدداً تلاش کنید.',
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Handle smart mobile-based login
     */
    private function handleSmartLogin(Request $request)
    {
        $step = $request->get('step');
        
        switch ($step) {
            case 'check_mobile':
                return $this->checkMobileUser($request);
            case 'password_login':
                return $this->loginWithPassword($request);
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'نوع درخواست نامعتبر است',
                    'code' => 'INVALID_STEP'
                ], 400);
        }
    }

    /**
     * Check mobile user and determine login method
     */
    private function checkMobileUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Otp::isValidIranianMobile($value)) {
                    $fail('شماره موبایل نامعتبر است.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mobile = Otp::formatMobile($request->mobile);
            $user = User::where('mobile', $mobile)->first();

            // If user doesn't exist, start registration process
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'action' => 'register',
                    'message' => 'کاربری با این شماره موبایل یافت نشد. آیا می‌خواهید ثبت نام کنید؟',
                    'code' => 'USER_NOT_FOUND'
                ]);
            }

            // If user has password, offer password option
            if ($user->password) {
                return response()->json([
                    'success' => false,
                    'action' => 'password_required',
                    'message' => 'لطفاً رمز عبور خود را وارد کنید یا از طریق کد تأیید وارد شوید',
                    'code' => 'PASSWORD_REQUIRED'
                ]);
            }

            // User doesn't have password, proceed with SMS verification
            return response()->json([
                'success' => false,
                'action' => 'sms_required',
                'message' => 'کد تأیید به شماره شما ارسال می‌شود',
                'code' => 'SMS_REQUIRED'
            ]);

        } catch (Exception $e) {
            Log::error('Mobile check error', [
                'mobile' => $request->mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطای سیستمی رخ داده است',
                'code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Login with mobile and password
     */
    private function loginWithPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Otp::isValidIranianMobile($value)) {
                    $fail('شماره موبایل نامعتبر است.');
                }
            }],
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mobile = Otp::formatMobile($request->mobile);
            $user = User::where('mobile', $mobile)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'کاربری با این شماره موبایل یافت نشد',
                    'code' => 'USER_NOT_FOUND'
                ], 422);
            }

            if (!$user->password || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'رمز عبور اشتباه است',
                    'code' => 'WRONG_PASSWORD'
                ], 422);
            }

            Auth::login($user, $request->filled('remember'));
            
            Log::info('Password login successful', ['user_id' => $user->id]);
            
            // Process pending payments and service continuations
            $finalRedirect = route('app.page.home');
            $finalMessage = 'با موفقیت وارد شدید';
            $serviceProcessed = false;
            $paymentProcessed = false;

            // Check for pending wallet charge (handles service continuation automatically)
            $walletChargeResult = $this->processPendingWalletCharge($user);
            
            // Check for pending guest payment processing
            $guestPaymentResult = null;
            if (Session::has('guest_payment_success') && Session::has('guest_payment_transaction_id')) {
                $guestPaymentController = app(\App\Http\Controllers\GuestPaymentController::class);
                $guestPaymentResult = $guestPaymentController->processGuestPaymentAfterLogin($user);
            }
            
            // Priority: Guest payment > Wallet charge > Default
            if ($guestPaymentResult && $guestPaymentResult['success']) {
                $finalRedirect = $guestPaymentResult['redirect'] ?? $finalRedirect;
                $finalMessage = $guestPaymentResult['message'] ?? $finalMessage;
                $serviceProcessed = $guestPaymentResult['service_processed'] ?? false;
                $paymentProcessed = true;
                
                Log::info('Guest payment processed after password login', [
                    'user_id' => $user->id,
                    'success' => true,
                    'service_processed' => $serviceProcessed,
                    'redirect' => $finalRedirect
                ]);
            } elseif ($walletChargeResult && $walletChargeResult['success']) {
                $finalRedirect = $walletChargeResult['redirect'] ?? $finalRedirect;
                $finalMessage = $walletChargeResult['message'] ?? $finalMessage;
                $serviceProcessed = $walletChargeResult['service_processed'] ?? false;
                $paymentProcessed = true;
                
                Log::info('Wallet charge processed after password login', [
                    'user_id' => $user->id,
                    'success' => true,
                    'service_processed' => $serviceProcessed,
                    'redirect' => $finalRedirect
                ]);
            } elseif ($guestPaymentResult && !$guestPaymentResult['success']) {
                // Guest payment failed
                $finalMessage = $guestPaymentResult['message'] ?? 'خطا در پردازش پرداخت مهمان';
                $paymentProcessed = true;
                
                Log::error('Guest payment processing failed after password login', [
                    'user_id' => $user->id,
                    'error' => $finalMessage
                ]);
            } elseif ($walletChargeResult && !$walletChargeResult['success']) {
                // Wallet charge failed
                $finalMessage = $walletChargeResult['message'] ?? 'خطا در پردازش شارژ کیف پول';
                $paymentProcessed = true;
                
                Log::error('Wallet charge processing failed after password login', [
                    'user_id' => $user->id,
                    'error' => $finalMessage
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => $finalMessage,
                'redirect' => $finalRedirect,
                'payment_processed' => $paymentProcessed,
                'service_processed' => $serviceProcessed,
                'guest_payment_processed' => $guestPaymentResult ? $guestPaymentResult['success'] : false,
                'wallet_charge_processed' => $walletChargeResult ? $walletChargeResult['success'] : false
            ]);

        } catch (Exception $e) {
            Log::error('Password login error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'خطای غیرمنتظره‌ای رخ داده است. لطفاً مجدداً تلاش کنید.',
                'code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }



    /**
     * Send OTP via SMS
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Otp::isValidIranianMobile($value)) {
                    $fail('شماره موبایل نامعتبر است.');
                }
            }],
            'type' => 'sometimes|in:login,register,password_reset'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mobile = $request->mobile;
            $type = $request->type ?? Otp::TYPE_LOGIN;
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
            $formattedMobile = Otp::formatMobile($mobile);

            // Check if user exists
            $userExists = User::where('mobile', $formattedMobile)->exists();

            // Track login attempts for non-existent users
            if (!$userExists) {
                $attemptKey = 'login_attempt_nonexistent:' . $formattedMobile;
                $attempts = cache()->increment($attemptKey, 1);
                cache()->put($attemptKey, $attempts, now()->addHours(1));
                
                // If more than 3 attempts for non-existent user, allow registration without OTP
                if ($attempts > 3) {
                    // Check if user exists and has wallet balance
                    $existingUser = User::where('mobile', $formattedMobile)->first();
                    $hasWalletBalance = false;
                    
                    if ($existingUser && $existingUser->wallet) {
                        $hasWalletBalance = $existingUser->wallet->balance > 0;
                    }
                    
                    // Only auto-login if user doesn't exist OR has no wallet balance
                    if (!$existingUser || !$hasWalletBalance) {
                        Log::warning('Multiple attempts for non-existent user, auto-logging in', [
                            'mobile' => $formattedMobile,
                            'attempts' => $attempts,
                            'user_exists' => $existingUser ? true : false,
                            'wallet_balance' => $existingUser && $existingUser->wallet ? $existingUser->wallet->balance : 0,
                            'ip' => $request->ip()
                        ]);
                        
                        // Automatically create/login user without OTP
                        $user = User::firstOrCreate(
                            ['mobile' => $formattedMobile],
                            [
                                'name' => 'کاربر ' . substr($formattedMobile, -4),
                                'email' => $formattedMobile . '@mobile.pishkhanak.com',
                                'mobile_verified_at' => now(),
                            ]
                        );
                        
                        // Update mobile verification if not set
                        if (!$user->mobile_verified_at) {
                            $user->update(['mobile_verified_at' => now()]);
                        }
                        
                        // Log the user in
                        Auth::login($user, true);
                        
                        Log::info('User auto-logged in due to multiple attempts', [
                            'user_id' => $user->id,
                            'mobile' => $formattedMobile,
                            'reason' => 'multiple_attempts_nonexistent'
                        ]);
                        
                        // Process pending payments and service continuations
                        $finalRedirect = route('app.page.home');
                        $finalMessage = 'با موفقیت وارد شدید';
                        
                        // Check for pending wallet charge
                        $walletChargeResult = $this->processPendingWalletCharge($user);
                        
                        // Check for pending guest payment processing
                        $guestPaymentResult = null;
                        if (Session::has('guest_payment_success') && Session::has('guest_payment_transaction_id')) {
                            $guestPaymentController = app(\App\Http\Controllers\GuestPaymentController::class);
                            $guestPaymentResult = $guestPaymentController->processGuestPaymentAfterLogin($user);
                        }
                        
                        // Determine final redirect and message
                        if ($guestPaymentResult && $guestPaymentResult['success']) {
                            $finalRedirect = $guestPaymentResult['redirect'] ?? $finalRedirect;
                            $finalMessage = $guestPaymentResult['message'] ?? $finalMessage;
                        } elseif ($walletChargeResult && $walletChargeResult['success']) {
                            $finalRedirect = $walletChargeResult['redirect'] ?? $finalRedirect;
                            $finalMessage = $walletChargeResult['message'] ?? $finalMessage;
                        }
                        
                        return response()->json([
                            'success' => true,
                            'auto_login' => true,
                            'message' => $finalMessage,
                            'redirect' => $finalRedirect,
                            'code' => 'AUTO_LOGIN_MULTIPLE_ATTEMPTS'
                        ]);
                    }
                }
            }

            // Rate limiting check
            $key = 'otp_request:' . $request->ip() . ':' . $mobile;
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => "تعداد تلاش بیش از حد مجاز. لطفاً {$seconds} ثانیه صبر کنید.",
                    'code' => 'RATE_LIMITED',
                    'retry_after' => $seconds
                ], 429);
            }

            RateLimiter::hit($key, 60); // 1 minute

            // Send OTP
            $result = $this->smsService->sendOtpWithRecord($mobile, $type, $ipAddress, $userAgent);

            // Handle SMS failure - automatically login/register user
            if (!$result['success'] && ($result['code'] === 'SMS_FAILED' || $result['code'] === 'SYSTEM_ERROR')) {
                // Track SMS failures
                $failureKey = 'sms_failure:' . $formattedMobile;
                $failures = cache()->increment($failureKey, 1);
                cache()->put($failureKey, $failures, now()->addMinutes(30));
                
                // Check if user exists and has wallet balance
                $existingUser = User::where('mobile', $formattedMobile)->first();
                $hasWalletBalance = false;
                
                if ($existingUser && $existingUser->wallet) {
                    $hasWalletBalance = $existingUser->wallet->balance > 0;
                }
                
                // Only auto-login if user doesn't exist OR has no wallet balance
                if (!$existingUser || !$hasWalletBalance) {
                    Log::warning('SMS failed, auto-logging in user (no wallet or empty wallet)', [
                        'mobile' => $formattedMobile,
                        'user_exists' => $existingUser ? true : false,
                        'wallet_balance' => $existingUser && $existingUser->wallet ? $existingUser->wallet->balance : 0,
                        'ip' => $request->ip()
                    ]);
                    
                    // Automatically create/login user without OTP
                    $user = User::firstOrCreate(
                        ['mobile' => $formattedMobile],
                        [
                            'name' => 'کاربر ' . substr($formattedMobile, -4),
                            'email' => $formattedMobile . '@mobile.pishkhanak.com',
                            'mobile_verified_at' => now(),
                        ]
                    );
                    
                    // Update mobile verification if not set
                    if (!$user->mobile_verified_at) {
                        $user->update(['mobile_verified_at' => now()]);
                    }
                    
                    // Log the user in
                    Auth::login($user, true);
                    
                    Log::info('User auto-logged in due to SMS failure', [
                        'user_id' => $user->id,
                        'mobile' => $formattedMobile,
                        'reason' => 'sms_failure_no_wallet'
                    ]);
                    
                    // Process pending payments and service continuations
                    $finalRedirect = route('app.page.home');
                    $finalMessage = 'با موفقیت وارد شدید';
                    
                    // Check for pending wallet charge
                    $walletChargeResult = $this->processPendingWalletCharge($user);
                    
                    // Check for pending guest payment processing
                    $guestPaymentResult = null;
                    if (Session::has('guest_payment_success') && Session::has('guest_payment_transaction_id')) {
                        $guestPaymentController = app(\App\Http\Controllers\GuestPaymentController::class);
                        $guestPaymentResult = $guestPaymentController->processGuestPaymentAfterLogin($user);
                    }
                    
                    // Determine final redirect and message
                    if ($guestPaymentResult && $guestPaymentResult['success']) {
                        $finalRedirect = $guestPaymentResult['redirect'] ?? $finalRedirect;
                        $finalMessage = $guestPaymentResult['message'] ?? $finalMessage;
                    } elseif ($walletChargeResult && $walletChargeResult['success']) {
                        $finalRedirect = $walletChargeResult['redirect'] ?? $finalRedirect;
                        $finalMessage = $walletChargeResult['message'] ?? $finalMessage;
                    }
                    
                    return response()->json([
                        'success' => true,
                        'auto_login' => true,
                        'message' => $finalMessage,
                        'redirect' => $finalRedirect,
                        'code' => 'AUTO_LOGIN_SMS_FAILED'
                    ]);
                } else {
                    // User has wallet balance, still require OTP (set bypass for fallback)
                    Session::put('bypass_otp_mobile', $formattedMobile);
                    Session::put('bypass_otp_reason', 'sms_failure_with_wallet');
                    
                    Log::warning('SMS failed but user has wallet balance, showing OTP with bypass', [
                        'mobile' => $formattedMobile,
                        'wallet_balance' => $existingUser->wallet->balance,
                        'ip' => $request->ip()
                    ]);
                    
                    // Show normal OTP flow but with bypass enabled
                    return response()->json([
                        'success' => true,
                        'message' => 'کد تأیید به شماره شما ارسال شد',
                        'expires_in' => Otp::EXPIRY_MINUTES * 60,
                        'code' => 'OTP_SENT'
                    ]);
                }
            }

            if ($result['success']) {
                Log::info('OTP sent successfully', [
                    'mobile' => $mobile,
                    'type' => $type,
                    'ip' => $ipAddress
                ]);
            }

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Send OTP error', [
                'mobile' => $request->mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطای سیستمی رخ داده است',
                'code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Verify OTP and log in user
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Otp::isValidIranianMobile($value)) {
                    $fail('شماره موبایل نامعتبر است.');
                }
            }],
            'code' => 'required|string|size:5',
            'type' => 'sometimes|in:login,register,password_reset'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mobile = $request->mobile;
            $code = $request->code;
            $type = $request->type ?? Otp::TYPE_LOGIN;
            $formattedMobile = Otp::formatMobile($mobile);
            
            // Check if OTP bypass is allowed for this mobile number
            $bypassAllowed = false;
            $bypassReason = null;
            
            if (Session::get('bypass_otp_mobile') === $formattedMobile) {
                $bypassAllowed = true;
                $bypassReason = Session::get('bypass_otp_reason');
                
                // When bypass is allowed due to SMS failure, accept any OTP code
                Log::info('OTP bypass used - accepting any code due to SMS failure', [
                    'mobile' => $formattedMobile,
                    'reason' => $bypassReason,
                    'ip' => $request->ip(),
                    'provided_code' => $code
                ]);
                
                // Clear bypass session
                Session::forget('bypass_otp_mobile');
                Session::forget('bypass_otp_reason');
                
                // Mark as successful verification regardless of code
                $result = ['success' => true, 'code' => 'OTP_BYPASSED'];
            }

            // Rate limiting for verification attempts
            $key = 'otp_verify:' . $request->ip() . ':' . $mobile;
            if (RateLimiter::tooManyAttempts($key, 10)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => "تعداد تلاش بیش از حد مجاز. لطفاً {$seconds} ثانیه صبر کنید.",
                    'code' => 'RATE_LIMITED',
                    'retry_after' => $seconds
                ], 429);
            }

            RateLimiter::hit($key, 60); // 1 minute

            // Verify OTP (skip if bypass was already applied)
            if (!$bypassAllowed) {
                $result = $this->smsService->verifyOtp($mobile, $code, $type);
            }

            if ($result['success']) {
                
                // Find or create user
                $user = User::firstOrCreate(
                    ['mobile' => $formattedMobile],
                    [
                        'name' => 'کاربر ' . substr($formattedMobile, -4),
                        'email' => $formattedMobile . '@mobile.pishkhanak.com', // Provide default email for mobile users
                        'mobile_verified_at' => now(),
                    ]
                );

                // Update mobile verification if not set
                if (!$user->mobile_verified_at) {
                    $user->update(['mobile_verified_at' => now()]);
                }

                // Log the user in
                Auth::login($user, true);

                Log::info('OTP verification successful', [
                    'user_id' => $user->id,
                    'mobile' => $mobile,
                    'type' => $type
                ]);

                // Clear rate limiting on successful verification
                RateLimiter::clear($key);

                // Process pending payments and service continuations
                $finalRedirect = route('app.page.home');
                $finalMessage = 'کد تایید درست است. با موفقیت وارد شدید';
                $serviceProcessed = false;
                $paymentProcessed = false;

                // Check for pending wallet charge (handles service continuation automatically)
                $walletChargeResult = $this->processPendingWalletCharge($user);
                
                // Check for pending guest payment processing
                $guestPaymentResult = null;
                if (Session::has('guest_payment_success') && Session::has('guest_payment_transaction_id')) {
                    $guestPaymentController = app(\App\Http\Controllers\GuestPaymentController::class);
                    $guestPaymentResult = $guestPaymentController->processGuestPaymentAfterLogin($user);
                }
                
                // Priority: Guest payment > Wallet charge > Default
                if ($guestPaymentResult && $guestPaymentResult['success']) {
                    $finalRedirect = $guestPaymentResult['redirect'] ?? $finalRedirect;
                    $finalMessage = $guestPaymentResult['message'] ?? $finalMessage;
                    $serviceProcessed = $guestPaymentResult['service_processed'] ?? false;
                    $paymentProcessed = true;
                    
                    Log::info('Guest payment processed after OTP login', [
                        'user_id' => $user->id,
                        'success' => true,
                        'service_processed' => $serviceProcessed,
                        'redirect' => $finalRedirect
                    ]);
                } elseif ($walletChargeResult && $walletChargeResult['success']) {
                    $finalRedirect = $walletChargeResult['redirect'] ?? $finalRedirect;
                    $finalMessage = $walletChargeResult['message'] ?? $finalMessage;
                    $serviceProcessed = $walletChargeResult['service_processed'] ?? false;
                    $paymentProcessed = true;
                    
                    Log::info('Wallet charge processed after OTP login', [
                        'user_id' => $user->id,
                        'success' => true,
                        'service_processed' => $serviceProcessed,
                        'redirect' => $finalRedirect
                    ]);
                } elseif ($guestPaymentResult && !$guestPaymentResult['success']) {
                    // Guest payment failed
                    $finalMessage = $guestPaymentResult['message'] ?? 'خطا در پردازش پرداخت مهمان';
                    $paymentProcessed = true;
                    
                    Log::error('Guest payment processing failed after OTP login', [
                        'user_id' => $user->id,
                        'error' => $finalMessage
                    ]);
                } elseif ($walletChargeResult && !$walletChargeResult['success']) {
                    // Wallet charge failed
                    $finalMessage = $walletChargeResult['message'] ?? 'خطا در پردازش شارژ کیف پول';
                    $paymentProcessed = true;
                    
                    Log::error('Wallet charge processing failed after OTP login', [
                        'user_id' => $user->id,
                        'error' => $finalMessage
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $finalMessage,
                    'redirect' => $finalRedirect,
                    'payment_processed' => $paymentProcessed,
                    'service_processed' => $serviceProcessed,
                    'guest_payment_processed' => $guestPaymentResult ? $guestPaymentResult['success'] : false,
                    'wallet_charge_processed' => $walletChargeResult ? $walletChargeResult['success'] : false
                ]);

            }

            return response()->json($result, 422);

        } catch (Exception $e) {
            Log::error('OTP verification error', [
                'mobile' => $request->mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطای سیستمی رخ داده است',
                'code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Register or login without OTP when bypass is allowed
     */
    public function registerWithoutOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Otp::isValidIranianMobile($value)) {
                    $fail('شماره موبایل نامعتبر است.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $formattedMobile = Otp::formatMobile($request->mobile);
            
            // Check if bypass is allowed
            if (Session::get('bypass_otp_mobile') !== $formattedMobile) {
                return response()->json([
                    'success' => false,
                    'message' => 'ثبت نام بدون کد تأیید مجاز نیست',
                    'code' => 'BYPASS_NOT_ALLOWED'
                ], 403);
            }
            
            $bypassReason = Session::get('bypass_otp_reason');
            
            // Log the bypass registration
            Log::warning('User registered/logged in without OTP', [
                'mobile' => $formattedMobile,
                'reason' => $bypassReason,
                'ip' => $request->ip()
            ]);
            
            // Clear bypass session
            Session::forget('bypass_otp_mobile');
            Session::forget('bypass_otp_reason');
            
            // Find or create user
            $user = User::firstOrCreate(
                ['mobile' => $formattedMobile],
                [
                    'name' => 'کاربر ' . substr($formattedMobile, -4),
                    'email' => $formattedMobile . '@mobile.pishkhanak.com',
                    'mobile_verified_at' => now(),
                ]
            );
            
            // Update mobile verification if not set
            if (!$user->mobile_verified_at) {
                $user->update(['mobile_verified_at' => now()]);
            }
            
            // Log the user in
            Auth::login($user, true);
            
            Log::info('User registered/logged in without OTP', [
                'user_id' => $user->id,
                'mobile' => $formattedMobile,
                'reason' => $bypassReason
            ]);
            
            // Process pending payments and service continuations
            $finalRedirect = route('app.page.home');
            $finalMessage = 'با موفقیت وارد شدید';
            $serviceProcessed = false;
            $paymentProcessed = false;
            
            // Check for pending wallet charge
            $walletChargeResult = $this->processPendingWalletCharge($user);
            
            // Check for pending guest payment processing
            $guestPaymentResult = null;
            if (Session::has('guest_payment_success') && Session::has('guest_payment_transaction_id')) {
                $guestPaymentController = app(\App\Http\Controllers\GuestPaymentController::class);
                $guestPaymentResult = $guestPaymentController->processGuestPaymentAfterLogin($user);
            }
            
            // Determine final redirect and message
            if ($guestPaymentResult && $guestPaymentResult['success']) {
                $finalRedirect = $guestPaymentResult['redirect'] ?? $finalRedirect;
                $finalMessage = $guestPaymentResult['message'] ?? $finalMessage;
                $serviceProcessed = $guestPaymentResult['service_processed'] ?? false;
                $paymentProcessed = true;
            } elseif ($walletChargeResult && $walletChargeResult['success']) {
                $finalRedirect = $walletChargeResult['redirect'] ?? $finalRedirect;
                $finalMessage = $walletChargeResult['message'] ?? $finalMessage;
                $serviceProcessed = $walletChargeResult['service_processed'] ?? false;
                $paymentProcessed = true;
            }
            
            return response()->json([
                'success' => true,
                'message' => $finalMessage,
                'redirect' => $finalRedirect,
                'payment_processed' => $paymentProcessed,
                'service_processed' => $serviceProcessed,
                'bypass_used' => true
            ]);
            
        } catch (Exception $e) {
            Log::error('Register without OTP error', [
                'mobile' => $request->mobile,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطای سیستمی رخ داده است',
                'code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }
    
    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['user_id' => $userId]);

        return redirect('/')->with('status', 'با موفقیت خارج شدید');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('app.auth.login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Get OTP statistics (for debugging)
     */
    public function getOtpStats()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403);
        }

        return response()->json($this->smsService->getStatistics());
    }
    
    /**
     * Process pending wallet charge after login
     */
    protected function processPendingWalletCharge($user)
    {
        try {
            $transactionUuid = Session::get('pending_wallet_charge_transaction_uuid');
            $transactionId = Session::get('pending_wallet_charge_transaction_id');
            
            if (!$transactionUuid && !$transactionId) {
                return null;
            }

            // Find the transaction
            $transaction = null;
            if ($transactionUuid) {
                $transaction = \App\Models\GatewayTransaction::where('uuid', $transactionUuid)->first();
            } elseif ($transactionId) {
                $transaction = \App\Models\GatewayTransaction::find($transactionId);
            }

            if (!$transaction || $transaction->status !== 'completed') {
                return null;
            }

            // ✅ UPDATE TRANSACTION TO LINK TO USER (Convert guest payment to user payment)
            $wasGuestPayment = !$transaction->user_id;
            
            if (!$transaction->user_id) {
                $transaction->update(['user_id' => $user->id]);
                
                Log::info('Transaction linked to user after login', [
                    'transaction_id' => $transaction->id,
                    'transaction_uuid' => $transaction->uuid,
                    'user_id' => $user->id,
                    'was_guest_payment' => true
                ]);
            } elseif ($transaction->user_id !== $user->id) {
                // Security check: ensure transaction belongs to this user
                Log::warning('Transaction user mismatch during wallet charge processing', [
                    'transaction_id' => $transaction->id,
                    'transaction_user_id' => $transaction->user_id,
                    'current_user_id' => $user->id
                ]);
                return [
                    'success' => false,
                    'message' => 'تراکنش متعلق به کاربر دیگری است'
                ];
            }

            // Check if wallet was already charged for this transaction
            $existingDeposit = $user->walletTransactions()
                ->where('meta->gateway_transaction_id', $transaction->id)
                ->where('type', 'deposit')
                ->first();

            if ($existingDeposit) {
                Log::info('Wallet already charged for this transaction', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id
                ]);
            } else {
                // Process wallet charge
                // Load gateway relationship if needed
                if (!$transaction->relationLoaded('paymentGateway')) {
                    $transaction->load('paymentGateway');
                }
                
                $gatewayName = $transaction->paymentGateway ? $transaction->paymentGateway->name : 'درگاه پرداخت';
                
                $user->deposit($transaction->amount, [
                    'description' => 'شارژ کیف‌پول از طریق ' . $gatewayName,
                    'gateway_transaction_id' => $transaction->id,
                    'gateway_reference_id' => $transaction->gateway_reference_id,
                    'type' => 'wallet_charge_after_login',
                    'payment_source' => 'gateway_payment',
                    'payment_method' => 'gateway',
                    'converted_from_guest' => $wasGuestPayment, // Track if this was a guest conversion
                    'processed_at' => now()->toISOString(),
                ]);
                
                Log::info('Wallet charged successfully after login', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'was_guest_conversion' => $wasGuestPayment
                ]);
            }
            
            // Clear wallet charge session data
            Session::forget([
                'pending_wallet_charge_transaction_id',
                'pending_wallet_charge_transaction_uuid', 
                'pending_wallet_charge_success',
                'pending_wallet_charge_amount',
                'pending_wallet_charge'
            ]);
            
            // Check for service continuation
            $metadata = $transaction->metadata ?? [];
            $redirect = route('app.user.wallet');
            $serviceProcessed = false;
            
            // Process service continuation from transaction metadata
            if (isset($metadata['continue_service']) && isset($metadata['service_session_key'])) {
                $result = $this->processServiceContinuation($user, $metadata);
                if ($result && $result['success']) {
                    $redirect = $result['redirect'];
                    $serviceProcessed = true;
                }
            }
            
            // Also check session for service continuation (backup)
            if (!$serviceProcessed && Session::has('pending_service_continuation')) {
                $serviceData = Session::get('pending_service_continuation');
                $result = $this->processServiceContinuationFromSession($user, $serviceData);
                if ($result && $result['success']) {
                    $redirect = $result['redirect'];
                    $serviceProcessed = true;
                }
            }
            
            Log::info('Pending wallet charge processed after login', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'service_processed' => $serviceProcessed,
                'redirect' => $redirect,
                'transaction_now_linked_to_user' => true
            ]);
            
            return [
                'success' => true,
                'message' => $serviceProcessed ? 
                    'کیف پول شما شارژ شد و سرویس در حال پردازش است...' : 
                    'کیف پول شما با موفقیت شارژ شد',
                'redirect' => $redirect,
                'service_processed' => $serviceProcessed
            ];
            
        } catch (Exception $e) {
            Log::error('Error processing pending wallet charge', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'خطا در پردازش شارژ کیف پول'
            ];
        }
    }

    /**
     * Process service continuation from transaction metadata
     */
    protected function processServiceContinuation($user, $metadata)
    {
        try {
            $serviceSlug = $metadata['continue_service'] ?? null;
            $serviceRequestHash = $metadata['service_request_hash'] ?? null;
            $sessionKey = $metadata['service_session_key'] ?? null;

            if (!$serviceSlug) {
                return null;
            }

            // Find the service using safe slug lookup to handle sub-services properly
            $service = \App\Models\Service::findBySlugWithFallback($serviceSlug);
            if (!$service) {
                Log::warning('Service not found for continuation', [
                    'service_slug' => $serviceSlug,
                    'user_id' => $user->id
                ]);
                return null;
            }

            // Check if user has sufficient balance
            if ($user->balance < $service->price) {
                Log::warning('Insufficient balance for service continuation', [
                    'user_id' => $user->id,
                    'user_balance' => $user->balance,
                    'service_price' => $service->price,
                    'service_slug' => $serviceSlug
                ]);
                return null;
            }

            // Try to get service data from ServiceRequest if hash is available
            $serviceData = null;
            if ($serviceRequestHash) {
                $serviceRequest = \App\Models\ServiceRequest::findByHash($serviceRequestHash);
                if ($serviceRequest && $serviceRequest->service_id == $service->id) {
                    $serviceData = $serviceRequest->input_data;
                    Log::info('Retrieved service data from ServiceRequest', [
                        'service_request_id' => $serviceRequest->id,
                        'service_slug' => $serviceSlug,
                        'user_id' => $user->id
                    ]);
                }
            }

            // Fallback to session data if no hash or ServiceRequest found
            if (!$serviceData && $sessionKey) {
                $serviceData = Session::get($sessionKey, []);
                Log::info('Retrieved service data from session', [
                    'session_key' => $sessionKey,
                    'service_slug' => $serviceSlug,
                    'user_id' => $user->id
                ]);
            }

            if (empty($serviceData)) {
                Log::warning('No service data found for continuation', [
                    'service_slug' => $serviceSlug,
                    'session_key' => $sessionKey,
                    'request_hash' => $serviceRequestHash,
                    'user_id' => $user->id
                ]);
                return null;
            }

            // Process the service using ServicePaymentService
            $servicePaymentService = app(\App\Services\ServicePaymentService::class);
            
            // Create a mock request with the service data
            $request = new \Illuminate\Http\Request();
            $request->replace($serviceData);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $result = $servicePaymentService->handleServiceSubmission($request, $service, $serviceData);

            // Clean up session data
            if ($sessionKey) {
                Session::forget($sessionKey);
            }
            Session::forget('pending_service_continuation');

            Log::info('Service continuation processed', [
                'user_id' => $user->id,
                'service_slug' => $serviceSlug,
                'success' => $result['success'] ?? false,
                'redirect' => $result['redirect'] ?? null
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Error processing service continuation', [
                'user_id' => $user->id,
                'service_slug' => $metadata['continue_service'] ?? null,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Process service continuation from session data
     */
    protected function processServiceContinuationFromSession($user, $sessionData)
    {
        try {
            $serviceSlug = $sessionData['service'] ?? null;
            $requestHash = $sessionData['request_hash'] ?? null;
            $sessionKey = $sessionData['session_key'] ?? null;

            if (!$serviceSlug) {
                return null;
            }

            // Create metadata structure similar to transaction metadata
            $metadata = [
                'continue_service' => $serviceSlug,
                'service_request_hash' => $requestHash,
                'service_session_key' => $sessionKey
            ];

            return $this->processServiceContinuation($user, $metadata);

        } catch (\Exception $e) {
            Log::error('Error processing service continuation from session', [
                'user_id' => $user->id,
                'session_data' => $sessionData,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}