<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use PragmaRX\Google2FA\Google2FA;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\CanPay;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\Customer;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, Wallet, Customer
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasWallet, CanPay;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'phone_number',
        'mobile_verified_at',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'social_tokens' => 'array',
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'array',
        'two_factor_confirmed_at' => 'datetime',
    ];

    /**
     * Check if user is admin
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('admin') || $this->email === 'khoshdel.net@gmail.com';
    }

    public function canAccessPanel(Panel $panel): bool
    {


        if($this->hasRole('admin') || $this->email == 'khoshdel.net@gmail.com'){
            return true;
        }

        return false;
    }

    // Payment System Relationships
    public function gatewayTransactions()
    {
        return $this->hasMany(GatewayTransaction::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function successfulGatewayTransactions()
    {
        return $this->gatewayTransactions()->successful();
    }

    public function pendingGatewayTransactions()
    {
        return $this->gatewayTransactions()->pending();
    }

    public function getDefaultPaymentMethod()
    {
        return $this->paymentMethods()->active()->default()->first();
    }

    /**
     * Bavix Wallet Helper Methods
     * Note: Once Bavix is installed, these methods will be available automatically:
     * - $user->balance (get balance as integer in cents)
     * - $user->balanceFloat (get balance as float)
     * - $user->deposit($amount, $meta, $confirmed)
     * - $user->withdraw($amount, $meta, $confirmed)
     * - $user->forceWithdraw($amount, $meta, $confirmed)
     * - $user->transfer($to, $amount, $meta)
     * - $user->transactions (relationship to all transactions)
     * - $user->wallet (relationship to default wallet)
     * - $user->wallets (relationship to all wallets if using multi-wallet)
     */

    public function getFormattedWalletBalanceAttribute()
    {
        // Once Bavix is installed, use: $this->balanceFloat
        return number_format($this->balance ?? 0) . ' تومان';
    }

    public function getLatestTransactions($limit = 5)
    {
        // Once Bavix is installed, use: $this->transactions()->latest()->limit($limit)->get()
        return collect(); // Temporary placeholder
    }

    /**
     * Get user tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get assigned tickets (for support agents)
     */
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Two-Factor Authentication Methods
     */
    public function enableTwoFactorAuthentication(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        $this->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => $this->generateRecoveryCodes(),
            'two_factor_confirmed_at' => null,
        ]);
    }

    public function disableTwoFactorAuthentication(): void
    {
        $this->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    public function confirmTwoFactorAuthentication(): void
    {
        $this->update([
            'two_factor_confirmed_at' => now(),
        ]);
    }

    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->two_factor_enabled || !$this->two_factor_secret) {
            return false;
        }

        $google2fa = new Google2FA();
        $secret = decrypt($this->two_factor_secret);
        
        return $google2fa->verifyKey($secret, $code);
    }

    public function getTwoFactorQrCodeUrl(): string
    {
        if (!$this->two_factor_enabled || !$this->two_factor_secret) {
            return '';
        }

        $google2fa = new Google2FA();
        $secret = decrypt($this->two_factor_secret);
        $companyName = config('app.name', 'Pishkhanak');
        
        return $google2fa->getQRCodeUrl(
            $companyName,
            $this->email,
            $secret
        );
    }

    public function regenerateRecoveryCodes(): array
    {
        $codes = $this->generateRecoveryCodes();
        
        $this->update([
            'two_factor_recovery_codes' => $codes
        ]);
        
        return $codes;
    }

    public function hasTwoFactorAuthenticationEnabled(): bool
    {
        return $this->two_factor_enabled && $this->two_factor_confirmed_at !== null;
    }

    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }
        return $codes;
    }
}