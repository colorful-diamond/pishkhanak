<?php

namespace Tests\Feature\Telegram;

use Tests\TestCase;
use App\Services\Telegram\Core\PersianTextProcessor;
use App\Services\Telegram\Core\UpdateContext;
use App\Services\Telegram\Core\MessageRouter;
use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use App\Services\Telegram\Contracts\TicketRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Telegram Architecture Test Suite
 * 
 * Comprehensive testing of the new clean architecture
 * including Persian text processing and command handling
 */
class TelegramArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected PersianTextProcessor $textProcessor;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->textProcessor = new PersianTextProcessor();
        
        // Set up test environment variables
        config([
            'services.telegram.bot_token' => 'test_token_123',
            'services.telegram.webhook_secret' => 'test_secret',
        ]);
    }

    /**
     * Test Persian text processor functionality
     */
    public function test_persian_text_processing()
    {
        // Test number conversion
        $persianNumbers = 'شماره تیکت: ۱۲۳۴۵';
        $englishNumbers = $this->textProcessor->convertNumbersToEnglish($persianNumbers);
        $this->assertEquals('شماره تیکت: 12345', $englishNumbers);

        // Test Persian number formatting
        $englishText = 'Ticket: 12345';
        $persianFormatted = $this->textProcessor->convertNumbersToPersian($englishText);
        $this->assertEquals('Ticket: ۱۲۳۴۵', $persianFormatted);

        // Test Persian text detection
        $persianText = 'سلام، چطور می‌تونم کمک کنم؟';
        $this->assertTrue($this->textProcessor->isPersianText($persianText));

        $englishText = 'Hello, how can I help you?';
        $this->assertFalse($this->textProcessor->isPersianText($englishText));

        // Test text normalization
        $messyText = 'سلام    چطوري؟   ';
        $normalized = $this->textProcessor->normalizeText($messyText);
        $this->assertEquals('سلام چطوري؟', $normalized);
    }

    /**
     * Test Persian input validation
     */
    public function test_persian_input_validation()
    {
        // Valid Persian input
        $validText = 'مشکل در پرداخت دارم';
        $validation = $this->textProcessor->validatePersianInput($validText, [
            'min_length' => 5,
            'max_length' => 100
        ]);
        $this->assertTrue($validation['is_valid']);
        $this->assertEmpty($validation['errors']);

        // Too short input
        $shortText = 'سلام';
        $validation = $this->textProcessor->validatePersianInput($shortText, [
            'min_length' => 10,
            'max_length' => 100
        ]);
        $this->assertFalse($validation['is_valid']);
        $this->assertNotEmpty($validation['errors']);

        // Input with dangerous RTL characters
        $dangerousText = "سلام\u{202E}TEST\u{202D}";
        $validation = $this->textProcessor->validatePersianInput($dangerousText);
        $this->assertFalse($validation['is_valid']);
    }

    /**
     * Test Persian date and time formatting
     */
    public function test_persian_date_formatting()
    {
        $timestamp = 1704067200; // 2024-01-01 00:00:00
        $formatted = $this->textProcessor->formatPersianDate($timestamp, 'Y/m/d');
        
        // Should contain Persian numbers
        $this->assertStringContainsString('۰', $formatted);
        
        // Test duration formatting
        $duration = $this->textProcessor->formatPersianDuration(3600); // 1 hour
        $this->assertStringContainsString('ساعت', $duration);
        
        $shortDuration = $this->textProcessor->formatPersianDuration(30); // 30 seconds
        $this->assertEquals('همین الان', $shortDuration);
    }

    /**
     * Test Persian currency formatting
     */
    public function test_persian_currency_formatting()
    {
        $amount = 1250000;
        $formatted = $this->textProcessor->formatPersianCurrency($amount);
        
        $this->assertStringContainsString('۱،۲۵۰،۰۰۰', $formatted);
        $this->assertStringContainsString('تومان', $formatted);
        
        // Test with custom currency
        $rialFormatted = $this->textProcessor->formatPersianCurrency($amount, 'ریال');
        $this->assertStringContainsString('ریال', $rialFormatted);
    }

    /**
     * Test keyboard conversion functionality
     */
    public function test_keyboard_conversion()
    {
        // Test English keyboard to Persian
        $englishInput = 'salam';
        $persianOutput = $this->textProcessor->convertEnglishKeyboardToPersian($englishInput);
        $this->assertEquals('سلام', $persianOutput);
        
        // Test smart text correction
        $messyInput = 'salam    chetori?';
        $corrected = $this->textProcessor->smartCorrectPersianText($messyInput);
        $this->assertEquals('سلام چطوری؟', $corrected);
    }

    /**
     * Test text summarization
     */
    public function test_text_summarization()
    {
        $longText = 'این یک متن طولانی است که باید خلاصه شود. ' .
                   'این متن شامل چندین جمله است و برای تست عملکرد ' .
                   'سیستم خلاصه‌سازی متن استفاده می‌شود. ' .
                   'امیدوارم که نتیجه مناسبی دریافت کنیم.';
        
        $summary = $this->textProcessor->generateSummary($longText, 50);
        
        $this->assertLessThanOrEqual(53, mb_strlen($summary)); // 50 + "..."
        $this->assertTrue(mb_strlen($summary) < mb_strlen($longText));
    }

    /**
     * Test UpdateContext creation and data extraction
     */
    public function test_update_context_creation()
    {
        $mockUpdate = [
            'update_id' => 123456,
            'message' => [
                'message_id' => 789,
                'from' => [
                    'id' => 987654321,
                    'first_name' => 'علی',
                    'username' => 'ali_user',
                ],
                'chat' => [
                    'id' => 987654321,
                    'type' => 'private',
                ],
                'date' => time(),
                'text' => '/start سلام'
            ]
        ];

        $context = UpdateContext::fromArray($mockUpdate);
        
        $this->assertEquals(123456, $context->getUpdateId());
        $this->assertEquals('message', $context->getType());
        $this->assertEquals('987654321', $context->getUserId());
        $this->assertEquals('987654321', $context->getChatId());
        $this->assertEquals('/start سلام', $context->getText());
        $this->assertTrue($context->isCommand());
        $this->assertEquals('start', $context->getCommand());
        $this->assertEquals(['سلام'], $context->getCommandArgs());
        $this->assertTrue($context->hasPersianText());
        $this->assertTrue($context->isPrivateChat());
    }

    /**
     * Test command routing functionality
     */
    public function test_command_routing()
    {
        // Mock the API client since we can't make real HTTP calls in tests
        $mockApiClient = $this->createMock(TelegramApiClientInterface::class);
        $this->app->instance(TelegramApiClientInterface::class, $mockApiClient);
        
        $router = app(MessageRouter::class);
        
        // Test that commands are properly registered
        $registeredCommands = $router->getRegisteredCommands();
        
        $expectedCommands = [
            'start', 'help', 'about', 'راهنما', 'درباره',
            'tickets', 'ticket', 'تیکت', 'تیکت‌ها',
            'admin', 'stats', 'users', 'broadcast', 'tickets_admin', 'system', 'مدیریت'
        ];
        
        foreach ($expectedCommands as $command) {
            $this->assertTrue($router->hasCommand($command), "Command '{$command}' should be registered");
        }
    }

    /**
     * Test Persian command aliases
     */
    public function test_persian_command_aliases()
    {
        $mockApiClient = $this->createMock(TelegramApiClientInterface::class);
        $this->app->instance(TelegramApiClientInterface::class, $mockApiClient);
        
        $router = app(MessageRouter::class);
        
        // Test Persian aliases
        $this->assertTrue($router->hasCommand('راهنما')); // Persian for help
        $this->assertTrue($router->hasCommand('درباره')); // Persian for about  
        $this->assertTrue($router->hasCommand('تیکت')); // Persian for ticket
        $this->assertTrue($router->hasCommand('مدیریت')); // Persian for admin
    }

    /**
     * Test service provider dependency injection
     */
    public function test_service_provider_bindings()
    {
        // Test core services are bound
        $this->assertTrue($this->app->bound(PersianTextProcessor::class));
        $this->assertTrue($this->app->bound(TicketRepositoryInterface::class));
        
        // Test singletons
        $processor1 = app(PersianTextProcessor::class);
        $processor2 = app(PersianTextProcessor::class);
        $this->assertSame($processor1, $processor2);
    }

    /**
     * Test RTL text handling and security
     */
    public function test_rtl_security()
    {
        // Test dangerous RTL character removal
        $dangerousText = "Hello\u{202E}DANGER\u{202D}World";
        $cleaned = $this->textProcessor->cleanRtlMarkers($dangerousText);
        $this->assertEquals('HelloDANGERWorld', $cleaned);
        
        // Test safe RTL marker addition
        $persianText = 'سلام دنیا';
        $withRtl = $this->textProcessor->addRtlMarkers($persianText);
        $this->assertStringContainsString($persianText, $withRtl);
        $this->assertTrue(mb_strlen($withRtl) > mb_strlen($persianText));
    }

    /**
     * Test Persian ordinal number generation
     */
    public function test_persian_ordinals()
    {
        $this->assertEquals('اول', $this->textProcessor->getPersianOrdinal(1));
        $this->assertEquals('دوم', $this->textProcessor->getPersianOrdinal(2));
        $this->assertEquals('سوم', $this->textProcessor->getPersianOrdinal(3));
        $this->assertEquals('دهم', $this->textProcessor->getPersianOrdinal(10));
        
        // Test numbers beyond predefined range
        $ordinal25 = $this->textProcessor->getPersianOrdinal(25);
        $this->assertStringContainsString('۲۵', $ordinal25);
        $this->assertStringContainsString('م', $ordinal25);
    }

    /**
     * Integration test for webhook processing workflow
     */
    public function test_webhook_processing_integration()
    {
        // This would test the full webhook processing pipeline
        // For now, just verify the components exist and are properly configured
        
        $this->assertTrue(class_exists(\App\Services\Telegram\Core\WebhookProcessor::class));
        $this->assertTrue(class_exists(\App\Services\Telegram\Core\MessageRouter::class));
        $this->assertTrue(class_exists(\App\Services\Telegram\Core\UpdateContext::class));
        $this->assertTrue(class_exists(\App\Services\Telegram\Core\ProcessingResult::class));
        
        // Verify the architecture follows SOLID principles
        $this->assertTrue(interface_exists(\App\Services\Telegram\Contracts\TelegramApiClientInterface::class));
        $this->assertTrue(interface_exists(\App\Services\Telegram\Contracts\TicketRepositoryInterface::class));
        $this->assertTrue(interface_exists(\App\Services\Telegram\Contracts\CommandHandlerInterface::class));
    }
}