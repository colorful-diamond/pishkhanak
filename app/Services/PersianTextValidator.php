<?php

namespace App\Services;

use Normalizer;
use Illuminate\Support\Facades\Log;

/**
 * Persian Text Security Validator
 * 
 * Provides comprehensive security validation for Persian text input
 * in the Telegram bot system, protecting against RTL injection attacks
 * and ensuring compliance with Iranian banking text standards.
 */
class PersianTextValidator
{
    /**
     * Persian character ranges (Unicode)
     */
    private const PERSIAN_MAIN = '\u0600-\u06FF';  // Arabic and Persian
    private const PERSIAN_EXTENDED = '\u0750-\u077F';  // Arabic Extended
    private const PERSIAN_SUPPLEMENT = '\uFB50-\uFDFF';  // Arabic Presentation Forms-A
    private const PERSIAN_FORMS_B = '\uFE70-\uFEFF';  // Arabic Presentation Forms-B

    /**
     * Dangerous RTL control characters to filter
     */
    private const DANGEROUS_RTL_CHARS = [
        "\u{202D}",  // LEFT-TO-RIGHT OVERRIDE
        "\u{202E}",  // RIGHT-TO-LEFT OVERRIDE  
        "\u{061C}",  // ARABIC LETTER MARK
        "\u{200E}",  // LEFT-TO-RIGHT MARK
        "\u{200F}",  // RIGHT-TO-LEFT MARK
    ];

    /**
     * Maximum allowed text length for different contexts
     */
    private const MAX_LENGTHS = [
        'command' => 100,
        'message' => 4096,  // Telegram limit
        'ticket_subject' => 200,
        'ticket_description' => 2000,
    ];

    /**
     * Sanitize Persian input text for security
     *
     * @param string $text Raw input text
     * @param string $context Context type (command, message, etc.)
     * @return string Sanitized text
     * @throws \InvalidArgumentException If text contains dangerous content
     */
    public function sanitizePersianInput(string $text, string $context = 'message'): string
    {
        // Step 1: Unicode normalization
        $normalized = Normalizer::normalize($text, Normalizer::FORM_C);
        
        if ($normalized === false) {
            throw new \InvalidArgumentException('Invalid Unicode text provided');
        }

        // Step 2: Length validation
        $maxLength = self::MAX_LENGTHS[$context] ?? self::MAX_LENGTHS['message'];
        if (mb_strlen($normalized, 'UTF-8') > $maxLength) {
            throw new \InvalidArgumentException("Text too long for context: {$context}");
        }

        // Step 3: Remove dangerous RTL control characters
        $cleaned = $this->removeDangerousRtlChars($normalized);

        // Step 4: Validate character ranges
        if (!$this->isValidPersianText($cleaned)) {
            Log::warning('Invalid characters detected in Persian text', [
                'original_length' => mb_strlen($text),
                'context' => $context,
                'suspicious_chars' => $this->findSuspiciousChars($cleaned)
            ]);
            
            throw new \InvalidArgumentException('Text contains invalid or potentially dangerous characters');
        }

        // Step 5: Additional financial context validation
        if (in_array($context, ['ticket_subject', 'ticket_description'])) {
            $cleaned = $this->validateFinancialTerms($cleaned);
        }

        return $cleaned;
    }

    /**
     * Remove dangerous RTL control characters
     */
    private function removeDangerousRtlChars(string $text): string
    {
        // Replace dangerous RTL characters one by one
        foreach (self::DANGEROUS_RTL_CHARS as $char) {
            $text = str_replace($char, '', $text);
        }
        return $text;
    }

    /**
     * Validate that text contains only allowed Persian characters
     */
    private function isValidPersianText(string $text): bool
    {
        // Allow Persian, digits, whitespace, and common punctuation
        $allowedPattern = '/^['
            . self::PERSIAN_MAIN
            . self::PERSIAN_EXTENDED
            . self::PERSIAN_SUPPLEMENT
            . self::PERSIAN_FORMS_B
            . '\s'          // Whitespace
            . '\d'          // Digits
            . '\p{P}'       // Punctuation
            . ']*$/u';

        return preg_match($allowedPattern, $text) === 1;
    }

    /**
     * Find suspicious characters for logging
     */
    private function findSuspiciousChars(string $text): array
    {
        $suspicious = [];
        $chars = mb_str_split($text, 1, 'UTF-8');
        
        foreach ($chars as $char) {
            $codePoint = mb_ord($char, 'UTF-8');
            
            // Check if character is outside expected ranges
            if (!$this->isCharacterAllowed($codePoint)) {
                $suspicious[] = [
                    'char' => $char,
                    'unicode' => sprintf('U+%04X', $codePoint),
                    'description' => $this->getCharacterDescription($codePoint)
                ];
            }
        }
        
        return $suspicious;
    }

    /**
     * Check if a Unicode code point is allowed
     */
    private function isCharacterAllowed(int $codePoint): bool
    {
        // Persian ranges
        if (($codePoint >= 0x0600 && $codePoint <= 0x06FF) ||  // Arabic/Persian
            ($codePoint >= 0x0750 && $codePoint <= 0x077F) ||  // Arabic Extended
            ($codePoint >= 0xFB50 && $codePoint <= 0xFDFF) ||  // Arabic Presentation Forms-A
            ($codePoint >= 0xFE70 && $codePoint <= 0xFEFF)) {  // Arabic Presentation Forms-B
            return true;
        }

        // Basic Latin (digits and punctuation)
        if ($codePoint >= 0x0020 && $codePoint <= 0x007F) {
            return true;
        }

        // Whitespace
        if (in_array($codePoint, [0x0009, 0x000A, 0x000D, 0x0020])) {
            return true;
        }

        return false;
    }

    /**
     * Get description for suspicious characters
     */
    private function getCharacterDescription(int $codePoint): string
    {
        // Map of suspicious character ranges to descriptions
        $ranges = [
            ['range' => [0x200E, 0x200F], 'desc' => 'Bidirectional control character'],
            ['range' => [0x202A, 0x202E], 'desc' => 'Bidirectional embedding/override'],
            ['range' => [0x2060, 0x206F], 'desc' => 'Invisible formatting character'],
            ['range' => [0xFEFF, 0xFEFF], 'desc' => 'Byte order mark'],
        ];

        foreach ($ranges as $item) {
            if ($codePoint >= $item['range'][0] && $codePoint <= $item['range'][1]) {
                return $item['desc'];
            }
        }

        return 'Unrecognized character';
    }

    /**
     * Validate financial terms and compliance
     */
    private function validateFinancialTerms(string $text): string
    {
        // Iranian banking compliance - ensure proper terminology
        $financialTerms = [
            'ریال' => 'ریال',  // Iranian Rial
            'تومان' => 'تومان',  // Toman
            'بانک' => 'بانک',   // Bank
            'کارت' => 'کارت',   // Card
            'پرداخت' => 'پرداخت', // Payment
        ];

        // Log usage of financial terms for compliance monitoring
        foreach ($financialTerms as $term => $standardForm) {
            if (mb_strpos($text, $term, 0, 'UTF-8') !== false) {
                Log::info('Financial term used in Telegram bot', [
                    'term' => $term,
                    'context' => 'telegram_input',
                    'timestamp' => now()->toISOString()
                ]);
            }
        }

        return $text;
    }

    /**
     * Validate Persian command syntax
     */
    public function validateCommand(string $command): bool
    {
        // Commands should start with / and contain only allowed characters
        if (!str_starts_with($command, '/')) {
            return false;
        }

        $commandText = substr($command, 1);
        
        // Allow English commands and Persian parameters
        $pattern = '/^[a-zA-Z0-9_' . self::PERSIAN_MAIN . '\s]*$/u';
        
        return preg_match($pattern, $commandText) === 1;
    }

    /**
     * Format Persian numbers (convert to Persian digits)
     */
    public function formatPersianNumbers(string $text): string
    {
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        
        return str_replace($englishDigits, $persianDigits, $text);
    }

    /**
     * Validate and sanitize Persian currency amounts
     */
    public function validateCurrencyAmount(string $amount): ?int
    {
        // Remove Persian digits and convert to English
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        $normalized = str_replace($persianDigits, $englishDigits, $amount);
        
        // Remove common Persian currency words
        $normalized = str_replace(['ریال', 'تومان', '،', ','], '', $normalized);
        $normalized = trim($normalized);
        
        // Validate as integer
        if (!ctype_digit($normalized)) {
            return null;
        }
        
        $value = intval($normalized);
        
        // Validate reasonable range for Iranian currency
        if ($value < 1000 || $value > 999999999999) { // 1000 Rial to 999 billion Rial
            return null;
        }
        
        return $value;
    }
}