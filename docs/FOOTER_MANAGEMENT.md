# سیستم مدیریت فوتر و لینک‌های داینامیک

## مقدمه

این سیستم امکان مدیریت کامل فوتر، لینک‌های مهم سایت و محتوای فوتر را از طریق پنل مدیریت فراهم می‌کند. تمام داده‌ها در دیتابیس ذخیره شده و با استفاده از کش بهینه‌سازی شده‌اند.

## ویژگی‌ها

- ✅ مدیریت بخش‌های فوتر
- ✅ مدیریت لینک‌های فوتر
- ✅ مدیریت لینک‌های مهم سایت (هدر، نوار کناری، منوی موبایل)
- ✅ مدیریت محتوای فوتر (اطلاعات تماس، قوانین، شبکه‌های اجتماعی)
- ✅ کش خودکار برای بهبود عملکرد
- ✅ رابط کاربری فارسی در پنل مدیریت
- ✅ پشتیبانی از آیکون‌ها
- ✅ ترتیب‌دهی لینک‌ها
- ✅ فعال/غیرفعال کردن آیتم‌ها

## جداول دیتابیس

### 1. footer_sections
بخش‌های مختلف فوتر

| فیلد | نوع | توضیحات |
|------|-----|---------|
| title | string | عنوان بخش |
| slug | string | شناسه یکتا |
| description | text | توضیحات |
| icon | string | آیکون |
| sort_order | integer | ترتیب نمایش |
| is_active | boolean | وضعیت فعال |
| location | string | محل نمایش (footer, sidebar, header) |
| settings | json | تنظیمات اضافی |

### 2. footer_links
لینک‌های هر بخش فوتر

| فیلد | نوع | توضیحات |
|------|-----|---------|
| footer_section_id | integer | شناسه بخش |
| title | string | عنوان لینک |
| url | string | آدرس لینک |
| icon | string | آیکون |
| sort_order | integer | ترتیب نمایش |
| is_active | boolean | وضعیت فعال |
| open_in_new_tab | boolean | باز شدن در تب جدید |
| target | string | نحوه باز شدن |
| attributes | json | ویژگی‌های HTML |

### 3. site_links
لینک‌های مهم سایت

| فیلد | نوع | توضیحات |
|------|-----|---------|
| title | string | عنوان لینک |
| url | string | آدرس لینک |
| location | string | محل نمایش |
| icon | string | آیکون |
| sort_order | integer | ترتیب نمایش |
| is_active | boolean | وضعیت فعال |
| open_in_new_tab | boolean | باز شدن در تب جدید |
| target | string | نحوه باز شدن |
| attributes | json | ویژگی‌های HTML |
| css_class | string | کلاس CSS |

### 4. footer_contents
محتوای فوتر

| فیلد | نوع | توضیحات |
|------|-----|---------|
| key | string | کلید |
| value | text | مقدار |
| type | string | نوع محتوا (text, html, image, json) |
| section | string | بخش (general, contact, social, legal) |
| is_active | boolean | وضعیت فعال |
| settings | json | تنظیمات اضافی |

## نحوه استفاده

### 1. دسترسی به پنل مدیریت

به آدرس `/access` بروید و وارد پنل مدیریت شوید. در منوی سمت راست، بخش "مدیریت فوتر و لینک‌ها" را خواهید دید.

### 2. مدیریت بخش‌های فوتر

- **بخش‌های فوتر**: ایجاد و ویرایش بخش‌های مختلف فوتر
- **لینک‌های فوتر**: مدیریت لینک‌های هر بخش
- **لینک‌های مهم سایت**: مدیریت لینک‌های هدر، نوار کناری و منوی موبایل
- **محتوای فوتر**: مدیریت اطلاعات تماس، قوانین و شبکه‌های اجتماعی

### 3. استفاده در کد

#### دریافت داده‌های فوتر
```php
use App\Services\FooterManagerService;

// دریافت تمام داده‌های فوتر
$footerData = FooterManagerService::getFooterData();

// دریافت بخش‌های فوتر
$sections = FooterManagerService::getFooterSections('footer');

// دریافت لینک‌های سایت
$headerLinks = FooterManagerService::getSiteLinks('header');
$sidebarLinks = FooterManagerService::getSiteLinks('sidebar');
$mobileNavLinks = FooterManagerService::getSiteLinks('mobile_nav');

// دریافت محتوای فوتر
$companyName = FooterManagerService::getFooterContent('company_name');
$phone = FooterManagerService::getFooterContent('phone');
```

#### استفاده در Blade
```blade
@php
    use App\Services\FooterManagerService;
    $footerData = FooterManagerService::getFooterData();
@endphp

<!-- نمایش نام شرکت -->
<h1>{{ $footerData['company_name'] }}</h1>

<!-- نمایش بخش‌های فوتر -->
@foreach($footerData['sections'] as $section)
    <div class="footer-section">
        <h3>{{ $section->title }}</h3>
        @foreach($section->activeLinks as $link)
            <a href="{{ $link->url }}">{{ $link->title }}</a>
        @endforeach
    </div>
@endforeach

<!-- استفاده از کامپوننت -->
<x-dynamic-links location="header" css-class="header-links" />
```

### 4. مدیریت کش

#### پاک کردن کش
```bash
# پاک کردن تمام کش‌ها
php artisan footer:clear-cache

# پاک کردن کش برای محل خاص
php artisan footer:clear-cache --location=header
```

#### کش خودکار
تمام داده‌ها به صورت خودکار کش می‌شوند و در صورت تغییر، کش به‌روزرسانی می‌شود.

## فایل‌های مهم

### Models
- `app/Models/FooterSection.php`
- `app/Models/FooterLink.php`
- `app/Models/SiteLink.php`
- `app/Models/FooterContent.php`

### Services
- `app/Services/FooterManagerService.php`

### Views
- `resources/views/front/partials/footer-dynamic.blade.php`
- `resources/views/components/dynamic-links.blade.php`

### Commands
- `app/Console/Commands/ClearFooterCache.php`

### Seeders
- `database/seeders/FooterDataSeeder.php`

## تست سیستم

برای تست سیستم، به آدرس `/test-footer` بروید. این صفحه اطلاعات فوتر داینامیک را نمایش می‌دهد.

## نکات مهم

1. **کش**: تمام داده‌ها کش می‌شوند تا عملکرد سایت بهبود یابد
2. **ترتیب**: از فیلد `sort_order` برای ترتیب‌دهی استفاده کنید
3. **فعال/غیرفعال**: از فیلد `is_active` برای نمایش/عدم نمایش استفاده کنید
4. **آیکون‌ها**: از Heroicons یا سایر آیکون‌ها استفاده کنید
5. **لینک‌های خارجی**: برای لینک‌های خارجی از `open_in_new_tab` استفاده کنید

## مثال‌های کاربردی

### اضافه کردن بخش جدید
1. به پنل مدیریت بروید
2. بخش "بخش‌های فوتر" را انتخاب کنید
3. روی "ایجاد" کلیک کنید
4. اطلاعات بخش را وارد کنید
5. لینک‌های مربوطه را اضافه کنید

### تغییر اطلاعات تماس
1. بخش "محتوای فوتر" را انتخاب کنید
2. آیتم مورد نظر را ویرایش کنید
3. تغییرات ذخیره می‌شود و کش به‌روزرسانی می‌شود

### اضافه کردن لینک به هدر
1. بخش "لینک‌های مهم سایت" را انتخاب کنید
2. لینک جدید ایجاد کنید
3. محل نمایش را "هدر" انتخاب کنید
4. ترتیب نمایش را تنظیم کنید 