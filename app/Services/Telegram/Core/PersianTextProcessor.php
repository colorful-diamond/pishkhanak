<?php

namespace App\Services\Telegram\Core;

/**
 * Persian Text Processor
 * 
 * Enhanced Persian language processing with support for:
 * - Text normalization and standardization
 * - Number conversion (Persian/Arabic to English and vice versa)
 * - Date and time formatting in Persian
 * - RTL text handling and layout
 * - Persian keyboard layout detection and conversion
 */
class PersianTextProcessor
{
    // Persian to English number mapping
    private const PERSIAN_NUMBERS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    private const ARABIC_NUMBERS = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    private const ENGLISH_NUMBERS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    // Persian characters for normalization
    private const PERSIAN_CHARS = [
        'ک' => 'ک', 'ي' => 'ی', 'ة' => 'ه', 'ء' => '',
        'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ٱ' => 'ا',
        'ؤ' => 'و', 'ئ' => 'ی', 'ى' => 'ی'
    ];

    // Persian month names
    private const PERSIAN_MONTHS = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
    ];

    // Persian weekday names
    private const PERSIAN_WEEKDAYS = [
        'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه', 'شنبه'
    ];

    /**
     * Normalize Persian text for consistent processing
     */
    public function normalizeText(string $text): string
    {
        // Convert Arabic/Persian numbers to English
        $text = $this->convertNumbersToEnglish($text);
        
        // Normalize Persian characters
        $text = strtr($text, self::PERSIAN_CHARS);
        
        // Remove extra whitespaces
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Trim whitespace
        return trim($text);
    }

    /**
     * Convert Persian/Arabic numbers to English
     */
    public function convertNumbersToEnglish(string $text): string
    {
        $text = str_replace(self::PERSIAN_NUMBERS, self::ENGLISH_NUMBERS, $text);
        $text = str_replace(self::ARABIC_NUMBERS, self::ENGLISH_NUMBERS, $text);
        return $text;
    }

    /**
     * Convert English numbers to Persian
     */
    public function convertNumbersToPersian(string $text): string
    {
        return str_replace(self::ENGLISH_NUMBERS, self::PERSIAN_NUMBERS, $text);
    }

    /**
     * Format Persian date from timestamp
     */
    public function formatPersianDate(int $timestamp, string $format = 'Y/m/d H:i'): string
    {
        // Use Verta library if available, fallback to basic formatting
        if (class_exists('\Verta')) {
            $verta = \Verta::createFromTimestamp($timestamp);
            return $this->convertNumbersToPersian($verta->format($format));
        }

        // Fallback: basic Persian date formatting
        return $this->convertNumbersToPersian(date($format, $timestamp));
    }

    /**
     * Format Persian time duration (e.g., "۳ دقیقه پیش")
     */
    public function formatPersianDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return 'همین الان';
        }

        $minutes = floor($seconds / 60);
        if ($minutes < 60) {
            $persianMinutes = $this->convertNumbersToPersian((string) $minutes);
            return $persianMinutes . ' دقیقه پیش';
        }

        $hours = floor($minutes / 60);
        if ($hours < 24) {
            $persianHours = $this->convertNumbersToPersian((string) $hours);
            return $persianHours . ' ساعت پیش';
        }

        $days = floor($hours / 24);
        if ($days < 30) {
            $persianDays = $this->convertNumbersToPersian((string) $days);
            return $persianDays . ' روز پیش';
        }

        $months = floor($days / 30);
        if ($months < 12) {
            $persianMonths = $this->convertNumbersToPersian((string) $months);
            return $persianMonths . ' ماه پیش';
        }

        $years = floor($months / 12);
        $persianYears = $this->convertNumbersToPersian((string) $years);
        return $persianYears . ' سال پیش';
    }

    /**
     * Detect if text is primarily Persian
     */
    public function isPersianText(string $text): bool
    {
        // Count Persian Unicode characters
        $persianCount = preg_match_all('/[\x{0600}-\x{06FF}]/u', $text);
        $totalChars = mb_strlen(preg_replace('/\s/', '', $text));
        
        if ($totalChars === 0) {
            return false;
        }
        
        // Consider text Persian if more than 30% is Persian characters
        return ($persianCount / $totalChars) > 0.3;
    }

    /**
     * Add RTL markers for proper text display
     */
    public function addRtlMarkers(string $text): string
    {
        if ($this->isPersianText($text)) {
            return "\u{202B}" . $text . "\u{202C}"; // RLE + text + PDF
        }
        
        return $text;
    }

    /**
     * Clean RTL markers and dangerous characters
     */
    public function cleanRtlMarkers(string $text): string
    {
        // Remove dangerous RTL override characters
        $dangerous = [
            "\u{202D}", // LEFT-TO-RIGHT OVERRIDE
            "\u{202E}", // RIGHT-TO-LEFT OVERRIDE  
            "\u{061C}", // ARABIC LETTER MARK
            "\u{200E}", // LEFT-TO-RIGHT MARK
            "\u{200F}", // RIGHT-TO-LEFT MARK
        ];
        
        return str_replace($dangerous, '', $text);
    }

    /**
     * Format Persian currency
     */
    public function formatPersianCurrency(int $amount, string $currency = 'تومان'): string
    {
        // Format number with thousands separator
        $formatted = number_format($amount);
        
        // Convert to Persian numbers
        $persianFormatted = $this->convertNumbersToPersian($formatted);
        
        return $persianFormatted . ' ' . $currency;
    }

    /**
     * Generate Persian ordinal numbers (اول، دوم، سوم، ...)
     */
    public function getPersianOrdinal(int $number): string
    {
        $ordinals = [
            1 => 'اول', 2 => 'دوم', 3 => 'سوم', 4 => 'چهارم', 5 => 'پنجم',
            6 => 'ششم', 7 => 'هفتم', 8 => 'هشتم', 9 => 'نهم', 10 => 'دهم',
            11 => 'یازدهم', 12 => 'دوازدهم', 13 => 'سیزدهم', 14 => 'چهاردهم', 15 => 'پانزدهم',
            16 => 'شانزدهم', 17 => 'هفدهم', 18 => 'هجدهم', 19 => 'نوزدهم', 20 => 'بیستم'
        ];

        return $ordinals[$number] ?? $this->convertNumbersToPersian((string) $number) . 'م';
    }

    /**
     * Convert English keyboard input to Persian (common typos)
     */
    public function convertEnglishKeyboardToPersian(string $text): string
    {
        $keyboardMap = [
            'q' => 'ض', 'w' => 'ص', 'e' => 'ث', 'r' => 'ق', 't' => 'ف',
            'y' => 'غ', 'u' => 'ع', 'i' => 'ه', 'o' => 'خ', 'p' => 'ح',
            'a' => 'ش', 's' => 'س', 'd' => 'ی', 'f' => 'ب', 'g' => 'ل',
            'h' => 'ا', 'j' => 'ت', 'k' => 'ن', 'l' => 'م', 'z' => 'ظ',
            'x' => 'ط', 'c' => 'ز', 'v' => 'ر', 'b' => 'ذ', 'n' => 'د',
            'm' => 'پ', 'Q' => 'ْ', 'W' => 'ٌ', 'E' => 'ٍ', 'R' => 'ً',
            'T' => 'ُ', 'Y' => 'ِ', 'U' => 'َ', 'I' => ']', 'O' => '[',
            'P' => '}', 'A' => 'ؤ', 'S' => 'ئ', 'D' => 'ي', 'F' => 'إ',
            'G' => 'أ', 'H' => 'آ', 'J' => 'ة', 'K' => '«', 'L' => '»',
            'Z' => 'ك', 'X' => 'ٓ', 'C' => 'ژ', 'V' => 'ٰ', 'B' => '‌',
            'N' => 'ء', 'M' => 'ؤ'
        ];

        return strtr($text, $keyboardMap);
    }

    /**
     * Smart text correction for common Persian input issues
     */
    public function smartCorrectPersianText(string $text): string
    {
        // First try English keyboard to Persian conversion
        if (!$this->isPersianText($text) && strlen($text) > 0) {
            $converted = $this->convertEnglishKeyboardToPersian($text);
            if ($this->isPersianText($converted)) {
                $text = $converted;
            }
        }

        // Normalize the text
        $text = $this->normalizeText($text);

        // Fix common spacing issues around Persian punctuation
        $text = preg_replace('/\s*([،؛؟!])\s*/', '$1 ', $text);
        $text = preg_replace('/\s*([:])\s*/', '$1 ', $text);

        // Remove extra spaces
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Generate Persian text summary
     */
    public function generateSummary(string $text, int $maxLength = 100): string
    {
        $text = $this->normalizeText($text);
        
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        // Find the best breaking point (end of sentence or word)
        $summary = mb_substr($text, 0, $maxLength);
        
        // Try to break at sentence end
        $lastSentence = mb_strrpos($summary, '.');
        if ($lastSentence === false) {
            $lastSentence = mb_strrpos($summary, '؟');
        }
        if ($lastSentence === false) {
            $lastSentence = mb_strrpos($summary, '!');
        }
        
        if ($lastSentence !== false && $lastSentence > $maxLength * 0.7) {
            return mb_substr($summary, 0, $lastSentence + 1);
        }

        // Break at word boundary
        $lastSpace = mb_strrpos($summary, ' ');
        if ($lastSpace !== false && $lastSpace > $maxLength * 0.8) {
            return mb_substr($summary, 0, $lastSpace) . '...';
        }

        return $summary . '...';
    }

    /**
     * Validate Persian text input
     */
    public function validatePersianInput(string $text, array $options = []): array
    {
        $errors = [];
        
        $minLength = $options['min_length'] ?? 1;
        $maxLength = $options['max_length'] ?? 4000;
        $requirePersian = $options['require_persian'] ?? false;

        // Length validation
        if (mb_strlen($text) < $minLength) {
            $errors[] = "متن باید حداقل {$minLength} کاراکتر باشد";
        }

        if (mb_strlen($text) > $maxLength) {
            $errors[] = "متن نمی‌تواند بیش از {$maxLength} کاراکتر باشد";
        }

        // Persian requirement validation
        if ($requirePersian && !$this->isPersianText($text)) {
            $errors[] = "متن باید شامل حروف فارسی باشد";
        }

        // Check for dangerous characters
        $cleanedText = $this->cleanRtlMarkers($text);
        if ($cleanedText !== $text) {
            $errors[] = "متن شامل کاراکترهای غیرمجاز است";
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'cleaned_text' => $this->smartCorrectPersianText($cleanedText),
            'is_persian' => $this->isPersianText($text),
            'length' => mb_strlen($text)
        ];
    }
}