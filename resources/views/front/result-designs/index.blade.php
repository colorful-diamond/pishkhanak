@extends('front.layouts.app')

@push('styles')
<style>
    :root {
        --sky-50: #f0f9ff;
        --sky-100: #e0f2fe;
        --sky-400: #38bdf8;
        --sky-500: #0ea5e9;
        --sky-600: #0284c7;
        --sky-700: #0369a1;
        --yellow-100: #fef3c7;
        --yellow-400: #fbbf24;
        --yellow-500: #f59e0b;
    }

    body {
        background: var(--sky-50);
        font-family: 'IRANSansWebFaNum', system-ui, sans-serif;
    }

    .designs-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .page-title {
        color: var(--sky-700);
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 16px 0;
    }

    .page-subtitle {
        color: var(--sky-600);
        font-size: 18px;
        margin: 0 0 24px 0;
    }

    .designs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .design-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .design-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(56, 189, 248, 0.15);
        border-color: var(--yellow-400);
    }

    .design-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .design-number {
        background: var(--sky-400);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
    }

    .design-title {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    .design-description {
        color: var(--sky-600);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .design-features {
        margin-bottom: 20px;
    }

    .feature-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .feature-tag {
        background: var(--yellow-100);
        color: #000;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid var(--yellow-400);
    }

    .design-actions {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: center;
    }

    .view-button {
        background: var(--sky-400);
        color: white;
        text-decoration: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .view-button:hover {
        background: var(--sky-500);
        transform: scale(1.05);
    }

    .mobile-indicator {
        color: var(--yellow-500);
        font-size: 20px;
        cursor: help;
        title: "بهینه‌سازی شده برای موبایل";
    }

    .desktop-indicator {
        color: var(--sky-500);
        font-size: 20px;
        cursor: help;
        title: "بهینه‌سازی شده برای دسکتاپ";
    }

    .responsive-indicator {
        color: #22c55e;
        font-size: 20px;
        cursor: help;
        title: "ریسپانسیو - مناسب همه صفحات";
    }

    .footer-note {
        background: white;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
    }

    .footer-text {
        color: var(--sky-600);
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .home-button {
        background: var(--yellow-400);
        color: #000;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
        transition: all 0.2s ease;
    }

    .home-button:hover {
        background: var(--yellow-500);
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .designs-container {
            padding: 0 12px;
            margin: 20px auto;
        }
        
        .designs-grid {
            grid-template-columns: 1fr;
        }
        
        .page-title {
            font-size: 24px;
        }
        
        .page-subtitle {
            font-size: 16px;
        }
        
        .design-actions {
            grid-template-columns: 1fr;
            text-align: center;
        }
    }

    /* Special styling for recommended designs */
    .design-card.recommended {
        border: 2px solid var(--yellow-400);
        background: linear-gradient(135deg, #ffffff 0%, var(--yellow-100) 100%);
    }

    .recommended-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--yellow-400);
        color: #000;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>
@endpush

@section('title', 'طرح‌های نتیجه - انتخاب طراحی')

@section('content')
<div class="designs-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">🎨 10 طرح مختلف صفحه نتیجه</h1>
        <p class="page-subtitle">
            طرح‌های مینیمال و حرفه‌ای با رنگ‌های آسمانی و زرد برای انتخاب شما
        </p>
    </div>

    <!-- Designs Grid -->
    <div class="designs-grid">
        <!-- Design 1 -->
        <div class="design-card">
            <div class="design-header">
                <div class="design-number">1</div>
                <h3 class="design-title">کارت‌های تمیز</h3>
            </div>
            <p class="design-description">
                طراحی کارت‌های ساده و تمیز با لایه‌بندی مناسب و فضای کافی بین عناصر
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">ساده</span>
                    <span class="feature-tag">تمیز</span>
                    <span class="feature-tag">کارت‌ها</span>
                    <span class="feature-tag">مینیمال</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.1') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="responsive-indicator">📱💻</span>
            </div>
        </div>

        <!-- Design 2 -->
        <div class="design-card">
            <div class="design-header">
                <div class="design-number">2</div>
                <h3 class="design-title">اپلیکیشن موبایل</h3>
            </div>
            <p class="design-description">
                طراحی مخصوص موبایل با ظاهر اپلیکیشن و المان‌های شناور
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">موبایل</span>
                    <span class="feature-tag">اپ-لایک</span>
                    <span class="feature-tag">شناور</span>
                    <span class="feature-tag">مدرن</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.2') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="mobile-indicator">📱</span>
            </div>
        </div>

        <!-- Design 3 -->
        <div class="design-card recommended">
            <div class="recommended-badge">پیشنهادی</div>
            <div class="design-header">
                <div class="design-number">3</div>
                <h3 class="design-title">داشبورد</h3>
            </div>
            <p class="design-description">
                طراحی داشبوردی با متریک‌ها و کارت‌های مدرن، مناسب برای نمایش اطلاعات کاربری
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">داشبورد</span>
                    <span class="feature-tag">متریک</span>
                    <span class="feature-tag">کاربری</span>
                    <span class="feature-tag">حرفه‌ای</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.3') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="responsive-indicator">📱💻</span>
            </div>
        </div>

        <!-- Design 4 -->
        <div class="design-card">
            <div class="design-header">
                <div class="design-number">4</div>
                <h3 class="design-title">سند/رسید</h3>
            </div>
            <p class="design-description">
                طراحی مثل سند رسمی با ظاهر حرفه‌ای و قابل چاپ
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">سند</span>
                    <span class="feature-tag">چاپی</span>
                    <span class="feature-tag">رسمی</span>
                    <span class="feature-tag">حرفه‌ای</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.4') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="desktop-indicator">💻</span>
            </div>
        </div>

        <!-- Design 5 -->
        <div class="design-card">
            <div class="design-header">
                <div class="design-number">5</div>
                <h3 class="design-title">تقسیم دو ستونه</h3>
            </div>
            <p class="design-description">
                طراحی تقسیم‌شده با دو ستون برای نمایش بهتر اطلاعات در صفحات بزرگ
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">دو ستونه</span>
                    <span class="feature-tag">تقسیم</span>
                    <span class="feature-tag">ریسپانسیو</span>
                    <span class="feature-tag">منظم</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.5') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="responsive-indicator">📱💻</span>
            </div>
        </div>

        <!-- Design 6 -->
        <div class="design-card">
            <div class="design-header">
                <div class="design-number">6</div>
                <h3 class="design-title">فهرست فشرده</h3>
            </div>
            <p class="design-description">
                طراحی فشرده و کارآمد که از فضا به بهترین نحو استفاده می‌کند
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">فشرده</span>
                    <span class="feature-tag">کارآمد</span>
                    <span class="feature-tag">کم‌جا</span>
                    <span class="feature-tag">سریع</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.6') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="responsive-indicator">📱💻</span>
            </div>
        </div>

        <!-- Design 7 -->
        <div class="design-card recommended">
            <div class="recommended-badge">محبوب</div>
            <div class="design-header">
                <div class="design-number">7</div>
                <h3 class="design-title">مینیمال مدرن</h3>
            </div>
            <p class="design-description">
                فوق‌العاده مینیمال با فضای سفید زیاد و طراحی بسیار تمیز و شیک
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">فوق‌مینیمال</span>
                    <span class="feature-tag">فضای سفید</span>
                    <span class="feature-tag">شیک</span>
                    <span class="feature-tag">مدرن</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.7') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="responsive-indicator">📱💻</span>
            </div>
        </div>

        <!-- Design 8 -->
        <div class="design-card">
            <div class="design-header">
                <div class="design-number">8</div>
                <h3 class="design-title">تابلو وضعیت</h3>
            </div>
            <p class="design-description">
                طراحی مانیتورینگ با نشانگرهای وضعیت و المان‌های بصری واضح
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">مانیتورینگ</span>
                    <span class="feature-tag">وضعیت</span>
                    <span class="feature-tag">نشانگر</span>
                    <span class="feature-tag">تکنیکال</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.8') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="responsive-indicator">📱💻</span>
            </div>
        </div>

        <!-- Design 9 -->
        <div class="design-card">
            <div class="design-header">
                <div class="design-number">9</div>
                <h3 class="design-title">رسید حرفه‌ای</h3>
            </div>
            <p class="design-description">
                طراحی مثل رسید بانکی با بارکد، QR کد و قابلیت چاپ
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">رسید</span>
                    <span class="feature-tag">بارکد</span>
                    <span class="feature-tag">چاپی</span>
                    <span class="feature-tag">بانکی</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.9') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="mobile-indicator">📱</span>
            </div>
        </div>

        <!-- Design 10 -->
        <div class="design-card recommended">
            <div class="recommended-badge">جذاب</div>
            <div class="design-header">
                <div class="design-number">10</div>
                <h3 class="design-title">تعاملی</h3>
            </div>
            <p class="design-description">
                طراحی پر از انیمیشن و افکت‌های تعاملی برای تجربه کاربری جذاب
            </p>
            <div class="design-features">
                <div class="feature-list">
                    <span class="feature-tag">انیمیشن</span>
                    <span class="feature-tag">تعاملی</span>
                    <span class="feature-tag">جذاب</span>
                    <span class="feature-tag">مدرن</span>
                </div>
            </div>
            <div class="design-actions">
                <a href="{{ route('result.designs.10') }}" class="view-button" target="_blank">
                    👁️ مشاهده طرح
                </a>
                <span class="responsive-indicator">📱💻</span>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="footer-note">
        <p class="footer-text">
            <strong>🎯 راهنمای انتخاب:</strong><br>
            همه طرح‌ها با رنگ‌های آسمانی و زرد شما طراحی شده‌اند و بدون گرادیانت هستند.<br>
            طرح‌های «پیشنهادی» برای استفاده عمومی و «محبوب» برای طراحی مدرن مناسب هستند.<br>
            بعد از انتخاب، طرح مورد نظرتان را اعلام کنید تا روی صفحه اصلی اعمال شود.
        </p>
        <a href="{{ route('app.page.home') }}" class="home-button">
            🏠 بازگشت به خانه
        </a>
    </div>
</div>
@endsection 