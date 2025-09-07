<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider',
        'access_token',
        'refresh_token',
        'expires_at',
        'refresh_expires_at',
        'last_used_at',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'refresh_expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    // Cache TTL in seconds (5 minutes)
    const CACHE_TTL = 300;

    /**
     * Provider constants
     */
    const PROVIDER_JIBIT = 'jibit';
    const PROVIDER_FINNOTECH = 'finnotech';

    /**
     * Token name constants
     */
    const NAME_JIBIT = 'jibit';
    const NAME_FINNOTECH = 'fino';
    
    // New Finnotech category-specific token names
    const NAME_FINNOTECH_INQUIRY = 'fino_inquiry';
    const NAME_FINNOTECH_CREDIT = 'fino_credit';
    const NAME_FINNOTECH_KYC = 'fino_kyc';
    const NAME_FINNOTECH_TOKEN = 'fino_token';
    const NAME_FINNOTECH_PROMISSORY = 'fino_promissory';
    const NAME_FINNOTECH_VEHICLE = 'fino_vehicle';
    const NAME_FINNOTECH_INSURANCE = 'fino_insurance';
    const NAME_FINNOTECH_SMS = 'fino_sms';

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when token is updated
        static::saved(function ($token) {
            $token->clearCache();
        });

        static::deleted(function ($token) {
            $token->clearCache();
        });
    }

    /**
     * Get token by provider with Redis caching
     *
     * @param string $provider
     * @return Token|null
     */
    public static function getByProvider(string $provider): ?Token
    {
        $cacheKey = "token:provider:{$provider}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($provider) {
            return self::where('provider', $provider)
                ->where('is_active', true)
                ->first();
        });
    }

    /**
     * Get token by name with Redis caching
     *
     * @param string $name
     * @return Token|null
     */
    public static function getByName(string $name): ?Token
    {
        $cacheKey = "token:name:{$name}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($name) {
            return self::where('name', $name)
                ->where('is_active', true)
                ->first();
        });
    }

    /**
     * Get active access token with caching
     *
     * @return string|null
     */
    public function getActiveAccessToken(): ?string
    {
        if (!$this->is_active) {
            return null;
        }

        // Check if token is expired
        if ($this->isAccessTokenExpired()) {
            return null;
        }

        // Update last used timestamp
        $this->updateLastUsed();

        return $this->access_token;
    }

    /**
     * Get active refresh token
     *
     * @return string|null
     */
    public function getActiveRefreshToken(): ?string
    {
        if (!$this->is_active) {
            return null;
        }

        // Check if refresh token is expired
        if ($this->isRefreshTokenExpired()) {
            return null;
        }

        return $this->refresh_token;
    }

    /**
     * Check if access token is expired
     *
     * @return bool
     */
    public function isAccessTokenExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if refresh token is expired
     *
     * @return bool
     */
    public function isRefreshTokenExpired(): bool
    {
        return $this->refresh_expires_at && $this->refresh_expires_at->isPast();
    }

    /**
     * Check if token needs refresh (expires in next 5 minutes)
     *
     * @return bool
     */
    public function needsRefresh(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isBefore(now()->addMinutes(5));
    }

    /**
     * Update access token
     *
     * @param string $accessToken
     * @param string|null $refreshToken
     * @param Carbon|null $expiresAt
     * @param Carbon|null $refreshExpiresAt
     * @return bool
     */
    public function updateTokens(
        string $accessToken,
        ?string $refreshToken = null,
        ?Carbon $expiresAt = null,
        ?Carbon $refreshExpiresAt = null
    ): bool {
        $this->access_token = $accessToken;
        
        if ($refreshToken) {
            $this->refresh_token = $refreshToken;
        }

        if ($expiresAt) {
            $this->expires_at = $expiresAt;
        }

        if ($refreshExpiresAt) {
            $this->refresh_expires_at = $refreshExpiresAt;
        }

        $this->last_used_at = now();

        return $this->save();
    }

    /**
     * Update last used timestamp
     *
     * @return bool
     */
    public function updateLastUsed(): bool
    {
        // Only update if last update was more than 1 minute ago to avoid excessive DB writes
        if (!$this->last_used_at || $this->last_used_at->isBefore(now()->subMinute())) {
            $this->last_used_at = now();
            return $this->save();
        }

        return true;
    }

    /**
     * Mark token as inactive
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Mark token as active
     *
     * @return bool
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Clear cache for this token
     */
    public function clearCache(): void
    {
        Cache::forget("token:provider:{$this->provider}");
        Cache::forget("token:name:{$this->name}");
    }

    /**
     * Get all cache keys for tokens
     *
     * @return array
     */
    public static function getAllCacheKeys(): array
    {
        return [
            'token:provider:' . self::PROVIDER_JIBIT,
            'token:provider:' . self::PROVIDER_FINNOTECH,
            'token:name:' . self::NAME_JIBIT,
            'token:name:' . self::NAME_FINNOTECH,
            // Add cache keys for new Finnotech categories
            'token:name:' . self::NAME_FINNOTECH_INQUIRY,
            'token:name:' . self::NAME_FINNOTECH_CREDIT,
            'token:name:' . self::NAME_FINNOTECH_KYC,
            'token:name:' . self::NAME_FINNOTECH_TOKEN,
            'token:name:' . self::NAME_FINNOTECH_PROMISSORY,
            'token:name:' . self::NAME_FINNOTECH_VEHICLE,
            'token:name:' . self::NAME_FINNOTECH_INSURANCE,
            'token:name:' . self::NAME_FINNOTECH_SMS,
        ];
    }

    /**
     * Clear all token caches
     */
    public static function clearAllCache(): void
    {
        foreach (self::getAllCacheKeys() as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Create or update token for provider
     *
     * @param string $provider
     * @param string $name
     * @param string $accessToken
     * @param string $refreshToken
     * @param Carbon|null $expiresAt
     * @param Carbon|null $refreshExpiresAt
     * @param array $metadata
     * @return Token
     */
    public static function createOrUpdate(
        string $provider,
        string $name,
        string $accessToken,
        string $refreshToken,
        ?Carbon $expiresAt = null,
        ?Carbon $refreshExpiresAt = null,
        array $metadata = []
    ): Token {
        return self::updateOrCreate(
            ['name' => $name],
            [
                'provider' => $provider,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_at' => $expiresAt,
                'refresh_expires_at' => $refreshExpiresAt,
                'metadata' => $metadata,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Get tokens that need refresh
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTokensNeedingRefresh()
    {
        return self::where('is_active', true)
            ->where('expires_at', '<', now()->addMinutes(5))
            ->where('refresh_expires_at', '>', now())
            ->get();
    }

    /**
     * Get expired tokens
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getExpiredTokens()
    {
        return self::where('is_active', true)
            ->where('refresh_expires_at', '<', now())
            ->get();
    }

    /**
     * Backward compatibility: get value (access token)
     *
     * @return string|null
     */
    public function getValueAttribute(): ?string
    {
        return $this->access_token;
    }

    /**
     * Backward compatibility: get value2 (refresh token)
     *
     * @return string|null
     */
    public function getValue2Attribute(): ?string
    {
        return $this->refresh_token;
    }

    /**
     * Backward compatibility: set value (access token)
     *
     * @param string $value
     */
    public function setValueAttribute(string $value): void
    {
        $this->access_token = $value;
    }

    /**
     * Backward compatibility: set value2 (refresh token)
     *
     * @param string $value
     */
    public function setValue2Attribute(string $value): void
    {
        $this->refresh_token = $value;
    }
}
