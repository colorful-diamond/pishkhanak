<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Bank;
use App\Models\User;
use App\Models\ServiceCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BankSpecificServicesSeeder extends Seeder
{
    /**
     * Main services that need bank-specific sub-services
     * These include IBAN-related services and specific services mentioned by user
     */
    protected array $mainServices = [
        // IBAN-related services
        'card-iban' => 'تبدیل کارت به شبا',
        'card-account' => 'تبدیل کارت به حساب',
        'iban-account' => 'تبدیل شبا به حساب',
        'account-iban' => 'تبدیل حساب به شبا',
        'iban-check' => 'بررسی شبا',
        
        // Specific services mentioned by user
        'credit-score-rating' => 'اعتبارسنجی',
        'cheque-inquiry' => 'استعلام چک برگشتی',
        'loan-inquiry' => 'استعلام وام',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏦 Starting Bank-Specific Services Seeder...');
        
        // Get all active banks
        $banks = Bank::where('is_active', true)->orderBy('name')->get();
        
        if ($banks->isEmpty()) {
            $this->command->error('❌ No active banks found in database. Please seed banks first.');
            return;
        }
        
        $this->command->info("📊 Found {$banks->count()} active banks");
        
        // Get default user for services
        $defaultUser = User::first();
        if (!$defaultUser) {
            $this->command->error('❌ No users found. Please create a user first.');
            return;
        }
        
        $createdCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        // Process each main service
        foreach ($this->mainServices as $serviceSlug => $serviceFallbackTitle) {
            $this->command->info("🔍 Processing service: {$serviceSlug}");
            
            // Find the main service
            $mainService = Service::where('slug', $serviceSlug)
                                 ->whereNull('parent_id')
                                 ->first();
            
            if (!$mainService) {
                $this->command->warn("⚠️  Main service '{$serviceSlug}' not found. Skipping...");
                $skippedCount++;
                continue;
            }
            
            $this->command->info("✅ Found main service: {$mainService->title}");
            
            // Create sub-services for each bank
            foreach ($banks as $bank) {
                try {
                    $subService = $this->createBankSubService($mainService, $bank, $defaultUser);
                    
                    if ($subService) {
                        $createdCount++;
                        $this->command->line("   ✅ Created: {$subService->title}");
                    } else {
                        $skippedCount++;
                        $this->command->line("   ⏭️  Skipped: {$this->generateSubServiceTitle($mainService->title, $bank->name)} (already exists)");
                    }
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->command->error("   ❌ Error creating sub-service for {$bank->name}: " . $e->getMessage());
                    Log::error('BankSpecificServicesSeeder error', [
                        'service_slug' => $serviceSlug,
                        'bank_name' => $bank->name,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }
        
        // Summary
        $this->command->newLine();
        $this->command->info('📈 Seeding Summary:');
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Services Created', $createdCount],
                ['Services Skipped', $skippedCount],
                ['Errors', $errorCount],
                ['Total Banks', $banks->count()],
                ['Main Services Processed', count($this->mainServices)],
            ]
        );
        
        if ($createdCount > 0) {
            $this->command->info("🎉 Successfully created {$createdCount} bank-specific sub-services!");
        }
        
        if ($errorCount > 0) {
            $this->command->warn("⚠️  {$errorCount} errors occurred. Check logs for details.");
        }
        
        $this->command->info('✅ Bank-Specific Services Seeder completed!');
    }
    
    /**
     * Create a bank-specific sub-service
     */
    protected function createBankSubService(Service $mainService, Bank $bank, User $defaultUser): ?Service
    {
        // Generate sub-service data
        $subServiceTitle = $this->generateSubServiceTitle($mainService->title, $bank->name);
        $subServiceSlug = $this->generateSubServiceSlug($bank);
        
        // Check if sub-service already exists
        if (Service::where('parent_id', $mainService->id)
                  ->where('slug', $subServiceSlug)
                  ->exists()) {
            return null; // Already exists
        }
        
        // Create the sub-service
        $subService = Service::create([
            'title' => $subServiceTitle,
            'short_title' => $this->generateShortTitle($mainService, $bank),
            'slug' => $subServiceSlug,
            'content' => $this->generateSubServiceContent($mainService, $bank),
            'summary' => $this->generateSubServiceSummary($mainService, $bank),
            'description' => $this->generateSubServiceDescription($mainService, $bank),
            'category_id' => $mainService->category_id,
            'author_id' => $defaultUser->id,
            'parent_id' => $mainService->id, // This makes it a sub-service
            'status' => 'active',
            'featured' => false,
            
            // Copy pricing from main service
            'is_paid' => $mainService->is_paid,
            'price' => $mainService->price,
            'cost' => $mainService->cost,
            'currency' => $mainService->currency ?: 'IRT',
            
            // Copy hidden fields
            'hidden_fields' => $mainService->hidden_fields,
            
            // SEO fields
            'meta_title' => $this->generateMetaTitle($mainService, $bank),
            'meta_description' => $this->generateMetaDescription($mainService, $bank),
            'meta_keywords' => $this->generateMetaKeywords($mainService, $bank),
        ]);
        
        return $subService;
    }
    
    /**
     * Generate sub-service title
     */
    protected function generateSubServiceTitle(string $mainTitle, string $bankName): string
    {
        return "{$mainTitle} {$bankName}";
    }
    
    /**
     * Generate sub-service slug based on bank name
     */
    protected function generateSubServiceSlug(Bank $bank): string
    {
        // Use English name if available, otherwise use Persian name
        $bankIdentifier = $bank->en_name ?: $this->transliteratePersian($bank->name);
        
        // Convert to slug format
        return Str::slug(strtolower($bankIdentifier));
    }
    
    /**
     * Generate short title for sub-service
     */
    protected function generateShortTitle(Service $mainService, Bank $bank): string
    {
        $baseTitle = $mainService->short_title ?: $this->extractBaseTitle($mainService->title);
        return "{$baseTitle} {$bank->name}";
    }
    
    /**
     * Generate sub-service content
     */
    protected function generateSubServiceContent(Service $mainService, Bank $bank): string
    {
        $bankName = $bank->name;
        $mainTitle = $mainService->title;
        
        return "<div class=\"service-content bank-specific\">
            <h2>سرویس {$mainTitle} {$bankName}</h2>
            
            <p>از این سرویس می‌توانید برای {$mainTitle} در {$bankName} استفاده کنید. این سرویس ویژه {$bankName} طراحی شده و با در نظر گیری ویژگی‌های خاص این بانک، بهترین نتایج را برای شما فراهم می‌کند.</p>
            
            <h3>ویژگی‌های سرویس:</h3>
            <ul>
                <li>✅ تخصصی برای {$bankName}</li>
                <li>⚡ پردازش سریع و دقیق</li>
                <li>🔒 امنیت بالا</li>
                <li>📱 پشتیبانی از تمام دستگاه‌ها</li>
            </ul>
            
            <div class=\"bank-notice\">
                <p><strong>توجه:</strong> این سرویس ویژه {$bankName} است و برای بهترین نتایج از اطلاعات مربوط به همین بانک استفاده کنید.</p>
            </div>
        </div>";
    }
    
    /**
     * Generate sub-service summary
     */
    protected function generateSubServiceSummary(Service $mainService, Bank $bank): string
    {
        return "سرویس {$mainService->title} ویژه {$bank->name} - دقیق، سریع و امن";
    }
    
    /**
     * Generate sub-service description
     */
    protected function generateSubServiceDescription(Service $mainService, Bank $bank): string
    {
        return "با استفاده از این سرویس می‌توانید {$mainService->title} در {$bank->name} را به راحتی و با دقت بالا انجام دهید. این سرویس ویژه {$bank->name} طراحی شده است.";
    }
    
    /**
     * Generate meta title
     */
    protected function generateMetaTitle(Service $mainService, Bank $bank): string
    {
        $baseTitle = $this->extractBaseTitle($mainService->title);
        return "{$baseTitle} {$bank->name} | پیشخوانک";
    }
    
    /**
     * Generate meta description
     */
    protected function generateMetaDescription(Service $mainService, Bank $bank): string
    {
        return "سرویس {$mainService->title} ویژه {$bank->name} در پیشخوانک. دقیق، سریع و امن. ✅ پردازش فوری ⚡ بدون نیاز به مراجعه حضوری 🔒 امنیت کامل";
    }
    
    /**
     * Generate meta keywords
     */
    protected function generateMetaKeywords(Service $mainService, Bank $bank): string
    {
        $keywords = [
            $mainService->title,
            $bank->name,
            'پیشخوانک',
            'سرویس بانکی',
            'آنلاین',
        ];
        
        // Add specific keywords based on service type
        if (str_contains($mainService->slug, 'card')) {
            $keywords[] = 'کارت بانکی';
            $keywords[] = 'شماره کارت';
        }
        
        if (str_contains($mainService->slug, 'iban') || str_contains($mainService->slug, 'sheba')) {
            $keywords[] = 'شبا';
            $keywords[] = 'شماره شبا';
            $keywords[] = 'IBAN';
        }
        
        if (str_contains($mainService->slug, 'account')) {
            $keywords[] = 'حساب بانکی';
            $keywords[] = 'شماره حساب';
        }
        
        if (str_contains($mainService->slug, 'credit')) {
            $keywords[] = 'اعتبارسنجی';
            $keywords[] = 'رتبه اعتباری';
        }
        
        if (str_contains($mainService->slug, 'cheque')) {
            $keywords[] = 'چک';
            $keywords[] = 'چک برگشتی';
        }
        
        if (str_contains($mainService->slug, 'loan')) {
            $keywords[] = 'وام';
            $keywords[] = 'تسهیلات';
        }
        
        return implode(', ', array_unique($keywords));
    }
    
    /**
     * Extract base title from main service title
     */
    protected function extractBaseTitle(string $title): string
    {
        // Remove common words to get the essence of the service
        $commonWords = ['سرویس', 'خدمات', 'استعلام', 'بررسی', 'اطلاعات'];
        
        $baseTitle = $title;
        foreach ($commonWords as $word) {
            $baseTitle = str_replace($word, '', $baseTitle);
        }
        
        return trim($baseTitle);
    }
    
    /**
     * Simple transliteration for Persian bank names
     */
    protected function transliteratePersian(string $text): string
    {
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
        
        $result = strtolower($text);
        
        foreach ($persianToEnglish as $persian => $english) {
            $result = str_replace($persian, $english, $result);
        }
        
        // Remove extra spaces and replace with hyphens
        $result = preg_replace('/\s+/', '-', trim($result));
        
        // Remove any remaining Persian characters
        $result = preg_replace('/[^\w\-]/', '', $result);
        
        return $result;
    }
} 