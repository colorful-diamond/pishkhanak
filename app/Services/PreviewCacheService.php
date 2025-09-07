<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

/**
 * PreviewCacheService
 * 
 * Handles Redis caching for all preview API calls to save costs and improve performance.
 * Supports different cache strategies for different types of inquiries.
 */
class PreviewCacheService
{
    // Cache TTL settings (in seconds)
    const CARD_INQUIRY_TTL = 86400;      // 24 hours - card info doesn't change often
    const IBAN_INQUIRY_TTL = 43200;      // 12 hours - IBAN info is relatively stable
    const ACCOUNT_INQUIRY_TTL = 43200;   // 12 hours - account info is relatively stable
    const POSTAL_CODE_TTL = 604800;      // 7 days - postal code info rarely changes
    const BANK_INFO_TTL = 604800;        // 7 days - bank info rarely changes
    
    // Cache key prefixes
    const CARD_PREFIX = 'preview:card:';
    const IBAN_PREFIX = 'preview:iban:';
    const ACCOUNT_PREFIX = 'preview:account:';
    const POSTAL_CODE_PREFIX = 'preview:postal:';
    const BANK_PREFIX = 'preview:bank:';
    
    /**
     * Get cached card inquiry data
     *
     * @param string $cardNumber
     * @return array|null
     */
    public static function getCardInquiry(string $cardNumber): ?array
    {
        try {
            $key = self::CARD_PREFIX . self::hashCardNumber($cardNumber);
            $cached = Redis::get($key);
            
            if ($cached) {
                $data = json_decode($cached, true);
                return $data;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting card inquiry from cache', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Cache card inquiry data
     *
     * @param string $cardNumber
     * @param array $data
     * @return bool
     */
    public static function setCardInquiry(string $cardNumber, array $data): bool
    {
        try {
            $key = self::CARD_PREFIX . self::hashCardNumber($cardNumber);
            
            // Add metadata
            $cacheData = array_merge($data, [
                'cached_at' => now()->toISOString(),
                'cache_type' => 'card_inquiry',
                'card_hash' => self::hashCardNumber($cardNumber)
            ]);
            
            $result = Redis::setex($key, self::CARD_INQUIRY_TTL, json_encode($cacheData));
            return (string) $result === 'OK';
        } catch (\Exception $e) {
            Log::error('Error caching card inquiry', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Get cached IBAN inquiry data
     *
     * @param string $iban
     * @return array|null
     */
    public static function getIbanInquiry(string $iban): ?array
    {
        try {
            $key = self::IBAN_PREFIX . self::hashIban($iban);
            $cached = Redis::get($key);
            
            if ($cached) {
                $data = json_decode($cached, true);
                return $data;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting IBAN inquiry from cache', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Cache IBAN inquiry data
     *
     * @param string $iban
     * @param array $data
     * @return bool
     */
    public static function setIbanInquiry(string $iban, array $data): bool
    {
        try {
            $key = self::IBAN_PREFIX . self::hashIban($iban);
            
            // Add metadata
            $cacheData = array_merge($data, [
                'cached_at' => now()->toISOString(),
                'cache_type' => 'iban_inquiry',
                'iban_hash' => self::hashIban($iban)
            ]);
            
            $result = Redis::setex($key, self::IBAN_INQUIRY_TTL, json_encode($cacheData));
            return (string) $result === 'OK';
        } catch (\Exception $e) {
            Log::error('Error caching IBAN inquiry', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Get cached account inquiry data
     *
     * @param string $bankId
     * @param string $accountNumber
     * @return array|null
     */
    public static function getAccountInquiry(string $bankId, string $accountNumber): ?array
    {
        try {
            $key = self::ACCOUNT_PREFIX . self::hashAccount($bankId, $accountNumber);
            $cached = Redis::get($key);
            
            if ($cached) {
                $data = json_decode($cached, true);
                return $data;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting account inquiry from cache', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Cache account inquiry data
     *
     * @param string $bankId
     * @param string $accountNumber
     * @param array $data
     * @return bool
     */
    public static function setAccountInquiry(string $bankId, string $accountNumber, array $data): bool
    {
        try {
            $key = self::ACCOUNT_PREFIX . self::hashAccount($bankId, $accountNumber);
            
            // Add metadata
            $cacheData = array_merge($data, [
                'cached_at' => now()->toISOString(),
                'cache_type' => 'account_inquiry',
                'account_hash' => self::hashAccount($bankId, $accountNumber)
            ]);
            
            $result = Redis::setex($key, self::ACCOUNT_INQUIRY_TTL, json_encode($cacheData));
            $success = (string) $result === 'OK';
            
            Log::info('💾 PreviewCache: Account inquiry cached', [
                'bank_id' => $bankId,
                'account_masked' => self::maskAccountNumber($accountNumber),
                'cache_key' => $key,
                'ttl' => self::ACCOUNT_INQUIRY_TTL,
                'success' => $success,
                'redis_result' => (string) $result,
                'data_keys' => array_keys($data)
            ]);
            
            return $success;
        } catch (\Exception $e) {
            Log::error('💥 PreviewCache: Error caching account inquiry', [
                'error' => $e->getMessage(),
                'bank_id' => $bankId,
                'account_masked' => self::maskAccountNumber($accountNumber)
            ]);
            return false;
        }
    }
    
    /**
     * Get cached postal code inquiry data
     *
     * @param string $postalCode
     * @return array|null
     */
    public static function getPostalCodeInquiry(string $postalCode): ?array
    {
        try {
            $key = self::POSTAL_CODE_PREFIX . self::hashPostalCode($postalCode);
            $cached = Redis::get($key);
            
            if ($cached) {
                $data = json_decode($cached, true);
                return $data;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting postal code inquiry from cache', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Cache postal code inquiry data
     *
     * @param string $postalCode
     * @param array $data
     * @return bool
     */
    public static function setPostalCodeInquiry(string $postalCode, array $data): bool
    {
        try {
            $key = self::POSTAL_CODE_PREFIX . self::hashPostalCode($postalCode);
            
            // Add metadata
            $cacheData = array_merge($data, [
                'cached_at' => now()->toISOString(),
                'cache_type' => 'postal_code_inquiry',
                'postal_code_hash' => self::hashPostalCode($postalCode)
            ]);
            
            $result = Redis::setex($key, self::POSTAL_CODE_TTL, json_encode($cacheData));
            return (string) $result === 'OK';
        } catch (\Exception $e) {
            Log::error('Error caching postal code inquiry', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Clear cache for a specific card number
     *
     * @param string $cardNumber
     * @return bool
     */
    public static function clearCardCache(string $cardNumber): bool
    {
        try {
            $key = self::CARD_PREFIX . self::hashCardNumber($cardNumber);
            $result = Redis::del($key);
            return $result > 0;
        } catch (\Exception $e) {
            Log::error('Error clearing card cache', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Clear cache for a specific IBAN
     *
     * @param string $iban
     * @return bool
     */
    public static function clearIbanCache(string $iban): bool
    {
        try {
            $key = self::IBAN_PREFIX . self::hashIban($iban);
            $result = Redis::del($key);
            return $result > 0;
        } catch (\Exception $e) {
            Log::error('Error clearing IBAN cache', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Get cache statistics
     *
     * @return array
     */
    public static function getCacheStats(): array
    {
        try {
            $cardKeys = Redis::keys(self::CARD_PREFIX . '*');
            $ibanKeys = Redis::keys(self::IBAN_PREFIX . '*');
            $accountKeys = Redis::keys(self::ACCOUNT_PREFIX . '*');
            
            return [
                'card_inquiries' => count($cardKeys),
                'iban_inquiries' => count($ibanKeys),
                'account_inquiries' => count($accountKeys),
                'total_cached_items' => count($cardKeys) + count($ibanKeys) + count($accountKeys),
                'memory_usage' => Redis::info('memory')['used_memory_human'] ?? 'unknown'
            ];
        } catch (\Exception $e) {
            Log::error('Error getting cache stats', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Hash card number for cache key (secure)
     *
     * @param string $cardNumber
     * @return string
     */
    private static function hashCardNumber(string $cardNumber): string
    {
        return hash('sha256', 'card:' . $cardNumber . config('app.key'));
    }
    
    /**
     * Hash IBAN for cache key (secure)
     *
     * @param string $iban
     * @return string
     */
    private static function hashIban(string $iban): string
    {
        return hash('sha256', 'iban:' . $iban . config('app.key'));
    }
    
    /**
     * Hash account for cache key (secure)
     *
     * @param string $bankId
     * @param string $accountNumber
     * @return string
     */
    private static function hashAccount(string $bankId, string $accountNumber): string
    {
        return hash('sha256', 'account:' . $bankId . ':' . $accountNumber . config('app.key'));
    }
    
    /**
     * Hash postal code for cache key (secure)
     *
     * @param string $postalCode
     * @return string
     */
    private static function hashPostalCode(string $postalCode): string
    {
        return hash('sha256', 'postal:' . $postalCode . config('app.key'));
    }
    
    /**
     * Mask card number for logging
     *
     * @param string $cardNumber
     * @return string
     */
    private static function maskCardNumber(string $cardNumber): string
    {
        if (strlen($cardNumber) < 10) {
            return '****';
        }
        return substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4);
    }
    
    /**
     * Mask IBAN for logging
     *
     * @param string $iban
     * @return string
     */
    private static function maskIban(string $iban): string
    {
        if (strlen($iban) < 10) {
            return '****';
        }
        return substr($iban, 0, 8) . '****' . substr($iban, -4);
    }
    
    /**
     * Mask account number for logging
     *
     * @param string $accountNumber
     * @return string
     */
    private static function maskAccountNumber(string $accountNumber): string
    {
        if (strlen($accountNumber) < 6) {
            return '****';
        }
        return substr($accountNumber, 0, 3) . '****' . substr($accountNumber, -3);
    }
} 