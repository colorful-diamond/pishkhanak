<?php

namespace App\Services;

class PersianContentPromptBuilder
{
    /**
     * Build enhanced prompt for section content generation
     */
    public function buildSectionPrompt(
        array $heading,
        string $title,
        string $shortDescription,
        int $sectionNumber,
        array $keywords = [],
        ?array $researchData = null
    ): string {
        $pattern = $this->getPatternForSection($sectionNumber);
        $researchContext = $this->formatResearchData($researchData);
        
        $headingTitle = $heading['title'] ?? $heading['headline'] ?? '';
        
        $prompt = "
🎯 بخش {$sectionNumber} از مقاله: {$headingTitle}

📝 راهنمای تولید محتوا:

✅ الزامات اصلی:
1. ⚠️ قانون بحرانی: در هر بخش ۳-۴ بار از \"پیشخوانک\" یا \"سامانه پیشخوانک\" استفاده کنید (نه بیشتر!)
2. ⚠️ محدودیت دامنه: فقط ۱ بار در کل بخش از pishkhanak.com استفاده کنید
3. محتوا باید کاملاً مرتبط با خدمت \"{$title}\" و بازار ایران در سال ۱۴۰۴ باشد
4. از کلمات کلیدی استفاده کنید: " . implode('، ', $keywords) . "

⛔ قانون مهم برای ذکر برند:
در کل بخش فقط ۳-۴ بار \"پیشخوانک\" ذکر شود (نه در هر پاراگراف!)
محتوا باید طبیعی باشد - اصلاً نباید تکراری به نظر برسد
نسبت مناسب: ۷۵٪ پیشخوانک (برند)، ۲۵٪ pishkhanak.com (دامنه)

📊 اطلاعات تحقیقاتی این سرویس:
{$researchContext}

🎨 الگوی نگارشی برای این بخش: {$pattern['title']}
{$pattern['description']}

🔤 المان‌های HTML مورد نیاز:
- <h2> برای عنوان اصلی بخش
- <h3> برای زیر عناوین
- <p> برای پاراگراف‌های متنی
- <strong> برای تأکید بر کلمات کلیدی
- <em> برای تأکید نرم
- <ul><li> برای فهرست‌ها
- <blockquote> برای نقل قول‌ها

🎯 تمرکز محتوا:
بر اساس تحقیقات انجام شده، این محتوا باید به موارد زیر بپردازد:
{$headingTitle}
" . implode("\n", array_map(fn($sub) => "- " . $sub, $heading['sub_headlines'] ?? [])) . "

لطفاً محتوایی بنویسید که کاربر را به استفاده از پیشخوانک برای این خدمت ترغیب کند.
محتوا باید برای کاربران ایرانی در سال ۱۴۰۴ کاملاً کاربردی باشد.
";
        
        return $prompt;
    }
    
    /**
     * Get writing pattern for specific section number
     */
    private function getPatternForSection(int $sectionNumber): array
    {
        $patterns = [
            1 => [
                'title' => 'سوال مستقیم',
                'description' => 'شروع با یک سوال که خواننده را درگیر کند، سپس پاسخ جامع و راه‌حل از طریق پیشخوانک'
            ],
            2 => [
                'title' => 'سناریوی عملی',
                'description' => 'شروع با مثال کاربردی از زندگی واقعی و سپس توضیح چگونگی حل مشکل'
            ],
            3 => [
                'title' => 'راهنمای گام‌به‌گام',
                'description' => 'ارائه مراحل دقیق و قابل پیگیری برای انجام کار'
            ],
            4 => [
                'title' => 'مقایسه و تحلیل',
                'description' => 'مقایسه روش‌های مختلف و نشان دادن برتری پیشخوانک'
            ],
            5 => [
                'title' => 'تجربه کاربری',
                'description' => 'تمرکز بر تجربه مثبت کاربران و مزایای استفاده از سرویس'
            ],
            6 => [
                'title' => 'راه‌حل جامع',
                'description' => 'ارائه راه‌حل کامل و همه‌جانبه برای نیاز کاربر'
            ],
            7 => [
                'title' => 'نکات تخصصی',
                'description' => 'ارائه نکات حرفه‌ای و تخصصی که کاربر باید بداند'
            ],
            8 => [
                'title' => 'خلاصه و نتیجه‌گیری',
                'description' => 'جمع‌بندی اطلاعات و تشویق کاربر به اقدام'
            ]
        ];
        
        // Cycle through patterns if section number is higher
        $patternIndex = (($sectionNumber - 1) % 8) + 1;
        return $patterns[$patternIndex];
    }
    
    /**
     * Format research data for prompt
     */
    private function formatResearchData(?array $researchData): string
    {
        if (!$researchData) {
            return "⚠️ داده‌های تحقیقاتی در دسترس نیست - از دانش عمومی استفاده کنید";
        }
        
        $formatted = "";
        
        if (!empty($researchData['service_purpose'])) {
            $formatted .= "📌 هدف سرویس: " . $researchData['service_purpose'] . "\n";
        }
        
        if (!empty($researchData['key_features']) && is_array($researchData['key_features'])) {
            $formatted .= "🔧 ویژگی‌های کلیدی:\n";
            foreach ($researchData['key_features'] as $feature) {
                $formatted .= "  - " . $feature . "\n";
            }
        }
        
        if (!empty($researchData['target_audience'])) {
            $formatted .= "👥 مخاطبان هدف: " . $researchData['target_audience'] . "\n";
        }
        
        if (!empty($researchData['benefits']) && is_array($researchData['benefits'])) {
            $formatted .= "✨ مزایا:\n";
            foreach ($researchData['benefits'] as $benefit) {
                $formatted .= "  - " . $benefit . "\n";
            }
        }
        
        return $formatted ?: "⚠️ داده‌های تحقیقاتی کامل نیست";
    }
}