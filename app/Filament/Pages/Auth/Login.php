<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends BaseLogin
{
    protected ?string $twoFactorCode = null;
    protected bool $requiresTwoFactor = false;

    public function mount(): void
    {
        parent::mount();
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getTwoFactorFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getTwoFactorFormComponent(): Component
    {
        return TextInput::make('two_factor_code')
            ->label('کد تایید دو مرحله‌ای')
            ->placeholder('کد ۶ رقمی از اپلیکیشن احراز هویت')
            ->maxLength(50)
            ->visible(fn () => $this->requiresTwoFactor)
            ->required(fn () => $this->requiresTwoFactor)
            ->helperText('کد تایید را از اپلیکیشن Google Authenticator یا Authy وارد کنید');
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        // First, try to authenticate with email and password
        if (!$this->requiresTwoFactor) {
            $credentials = [
                'email' => $data['email'],
                'password' => $data['password'],
            ];

            if (!auth()->guard('web')->validate($credentials)) {
                throw ValidationException::withMessages([
                    'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
                ]);
            }

            // Check if user has 2FA enabled
            $user = \App\Models\User::where('email', $data['email'])->first();
            
            if ($user && $user->hasTwoFactorAuthenticationEnabled()) {
                // Store credentials temporarily and show 2FA field
                session(['2fa_user_id' => $user->id]);
                session(['2fa_remember' => $data['remember'] ?? false]);
                
                $this->requiresTwoFactor = true;
                
                Notification::make()
                    ->title('کد تایید دو مرحله‌ای مورد نیاز است')
                    ->body('لطفاً کد تایید ۶ رقمی را از اپلیکیشن احراز هویت خود وارد کنید')
                    ->info()
                    ->send();
                
                return null;
            }

            // No 2FA required, proceed with login
            auth()->guard('web')->attempt($credentials, $data['remember'] ?? false);

            session()->regenerate();

            return app(LoginResponse::class);
        }

        // Handle 2FA verification
        $userId = session('2fa_user_id');
        
        if (!$userId) {
            // Session expired, start over
            $this->requiresTwoFactor = false;
            session()->forget(['2fa_user_id', '2fa_remember']);
            
            throw ValidationException::withMessages([
                'data.email' => 'نشست منقضی شده است. لطفاً دوباره وارد شوید.',
            ]);
        }

        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            $this->requiresTwoFactor = false;
            session()->forget(['2fa_user_id', '2fa_remember']);
            
            throw ValidationException::withMessages([
                'data.email' => 'کاربر یافت نشد. لطفاً دوباره تلاش کنید.',
            ]);
        }

        // Verify 2FA code
        $code = trim($data['two_factor_code'] ?? '');
        
        if (!$code) {
            throw ValidationException::withMessages([
                'data.two_factor_code' => 'کد تایید الزامی است.',
            ]);
        }

        if (!$user->verifyTwoFactorCode($code)) {
            throw ValidationException::withMessages([
                'data.two_factor_code' => 'کد تایید نامعتبر است. لطفاً دوباره تلاش کنید.',
            ]);
        }

        // 2FA verified, proceed with login
        auth()->guard('web')->login($user, session('2fa_remember', false));
        
        session()->forget(['2fa_user_id', '2fa_remember']);
        session()->regenerate();

        return app(LoginResponse::class);
    }
}