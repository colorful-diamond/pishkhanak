# Authentication System Documentation

## Overview

The Pishkhanak platform implements a comprehensive authentication system supporting multiple authentication methods, role-based access control, and secure session management. The system is designed to handle both web-based users and API clients with different security requirements.

## Authentication Methods

### 1. Traditional Username/Password
- Email-based login
- Secure password hashing using bcrypt
- Password reset functionality
- Account verification via email

### 2. SMS Authentication
- Mobile number-based registration
- OTP (One-Time Password) verification
- Multi-factor authentication support
- Iranian mobile number validation

### 3. Social Authentication
- Google OAuth 2.0 integration
- Discord OAuth integration
- GitHub OAuth support (configurable)
- Facebook OAuth support (configurable)

### 4. Guest Authentication
- Anonymous service access
- Mobile-based verification
- Temporary session management
- Conversion to full accounts

## Architecture

### Core Components

#### 1. User Model (`app/Models/User.php`)
```php
class User extends Authenticatable implements FilamentUser, HasWallet
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasWallet;
    
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'email_verified_at',
        'mobile_verified_at',
        'two_factor_enabled',
        'avatar',
        'settings'
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'settings' => 'array'
    ];
    
    // Filament admin access control
    public function canAccessFilament(): bool
    {
        return $this->hasRole(['admin', 'moderator']);
    }
}
```

#### 2. OTP System (`app/Models/Otp.php`)
```php
class Otp extends Model
{
    protected $fillable = [
        'identifier',
        'identifier_type',
        'code',
        'purpose',
        'expires_at',
        'verified_at',
        'attempts'
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime'
    ];
    
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
    
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }
    
    public function canAttempt(): bool
    {
        return $this->attempts < 3;
    }
}
```

#### 3. Social Authentication (`app/Http/Controllers/Auth/SocialAuthController.php`)
```php
class SocialAuthController extends Controller
{
    public function redirectToProvider(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);
        
        return Socialite::driver($provider)->redirect();
    }
    
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            $user = $this->findOrCreateUser($socialUser, $provider);
            
            Auth::login($user, true);
            
            return redirect()->intended('/dashboard');
        } catch (Exception $e) {
            return redirect('/login')->withError('Social login failed');
        }
    }
    
    private function findOrCreateUser($socialUser, string $provider): User
    {
        // Check for existing social account
        $existingUser = User::where('email', $socialUser->email)->first();
        
        if ($existingUser) {
            $this->updateSocialData($existingUser, $socialUser, $provider);
            return $existingUser;
        }
        
        // Create new user
        return User::create([
            'name' => $socialUser->name,
            'email' => $socialUser->email,
            'avatar' => $socialUser->avatar,
            'email_verified_at' => now(),
            'settings' => [
                'social_accounts' => [
                    $provider => [
                        'id' => $socialUser->id,
                        'nickname' => $socialUser->nickname
                    ]
                ]
            ]
        ]);
    }
}
```

### 4. SMS Authentication Service
```php
class SmsVerificationService
{
    public function sendVerificationCode(string $mobile, string $purpose = 'verification'): bool
    {
        // Generate 5-digit code
        $code = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
        
        // Store OTP
        Otp::create([
            'identifier' => $mobile,
            'identifier_type' => 'mobile',
            'code' => Hash::make($code),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0
        ]);
        
        // Send SMS
        return $this->smsService->send($mobile, "کد تایید: {$code}");
    }
    
    public function verifyCode(string $mobile, string $code): bool
    {
        $otp = Otp::where('identifier', $mobile)
            ->where('identifier_type', 'mobile')
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->first();
            
        if (!$otp || !$otp->canAttempt()) {
            return false;
        }
        
        $otp->increment('attempts');
        
        if (Hash::check($code, $otp->code)) {
            $otp->update(['verified_at' => now()]);
            return true;
        }
        
        return false;
    }
}
```

## Role-Based Access Control (RBAC)

### Roles and Permissions System
Using Spatie Laravel Permission package for comprehensive RBAC:

#### Default Roles
```php
// Database seeder
$roles = [
    'super-admin' => [
        'description' => 'Full system access',
        'permissions' => ['*']
    ],
    'admin' => [
        'description' => 'Administrative access',
        'permissions' => [
            'manage users', 'manage services', 'manage payments',
            'view analytics', 'manage content', 'manage settings'
        ]
    ],
    'moderator' => [
        'description' => 'Content and user moderation',
        'permissions' => [
            'moderate users', 'moderate content', 'view reports'
        ]
    ],
    'user' => [
        'description' => 'Standard user access',
        'permissions' => [
            'use services', 'manage profile', 'view history'
        ]
    ],
    'guest' => [
        'description' => 'Limited guest access',
        'permissions' => [
            'use basic services'
        ]
    ]
];
```

#### Permission Categories
```php
$permissions = [
    // User Management
    'manage users' => 'Create, update, delete users',
    'moderate users' => 'Suspend, ban users',
    'view users' => 'View user information',
    
    // Service Management
    'manage services' => 'Add, edit, delete services',
    'use services' => 'Access and use services',
    'view service analytics' => 'View service usage statistics',
    
    // Payment Management
    'manage payments' => 'Configure payment gateways',
    'process refunds' => 'Process payment refunds',
    'view transactions' => 'View payment transactions',
    
    // Content Management
    'manage content' => 'Create, edit, delete content',
    'moderate content' => 'Review and moderate content',
    'publish content' => 'Publish content to website',
    
    // System Settings
    'manage settings' => 'Configure system settings',
    'view analytics' => 'Access system analytics',
    'manage tokens' => 'Manage API tokens'
];
```

### Middleware Integration
```php
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        if (!Auth::user()->can($permission)) {
            abort(403, 'Insufficient permissions');
        }
        
        return $next($request);
    }
}

// Route protection
Route::middleware(['auth', 'permission:manage users'])->group(function () {
    Route::resource('users', UserController::class);
});
```

## API Authentication

### Sanctum Token-Based Authentication
```php
class TokenService
{
    public function createToken(User $user, string $name, array $abilities = ['*']): string
    {
        $token = $user->createToken($name, $abilities, now()->addYear());
        
        // Log token creation
        TokenRefreshLog::create([
            'user_id' => $user->id,
            'token_name' => $name,
            'action' => 'created',
            'abilities' => $abilities,
            'expires_at' => now()->addYear()
        ]);
        
        return $token->plainTextToken;
    }
    
    public function revokeToken(User $user, string $tokenName): bool
    {
        $deleted = $user->tokens()
            ->where('name', $tokenName)
            ->delete();
            
        if ($deleted) {
            TokenRefreshLog::create([
                'user_id' => $user->id,
                'token_name' => $tokenName,
                'action' => 'revoked'
            ]);
        }
        
        return $deleted > 0;
    }
}
```

### Token Scopes and Abilities
```php
// API routes with specific abilities
Route::middleware(['auth:sanctum', 'abilities:read-services'])
    ->get('/services', [ServiceController::class, 'index']);
    
Route::middleware(['auth:sanctum', 'abilities:use-services'])
    ->post('/services/{service}/request', [ServiceController::class, 'request']);
    
Route::middleware(['auth:sanctum', 'abilities:admin'])
    ->apiResource('admin/users', AdminUserController::class);
```

## Two-Factor Authentication (2FA)

### SMS-Based 2FA
```php
class TwoFactorAuthService
{
    public function enable2FA(User $user, string $mobile): bool
    {
        // Send verification SMS
        $sent = $this->smsService->sendVerificationCode($mobile, '2fa-setup');
        
        if ($sent) {
            $user->update([
                'two_factor_mobile' => $mobile,
                'two_factor_enabled' => false // Will be enabled after verification
            ]);
            
            return true;
        }
        
        return false;
    }
    
    public function verify2FASetup(User $user, string $code): bool
    {
        $verified = $this->smsService->verifyCode($user->two_factor_mobile, $code);
        
        if ($verified) {
            $user->update([
                'two_factor_enabled' => true,
                'two_factor_verified_at' => now()
            ]);
        }
        
        return $verified;
    }
    
    public function require2FA(User $user): bool
    {
        return $user->two_factor_enabled && 
               session('2fa_verified_at', 0) < now()->subHours(8)->timestamp;
    }
}
```

### 2FA Middleware
```php
class RequireTwoFactorAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        $twoFactorService = app(TwoFactorAuthService::class);
        
        if ($twoFactorService->require2FA($user)) {
            return redirect('/2fa/verify');
        }
        
        return $next($request);
    }
}
```

## Session Management

### Secure Session Configuration
```php
// config/session.php
return [
    'driver' => 'redis',
    'lifetime' => 120, // 2 hours
    'expire_on_close' => false,
    'encrypt' => true,
    'files' => storage_path('framework/sessions'),
    'connection' => 'session',
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'pishkhanak_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE', true),
    'http_only' => true,
    'same_site' => 'lax',
];
```

### Session Security Features
```php
class SessionSecurityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check for session hijacking
        if ($this->detectSessionHijacking($request)) {
            Auth::logout();
            session()->invalidate();
            return redirect('/login')->withError('Security violation detected');
        }
        
        // Update session activity
        $this->updateSessionActivity($request);
        
        return $next($request);
    }
    
    private function detectSessionHijacking(Request $request): bool
    {
        $storedUserAgent = session('user_agent');
        $storedIp = session('ip_address');
        
        if ($storedUserAgent && $storedUserAgent !== $request->userAgent()) {
            return true;
        }
        
        if ($storedIp && $storedIp !== $request->ip()) {
            return true;
        }
        
        return false;
    }
}
```

## Password Security

### Password Policies
```php
class PasswordPolicy
{
    public static function rules(): array
    {
        return [
            'required',
            'min:8',
            'max:128',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'confirmed'
        ];
    }
    
    public static function validate(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain lowercase letters';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain uppercase letters';
        }
        
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Password must contain numbers';
        }
        
        if (!preg_match('/[@$!%*?&]/', $password)) {
            $errors[] = 'Password must contain special characters';
        }
        
        return $errors;
    }
}
```

### Password Reset System
```php
class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            // Return success to prevent email enumeration
            return response()->json(['message' => 'Password reset link sent if email exists']);
        }
        
        $token = Str::random(60);
        
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );
        
        Mail::to($user)->send(new PasswordResetMail($token));
        
        return response()->json(['message' => 'Password reset link sent']);
    }
    
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => PasswordPolicy::rules()
        ]);
        
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();
            
        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['error' => 'Invalid reset token'], 400);
        }
        
        // Check token age (expires after 1 hour)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            return response()->json(['error' => 'Reset token expired'], 400);
        }
        
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);
        
        // Revoke all existing tokens
        $user->tokens()->delete();
        
        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        
        return response()->json(['message' => 'Password reset successfully']);
    }
}
```

## Guest Authentication

### Guest Service Access
```php
class GuestAuthController extends Controller
{
    public function requestGuestService(Request $request): JsonResponse
    {
        $request->validate([
            'mobile' => 'required|regex:/^09\d{9}$/',
            'service_slug' => 'required|exists:services,slug'
        ]);
        
        // Create temporary guest session
        $guestToken = $this->createGuestToken($request->mobile);
        
        // Send SMS verification
        $smsService = new SmsVerificationService();
        $sent = $smsService->sendVerificationCode($request->mobile, 'guest-service');
        
        if (!$sent) {
            return response()->json(['error' => 'Failed to send SMS'], 500);
        }
        
        return response()->json([
            'guest_token' => $guestToken,
            'message' => 'SMS sent for verification',
            'expires_at' => now()->addMinutes(30)
        ]);
    }
    
    public function verifyGuestAccess(Request $request): JsonResponse
    {
        $request->validate([
            'guest_token' => 'required',
            'mobile' => 'required',
            'code' => 'required|digits:5'
        ]);
        
        // Verify guest token
        $guestData = $this->verifyGuestToken($request->guest_token);
        
        if (!$guestData || $guestData['mobile'] !== $request->mobile) {
            return response()->json(['error' => 'Invalid guest token'], 400);
        }
        
        // Verify SMS code
        $smsService = new SmsVerificationService();
        $verified = $smsService->verifyCode($request->mobile, $request->code);
        
        if (!$verified) {
            return response()->json(['error' => 'Invalid verification code'], 400);
        }
        
        // Create authenticated guest session
        $this->authenticateGuest($guestData);
        
        return response()->json([
            'message' => 'Guest access verified',
            'service_url' => "/services/{$guestData['service_slug']}"
        ]);
    }
    
    private function createGuestToken(string $mobile): string
    {
        $payload = [
            'mobile' => $mobile,
            'type' => 'guest',
            'created_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(30)->timestamp
        ];
        
        return encrypt($payload);
    }
}
```

## Security Hardening

### Rate Limiting
```php
class AuthRateLimitService
{
    public function attemptLogin(Request $request): bool
    {
        $key = 'login_attempts:' . $request->ip();
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= 5) {
            $this->lockout($request->ip());
            throw new TooManyAttemptsException('Too many login attempts');
        }
        
        Cache::put($key, $attempts + 1, 900); // 15 minutes
        return true;
    }
    
    public function clearAttempts(Request $request): void
    {
        Cache::forget('login_attempts:' . $request->ip());
        Cache::forget('lockout:' . $request->ip());
    }
    
    private function lockout(string $ip): void
    {
        Cache::put("lockout:{$ip}", true, 3600); // 1 hour lockout
    }
}
```

### CSRF Protection
```php
// All forms include CSRF tokens
@csrf

// API routes are stateless and use different protection
Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->group(function () {
        // API routes
    });
```

### Input Sanitization
```php
class SanitizeInput
{
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        });
        
        $request->replace($input);
        
        return $next($request);
    }
}
```

## Logging and Monitoring

### Authentication Events
```php
class AuthEventSubscriber
{
    public function handleLoginSuccess($event): void
    {
        Log::info('User login successful', [
            'user_id' => $event->user->id,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }
    
    public function handleLoginFailed($event): void
    {
        Log::warning('User login failed', [
            'email' => $event->credentials['email'] ?? 'unknown',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }
    
    public function handleLogout($event): void
    {
        Log::info('User logout', [
            'user_id' => $event->user->id,
            'session_duration' => $this->calculateSessionDuration(),
            'timestamp' => now()
        ]);
    }
}
```

### Security Metrics
```php
class SecurityMetricsService
{
    public function getAuthenticationMetrics(Carbon $from, Carbon $to): array
    {
        return [
            'total_logins' => $this->getTotalLogins($from, $to),
            'failed_attempts' => $this->getFailedAttempts($from, $to),
            'success_rate' => $this->getSuccessRate($from, $to),
            'unique_users' => $this->getUniqueUsers($from, $to),
            'suspicious_activities' => $this->getSuspiciousActivities($from, $to),
            'blocked_ips' => $this->getBlockedIPs($from, $to)
        ];
    }
}
```

## Testing

### Authentication Tests
```php
class AuthenticationTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);
        
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
    
    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);
        
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
    
    public function test_two_factor_authentication_flow()
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_mobile' => '09123456789'
        ]);
        
        // Login with valid credentials
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        
        // Should be redirected to 2FA
        $this->assertRedirect('/2fa/verify');
        
        // Verify 2FA code
        $this->post('/2fa/verify', [
            'code' => '12345'
        ]);
        
        $this->assertRedirect('/dashboard');
    }
}
```

This comprehensive authentication system provides secure, scalable user management with multiple authentication methods, robust session handling, and comprehensive security features.