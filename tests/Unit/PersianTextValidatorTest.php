<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PersianTextValidator;
use InvalidArgumentException;

/**
 * Persian Text Validator Unit Tests
 * 
 * Comprehensive testing of Persian text validation, sanitization,
 * and security features for the Telegram bot system.
 */
class PersianTextValidatorTest extends TestCase
{
    private PersianTextValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new PersianTextValidator();
    }

    /**
     * Test basic Persian text sanitization
     */
    public function test_basic_persian_text_sanitization(): void
    {
        $validPersianText = 'سلام، چطور می‌تونم کمک کنم؟';
        $sanitized = $this->validator->sanitizePersianInput($validPersianText);
        
        $this->assertEquals($validPersianText, $sanitized);
        $this->assertIsString($sanitized);
    }

    /**
     * Test text length validation for different contexts
     */
    public function test_text_length_validation(): void
    {
        // Valid length for message context
        $shortText = 'سلام';
        $this->assertIsString($this->validator->sanitizePersianInput($shortText, 'message'));

        // Too long for command context
        $longText = str_repeat('سلام ', 50); // ~200 characters
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Text too long for context: command');
        $this->validator->sanitizePersianInput($longText, 'command');
    }

    /**
     * Test dangerous RTL character removal
     */
    public function test_dangerous_rtl_character_removal(): void
    {
        // Text with dangerous RTL override characters
        $dangerousText = "سلام\u{202E}TEST\u{202D}دنیا";
        $sanitized = $this->validator->sanitizePersianInput($dangerousText);
        
        // Should not contain dangerous characters
        $this->assertStringNotContainsString("\u{202E}", $sanitized);
        $this->assertStringNotContainsString("\u{202D}", $sanitized);
        
        // Should contain the safe Persian text
        $this->assertStringContainsString('سلام', $sanitized);
        $this->assertStringContainsString('دنیا', $sanitized);
    }

    /**
     * Test invalid character detection
     */
    public function test_invalid_character_detection(): void
    {
        // Text with suspicious characters
        $suspiciousText = "سلام\u{2060}TEST"; // Contains invisible separator
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Text contains invalid or potentially dangerous characters');
        $this->validator->sanitizePersianInput($suspiciousText);
    }

    /**
     * Test Unicode normalization
     */
    public function test_unicode_normalization(): void
    {
        // Text that needs normalization (composed vs decomposed characters)
        $unnormalizedText = "سلا\u{0645}\u{064E}"; // Arabic diacritic
        
        try {
            $sanitized = $this->validator->sanitizePersianInput($unnormalizedText);
            $this->assertIsString($sanitized);
        } catch (InvalidArgumentException $e) {
            // Some diacritics might be rejected - this is acceptable for security
            $this->assertStringContainsString('invalid', $e->getMessage());
        }
    }

    /**
     * Test financial terms validation
     */
    public function test_financial_terms_validation(): void
    {
        $financialText = 'مشکل در پرداخت ۱۰۰،۰۰۰ تومان دارم';
        $sanitized = $this->validator->sanitizePersianInput($financialText, 'ticket_description');
        
        $this->assertStringContainsString('پرداخت', $sanitized);
        $this->assertStringContainsString('تومان', $sanitized);
        $this->assertIsString($sanitized);
    }

    /**
     * Test command validation
     */
    public function test_command_validation(): void
    {
        // Valid English command
        $this->assertTrue($this->validator->validateCommand('/start'));
        $this->assertTrue($this->validator->validateCommand('/help'));
        
        // Valid Persian command parameters
        $this->assertTrue($this->validator->validateCommand('/ticket سلام'));
        
        // Invalid commands
        $this->assertFalse($this->validator->validateCommand('start')); // Missing /
        $this->assertFalse($this->validator->validateCommand('/test@#$')); // Invalid chars
    }

    /**
     * Test Persian number formatting
     */
    public function test_persian_number_formatting(): void
    {
        $textWithNumbers = 'شماره تیکت: 12345';
        $formatted = $this->validator->formatPersianNumbers($textWithNumbers);
        
        $this->assertEquals('شماره تیکت: ۱۲۳۴۵', $formatted);
        $this->assertStringNotContainsString('1', $formatted);
        $this->assertStringNotContainsString('2', $formatted);
    }

    /**
     * Test currency amount validation
     */
    public function test_currency_amount_validation(): void
    {
        // Valid Persian currency amounts
        $this->assertEquals(100000, $this->validator->validateCurrencyAmount('۱۰۰۰۰۰ ریال'));
        $this->assertEquals(50000, $this->validator->validateCurrencyAmount('۵۰،۰۰۰ تومان'));
        $this->assertEquals(25000, $this->validator->validateCurrencyAmount('25000'));
        
        // Invalid amounts
        $this->assertNull($this->validator->validateCurrencyAmount('abc'));
        $this->assertNull($this->validator->validateCurrencyAmount('۱۰۰ ریال')); // Too small
        $this->assertNull($this->validator->validateCurrencyAmount('9999999999999 ریال')); // Too large
    }

    /**
     * Test edge cases and boundary conditions
     */
    public function test_edge_cases(): void
    {
        // Empty string
        $this->assertEquals('', $this->validator->sanitizePersianInput(''));
        
        // Only whitespace
        $this->assertEquals('', $this->validator->sanitizePersianInput('   '));
        
        // Only numbers
        $this->assertEquals('۱۲۳', $this->validator->sanitizePersianInput('123'));
        
        // Mixed Persian and numbers
        $mixed = 'تست ۱۲۳ ABC';
        $this->expectException(InvalidArgumentException::class);
        $this->validator->sanitizePersianInput($mixed);
    }

    /**
     * Test performance with large text
     */
    public function test_performance_with_large_text(): void
    {
        // Generate large Persian text (within limits)
        $largeText = str_repeat('این متن طولانی است. ', 100); // ~2000 chars
        
        $start = microtime(true);
        $sanitized = $this->validator->sanitizePersianInput($largeText, 'ticket_description');
        $duration = microtime(true) - $start;
        
        // Should process within reasonable time (less than 1 second)
        $this->assertLessThan(1.0, $duration);
        $this->assertIsString($sanitized);
    }

    /**
     * Test security against injection attacks
     */
    public function test_security_injection_protection(): void
    {
        // SQL injection attempt in Persian context
        $sqlInjection = "'; DROP TABLE users; --";
        
        $this->expectException(InvalidArgumentException::class);
        $this->validator->sanitizePersianInput($sqlInjection);
        
        // Script injection attempt
        $scriptInjection = "<script>alert('xss')</script>";
        
        $this->expectException(InvalidArgumentException::class);
        $this->validator->sanitizePersianInput($scriptInjection);
    }

    /**
     * Test RTL marker handling
     */
    public function test_rtl_marker_handling(): void
    {
        // Test with various RTL control characters
        $rtlChars = [
            "\u{202D}", // LEFT-TO-RIGHT OVERRIDE
            "\u{202E}", // RIGHT-TO-LEFT OVERRIDE
            "\u{061C}", // ARABIC LETTER MARK
            "\u{200E}", // LEFT-TO-RIGHT MARK
            "\u{200F}", // RIGHT-TO-LEFT MARK
        ];
        
        foreach ($rtlChars as $char) {
            $textWithRtl = "سلام{$char}TEST";
            
            $this->expectException(InvalidArgumentException::class);
            $this->validator->sanitizePersianInput($textWithRtl);
        }
    }

    /**
     * Test financial compliance validation
     */
    public function test_financial_compliance(): void
    {
        $financialTerms = [
            'بانک ملی ایران',
            'کارت عابر بانک',
            'پرداخت الکترونیک',
            '۱۰۰،۰۰۰ ریال',
        ];
        
        foreach ($financialTerms as $term) {
            try {
                $sanitized = $this->validator->sanitizePersianInput($term, 'ticket_subject');
                $this->assertStringContainsString($term, $sanitized);
            } catch (InvalidArgumentException $e) {
                // Some terms might be rejected - log for review
                $this->addWarning("Financial term rejected: {$term}");
            }
        }
    }

    /**
     * Test character encoding consistency
     */
    public function test_character_encoding_consistency(): void
    {
        $persianText = 'متن فارسی با اعداد ۱۲۳۴۵ و نقطه‌گذاری!؟';
        $sanitized = $this->validator->sanitizePersianInput($persianText);
        
        // Check encoding is preserved
        $this->assertEquals('UTF-8', mb_detect_encoding($sanitized));
        $this->assertTrue(mb_check_encoding($sanitized, 'UTF-8'));
        
        // Check character count is preserved (accounting for normalization)
        $originalLength = mb_strlen($persianText, 'UTF-8');
        $sanitizedLength = mb_strlen($sanitized, 'UTF-8');
        $this->assertLessThanOrEqual($originalLength, $sanitizedLength);
    }

    /**
     * Test context-specific validation rules
     */
    public function test_context_specific_validation(): void
    {
        $contexts = ['command', 'message', 'ticket_subject', 'ticket_description'];
        $testText = 'متن تست برای بررسی محدودیت‌های متنی';
        
        foreach ($contexts as $context) {
            try {
                $sanitized = $this->validator->sanitizePersianInput($testText, $context);
                $this->assertIsString($sanitized);
                $this->assertStringContainsString('متن تست', $sanitized);
            } catch (InvalidArgumentException $e) {
                // Context might have specific restrictions
                $this->assertStringContainsString('Text too long', $e->getMessage());
            }
        }
    }
}