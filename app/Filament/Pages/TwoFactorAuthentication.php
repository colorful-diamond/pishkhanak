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
    protected static ?string $navigationLabel = 'PERSIAN_TEXT_f597256e';
    protected static ?string $title = 'PERSIAN_TEXT_f597256e';
    protected static ?string $navigationGroup = 'PERSIAN_TEXT_b67081a5';
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
                ->label('PERSIAN_TEXT_c093b9f4')
                ->placeholder('PERSIAN_TEXT_57c5ed70')
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
                ->label('PERSIAN_TEXT_e2bdc21a')
                ->color('success')
                ->icon('heroicon-o-shield-check')
                ->action('enableTwoFactor')
                ->requiresConfirmation()
                ->modalHeading('PERSIAN_TEXT_e2bdc21a')
                ->modalDescription('PERSIAN_TEXT_7b0376af');
        } else {
            if (!$user->two_factor_confirmed_at) {
                $actions[] = Action::make('confirm')
                    ->label('PERSIAN_TEXT_afe51151')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action('confirmTwoFactor');
            }

            $actions[] = Action::make('showRecoveryCodes')
                ->label('PERSIAN_TEXT_d431cc33')
                ->color('info')
                ->icon('heroicon-o-key')
                ->action('showRecoveryCodes');

            $actions[] = Action::make('regenerateRecoveryCodes')
                ->label('PERSIAN_TEXT_0a0644b3')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->action('regenerateRecoveryCodes')
                ->requiresConfirmation()
                ->modalHeading('PERSIAN_TEXT_0a0644b3')
                ->modalDescription('PERSIAN_TEXT_e06f4492');

            $actions[] = Action::make('disable')
                ->label('PERSIAN_TEXT_a05ec6e3')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->action('disableTwoFactor')
                ->requiresConfirmation()
                ->modalHeading('PERSIAN_TEXT_a05ec6e3')
                ->modalDescription('PERSIAN_TEXT_f8fd9bed');
        }

        return $actions;
    }

    public function enableTwoFactor(): void
    {
        Auth::user()->enableTwoFactorAuthentication();
        
        Notification::make()
            ->title('PERSIAN_TEXT_29e18053')
            ->body('PERSIAN_TEXT_79145d08')
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
                ->title('PERSIAN_TEXT_642a882c')
                ->body('PERSIAN_TEXT_e1cfffd9')
                ->danger()
                ->send();
            return;
        }

        Auth::user()->confirmTwoFactorAuthentication();
        $this->showRecoveryCodes = true;
        $this->recoveryCodes = Auth::user()->two_factor_recovery_codes;

        Notification::make()
            ->title('PERSIAN_TEXT_c6d45641')
            ->body('PERSIAN_TEXT_888913c6')
            ->success()
            ->send();

        $this->confirmationCode = '';
    }

    public function disableTwoFactor(): void
    {
        Auth::user()->disableTwoFactorAuthentication();
        
        Notification::make()
            ->title('PERSIAN_TEXT_735c2305')
            ->body('PERSIAN_TEXT_4ac81573')
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
            ->title('PERSIAN_TEXT_d241e20e')
            ->body('PERSIAN_TEXT_233bccc1')
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