<?php

namespace App\Services;

use App\Models\Bank;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class BankService
{
    const CACHE_KEY = 'banks:supported';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get all active banks with Redis caching
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getSupportedBanks()
    {
        // Try Redis first
        $redisData = Redis::get(self::CACHE_KEY);
        
        if ($redisData) {
            $banksArray = json_decode($redisData, true);
            return collect($banksArray);
        }

        // If not in Redis, get from database and cache
        $banks = Bank::where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'en_name', 'logo', 'color', 'card_prefixes']);

        // Cache in Redis
        Redis::setex(self::CACHE_KEY, self::CACHE_TTL, $banks->toJson());

        return $banks;
    }

    /**
     * Clear bank cache
     *
     * @return void
     */
    public static function clearCache()
    {
        Redis::del(self::CACHE_KEY);
        Cache::forget('banks:all');
    }

    /**
     * Get banks for slider display
     *
     * @return array
     */
    public static function getBanksForSlider()
    {
        $banks = self::getSupportedBanks();
        
        return $banks->map(function ($bank) {
            // Handle both array and object cases
            $bankData = is_array($bank) ? $bank : (array) $bank;
            
            // Generate bank slug for logo path
            $bankSlug = self::generateBankSlug($bankData['en_name'] ?? $bankData['name'] ?? '');
            
            return [
                'id' => $bankData['id'] ?? null,
                'name' => $bankData['name'] ?? '',
                'en_name' => $bankData['en_name'] ?? '',
                'fa_name' => $bankData['name'] ?? '',
                'slug' => $bankSlug,
                'logo' => self::getBankLogoUrl($bankSlug),
                'color' => $bankData['color'] ?? null,
                'card_prefixes' => $bankData['card_prefixes'] ?? [],
            ];
        })->toArray();
    }

    /**
     * Get bank information by slug
     *
     * @param string $slug
     * @return array|null
     */
    public function getBankBySlug(string $slug): ?array
    {
        $banks = self::getBanksForSlider();
        
        foreach ($banks as $bank) {
            if ($bank['slug'] === $slug) {
                return $bank;
            }
        }
        
        return null;
    }

    /**
     * Generate bank slug from bank name
     *
     * @param string $bankName
     * @return string
     */
    public static function generateBankSlug(string $bankName): string
    {
        // Persian to English mappings for common bank names
        $persianToEnglish = [
            'ملی' => 'melli',
            'ملت' => 'mellat',
            'سپه' => 'sepah',
            'پارسیان' => 'parsian',
            'پاسارگاد' => 'pasargad',
            'سامان' => 'saman',
            'کشاورزی' => 'keshavarzi',
            'صادرات' => 'saderat',
            'تجارت' => 'tejarat',
            'رفاه' => 'refah',
            'مسکن' => 'maskan',
            'شهر' => 'shahr',
            'دی' => 'day',
            'پست' => 'post',
            'توسعه' => 'tosee',
            'اقتصاد' => 'eghtesad',
            'نوین' => 'novin',
            'آینده' => 'ayandeh',
            'سینا' => 'sina',
            'کار' => 'kar',
            'آفرین' => 'afarin',
            'ایران' => 'iran',
            'زمین' => 'zamin',
            'قوامین' => 'ghavamin',
            'حکمت' => 'hekmat',
            'گردشگری' => 'gardeshgari',
            'صنعت' => 'sanat',
            'معدن' => 'madan',
            'مرکزی' => 'markazi',
            'رسالت' => 'resalat',
            'انصار' => 'ansar',
            'کوثر' => 'kosar',
            'مهر' => 'mehr',
            'ایرانیان' => 'iranian',
            'تعاون' => 'taavon',
        ];
        
        $slug = strtolower($bankName);
        
        // Replace Persian words with English equivalents
        foreach ($persianToEnglish as $persian => $english) {
            $slug = str_replace($persian, $english, $slug);
        }
        
        // Clean up the slug
        $slug = preg_replace('/[^\w\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }

    /**
     * Get bank logo URL based on slug
     *
     * @param string $slug
     * @return string
     */
    public static function getBankLogoUrl(string $slug): string
    {
        // Try multiple possible logo file extensions and locations
        $possiblePaths = [
            "assets/images/banks/{$slug}.png",
            "assets/images/banks/{$slug}.jpg",
            "assets/images/banks/{$slug}.webp",
            "assets/images/banks/{$slug}.svg",
            "assets/banks/{$slug}.png",
            "assets/banks/{$slug}.jpg",
            "assets/banks/{$slug}.webp",
            "assets/banks/{$slug}.svg",
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }
        
        // Fallback to default bank icon
        return asset('assets/images/bank-default.png');
    }
} 