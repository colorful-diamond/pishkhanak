<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Services\AiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateServicesContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan services:generate-content --limit=50 --dry-run
     */
    protected $signature = 'services:generate-content {--limit=50} {--dry-run}';

    /**
     * The console command description.
     */
    protected $description = 'Generate rich SEO-optimised HTML content for service pages that are missing it.';

    public function handle(AiService $aiService): int
    {
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        $services = Service::query()
            ->whereNotNull('parent_slug')
            ->where(function ($q) {
                $q->whereNull('done')->orWhere('done', false);
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($services->isEmpty()) {
            $this->info('Nothing to generate. All services already have content.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($services->count());
        $bar->start();

        foreach ($services as $service) {
            // Build AI prompt
            $prompt = $this->buildPrompt($service);

            // Call the AI model via AiService
            $html = $aiService->generateHtmlContent($prompt);

            // Save the file
            $filePath = "generated_service_contents/{$service->id}.html";
            if (!$dryRun) {
                Storage::disk('local')->put($filePath, $html);

                // Mark as done
                $service->forceFill(['done' => true])->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Generated content for {$services->count()} services.");

        return self::SUCCESS;
    }

    private function buildPrompt(Service $service): string
    {
        // Related services (top 10 random of same parent)
        $related = Service::query()
            ->where('parent_slug', $service->parent_slug)
            ->where('id', '!=', $service->id)
            ->inRandomOrder()
            ->limit(10)
            ->pluck('title')
            ->toArray();

        $relatedList = implode("\n - ", $related);

        return <<<'PROMPT'
شما یک نویسنده سئو حرفه‌ای در سال 2025 هستید. لطفاً محتوایی حداقل ۲۰۰۰ کلمه‌ای (حدود ۱۶٬۰۰۰ توکن) برای سرویس زیر تولید کنید. ساختار باید دقیقاً مشابه صفحه «استعلام رتبه بندی و اعتبارسنجی بانکی» در پیشخوانک باشد و شامل:

1. توضیح جامع درباره سرویس و کاربردهای آن با تگ‌های HTML مناسب (h1-h3, p, strong, em, ul, ol).
2. حداقل ۱۰ لینک داخلی به سرویس‌های مرتبط پیشخوانک در متن.
3. ۱۰ سوال متداول (FAQ) با پاسخ کامل.
4. خلاصه ۲۰۰ کلمه‌ای ابتدای متن داخل تگ summary.
5. اسکیما FAQPage و Article به صورت JSON-LD.
6. ذکر برند «پیشخوانک» و مزایای استفاده از وب‌سایت.
7. قیمت دقیق سرویس (از دیتابیس) به تومان.
8. رعایت بهترین شیوه‌های سئو 2025.

سرویس: %%SERVICE_TITLE%%
اسلاگ والد: %%PARENT_SLUG%%
قیمت: %%PRICE%% تومان

سرویس‌های مرتبط:
 - %%RELATED%%
PROMPT;
    }
} 