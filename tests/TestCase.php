<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\TelegramAdmin;
use App\Models\User;
use App\Services\PersianTextValidator;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set Persian locale for tests
        config([
            'app.locale' => 'fa',
            'app.fallback_locale' => 'en',
            'services.telegram.bot_token' => 'test_token_123456789',
            'services.telegram.webhook_secret' => 'test_webhook_secret_key',
        ]);
    }

    /**
     * Create a test admin user
     */
    protected function createTestAdmin(array $attributes = []): TelegramAdmin
    {
        return TelegramAdmin::factory()->create(array_merge([
            'telegram_user_id' => '123456789',
            'username' => 'test_admin',
            'first_name' => 'علی',
            'last_name' => 'احمدی',
            'role' => 'super_admin',
            'is_active' => true,
        ], $attributes));
    }

    /**
     * Create a test regular user
     */
    protected function createTestUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'کاربر تست',
            'email' => 'test@pishkhanak.com',
            'phone' => '09123456789',
        ], $attributes));
    }

    /**
     * Create Persian text validator instance
     */
    protected function createTextValidator(): PersianTextValidator
    {
        return new PersianTextValidator();
    }

    /**
     * Generate a mock Telegram update
     */
    protected function mockTelegramUpdate(array $override = []): array
    {
        return array_merge([
            'update_id' => random_int(100000, 999999),
            'message' => [
                'message_id' => random_int(1000, 9999),
                'from' => [
                    'id' => 123456789,
                    'is_bot' => false,
                    'first_name' => 'علی',
                    'username' => 'test_user',
                    'language_code' => 'fa',
                ],
                'chat' => [
                    'id' => 123456789,
                    'first_name' => 'علی',
                    'username' => 'test_user',
                    'type' => 'private',
                ],
                'date' => time(),
                'text' => 'سلام',
            ]
        ], $override);
    }

    /**
     * Create mock webhook request
     */
    protected function createWebhookRequest(array $data = []): \Illuminate\Http\Request
    {
        $request = new \Illuminate\Http\Request();
        $request->merge($data ?: $this->mockTelegramUpdate());
        $request->headers->set('X-Telegram-Bot-Api-Secret-Token', 'test_webhook_secret_key');
        
        return $request;
    }

    /**
     * Assert Persian text contains specific content
     */
    protected function assertPersianTextContains(string $haystack, string $needle, string $message = ''): void
    {
        $this->assertStringContainsString(
            $needle,
            $haystack,
            $message ?: "Persian text '{$haystack}' should contain '{$needle}'"
        );
    }

    /**
     * Assert Persian numbers are formatted correctly
     */
    protected function assertPersianNumbers(string $text, string $message = ''): void
    {
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $hasAnyPersianDigit = false;
        
        foreach ($persianDigits as $digit) {
            if (strpos($text, $digit) !== false) {
                $hasAnyPersianDigit = true;
                break;
            }
        }
        
        $this->assertTrue(
            $hasAnyPersianDigit,
            $message ?: "Text should contain Persian numbers: {$text}"
        );
    }

    /**
     * Assert currency format is correct
     */
    protected function assertCurrencyFormat(string $amount, string $message = ''): void
    {
        // Should contain Persian digits and currency word
        $this->assertPersianNumbers($amount, $message);
        $this->assertTrue(
            str_contains($amount, 'ریال') || str_contains($amount, 'تومان'),
            $message ?: "Currency should contain ریال or تومان: {$amount}"
        );
    }

    /**
     * Create test financial transaction data
     */
    protected function createTestTransaction(int $amount = 100000, string $type = 'deposit'): array
    {
        return [
            'amount' => $amount,
            'type' => $type,
            'description' => 'تراکنش تستی',
            'reference_id' => 'TXN' . time() . random_int(1000, 9999),
            'currency' => 'IRR',
            'status' => 'pending',
        ];
    }

    /**
     * Assert security event was logged
     */
    protected function assertSecurityEventLogged(string $eventType, string $message = ''): void
    {
        $this->assertDatabaseHas('telegram_security_events', [
            'event_type' => $eventType,
        ], $message);
    }

    /**
     * Assert audit log entry exists
     */
    protected function assertAuditLogged(string $action, ?int $adminId = null): void
    {
        $conditions = ['action' => $action];
        
        if ($adminId) {
            $conditions['admin_id'] = $adminId;
        }
        
        $this->assertDatabaseHas('telegram_audit_logs', $conditions);
    }
}