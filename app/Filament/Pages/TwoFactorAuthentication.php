<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthentication extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.pages.two-factor-authentication';
    protected static ?string $navigationLabel = 'احراز هویت دو مرحله‌ای';
    protected static ?string $title = 'احراز هویت دو مرحله‌ای';
    protected static ?string $navigationGroup = 'امنیت';
    protected static ?int $navigationSort = 1;

    public ?string $confirmationCode = '';
    public bool $showRecoveryCodes = false;
    public array $recoveryCodes = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('confirmationCode')
                ->label('کد تایید')
                ->placeholder('کد ۶ رقمی را وارد کنید')
                ->numeric()
                ->maxLength(6)
                ->visible(fn () => Auth::user()->two_factor_enabled && !Auth::user()->two_factor_confirmed_at),
        ];
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $actions = [];

        if (!$user->two_factor_enabled) {
            $actions[] = Action::make('enable')
                ->label('فعال‌سازی احراز هویت دو مرحله‌ای')
                ->color('success')
                ->icon('heroicon-o-shield-check')
                ->action('enableTwoFactor')
                ->requiresConfirmation()
                ->modalHeading('فعال‌سازی احراز هویت دو مرحله‌ای')
                ->modalDescription('آیا مطمئن هستید که می‌خواهید احراز هویت دو مرحله‌ای را فعال کنید؟');
        } else {
            if (!$user->two_factor_confirmed_at) {
                $actions[] = Action::make('confirm')
                    ->label('تایید کد')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action('confirmTwoFactor');
            }

            $actions[] = Action::make('showRecoveryCodes')
                ->label('نمایش کدهای بازیابی')
                ->color('info')
                ->icon('heroicon-o-key')
                ->action('showRecoveryCodes');

            $actions[] = Action::make('regenerateRecoveryCodes')
                ->label('تولید مجدد کدهای بازیابی')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->action('regenerateRecoveryCodes')
                ->requiresConfirmation()
                ->modalHeading('تولید مجدد کدهای بازیابی')
                ->modalDescription('با تولید مجدد کدهای بازیابی، کدهای قبلی غیرفعال می‌شوند. آیا مطمئن هستید؟');

            $actions[] = Action::make('disable')
                ->label('غیرفعال‌سازی احراز هویت دو مرحله‌ای')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->action('disableTwoFactor')
                ->requiresConfirmation()
                ->modalHeading('غیرفعال‌سازی احراز هویت دو مرحله‌ای')
                ->modalDescription('با غیرفعال‌سازی احراز هویت دو مرحله‌ای، امنیت حساب شما کاهش می‌یابد. آیا مطمئن هستید؟');
        }

        return $actions;
    }

    public function enableTwoFactor(): void
    {
        Auth::user()->enableTwoFactorAuthentication();
        
        Notification::make()
            ->title('احراز هویت دو مرحله‌ای فعال شد')
            ->body('برای تایید و فعال‌سازی کامل، لطفاً کد QR را با اپلیکیشن احراز هویت خود اسکن کرده و کد تایید را وارد کنید.')
            ->success()
            ->send();
    }

    public function confirmTwoFactor(): void
    {
        $this->validate([
            'confirmationCode' => ['required', 'string', 'size:6'],
        ]);

        if (!Auth::user()->verifyTwoFactorCode($this->confirmationCode)) {
            Notification::make()
                ->title('کد تایید نامعتبر')
                ->body('کد واردشده صحیح نیست. لطفاً کد درست را از اپلیکیشن احراز هویت وارد کنید.')
                ->danger()
                ->send();
            return;
        }

        Auth::user()->confirmTwoFactorAuthentication();
        $this->showRecoveryCodes = true;
        $this->recoveryCodes = Auth::user()->two_factor_recovery_codes;

        Notification::make()
            ->title('احراز هویت دو مرحله‌ای تایید شد')
            ->body('احراز هویت دو مرحله‌ای با موفقیت فعال شد. کدهای بازیابی را در مکان امنی ذخیره کنید.')
            ->success()
            ->send();

        $this->confirmationCode = '';
    }

    public function disableTwoFactor(): void
    {
        Auth::user()->disableTwoFactorAuthentication();
        
        Notification::make()
            ->title('احراز هویت دو مرحله‌ای غیرفعال شد')
            ->body('احراز هویت دو مرحله‌ای با موفقیت غیرفعال شد. حساب شما اکنون تنها با رمز عبور محافظت می‌شود.')
            ->warning()
            ->send();
    }

    public function showRecoveryCodes(): void
    {
        $this->showRecoveryCodes = true;
        $this->recoveryCodes = Auth::user()->two_factor_recovery_codes;
    }

    public function regenerateRecoveryCodes(): void
    {
        $this->recoveryCodes = Auth::user()->regenerateRecoveryCodes();
        $this->showRecoveryCodes = true;
        
        Notification::make()
            ->title('کدهای بازیابی جدید تولید شد')
            ->body('کدهای بازیابی جدید با موفقیت تولید شد. کدهای قبلی دیگر قابل استفاده نیستند.')
            ->success()
            ->send();
    }

    public function getQrCodeSvg(): string
    {
        if (!Auth::user()->two_factor_enabled || Auth::user()->two_factor_confirmed_at) {
            return '';
        }

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        
        return $writer->writeString(Auth::user()->getTwoFactorQrCodeUrl());
    }
}