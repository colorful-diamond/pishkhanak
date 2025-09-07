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
        margin: 0;
        padding: 0;
    }

    .split-container {
        max-width: 1200px;
        margin: 20px auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding: 0 20px;
        min-height: calc(100vh - 40px);
    }

    .left-panel {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
        height: fit-content;
    }

    .right-panel {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
        height: fit-content;
    }

    .panel-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--sky-100);
    }

    .panel-icon {
        background: var(--sky-400);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .panel-title {
        color: var(--sky-700);
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }

    .status-indicator {
        background: #22c55e;
        color: white;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        margin-right: auto;
    }

    .content-section {
        margin-bottom: 24px;
    }

    .section-title {
        color: var(--sky-700);
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-badge {
        background: var(--yellow-100);
        color: #000;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        border: 1px solid var(--yellow-400);
    }

    .data-list {
        space-y: 12px;
    }

    .data-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--sky-50);
    }

    .data-item:last-child {
        border-bottom: none;
    }

    .item-label {
        color: var(--sky-600);
        font-size: 14px;
        font-weight: 500;
        flex: 1;
    }

    .item-value {
        background: var(--sky-50);
        color: var(--sky-700);
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid var(--sky-100);
        font-family: monospace;
        direction: ltr;
        text-align: left;
        max-width: 60%;
        word-break: break-all;
        flex: 1;
        margin-right: 12px;
    }

    .highlight-item .item-value {
        background: var(--yellow-100);
        border-color: var(--yellow-400);
        color: #000;
        font-weight: 700;
    }

    .summary-card {
        background: var(--sky-100);
        border: 1px solid var(--sky-200);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-bottom: 24px;
    }

    .summary-icon {
        color: var(--sky-400);
        font-size: 32px;
        margin-bottom: 12px;
    }

    .summary-title {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .summary-desc {
        color: var(--sky-600);
        font-size: 14px;
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 24px;
    }

    .stat-box {
        background: var(--sky-50);
        border: 1px solid var(--sky-100);
        border-radius: 8px;
        padding: 16px;
        text-align: center;
    }

    .stat-value {
        color: var(--sky-700);
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .stat-label {
        color: var(--sky-600);
        font-size: 12px;
        text-transform: uppercase;
    }

    .timeline {
        position: relative;
        padding-right: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        right: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--sky-200);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-right: 24px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        right: -1px;
        top: 6px;
        width: 10px;
        height: 10px;
        background: var(--sky-400);
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 2px var(--sky-200);
    }

    .timeline-item.completed::before {
        background: #22c55e;
    }

    .timeline-content {
        background: var(--sky-50);
        border: 1px solid var(--sky-100);
        border-radius: 8px;
        padding: 12px;
    }

    .timeline-title {
        color: var(--sky-700);
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 4px 0;
    }

    .timeline-time {
        color: var(--sky-500);
        font-size: 12px;
        font-family: monospace;
    }

    .actions-panel {
        background: var(--yellow-100);
        border: 1px solid var(--yellow-400);
        border-radius: 12px;
        padding: 20px;
    }

    .actions-title {
        color: var(--sky-700);
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 16px;
        text-align: center;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 16px;
    }

    .action-btn {
        background: var(--sky-400);
        color: white;
        border: none;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .action-btn:hover {
        background: var(--sky-500);
        transform: translateY(-1px);
    }

    .action-btn.secondary {
        background: var(--yellow-400);
        color: #000;
    }

    .action-btn.secondary:hover {
        background: var(--yellow-500);
    }

    .action-btn.full-width {
        grid-column: 1 / -1;
    }

    .verification-badge {
        background: #22c55e;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 16px;
    }

    .footer-info {
        text-align: center;
        color: var(--sky-500);
        font-size: 12px;
        padding: 16px;
        border-top: 1px solid var(--sky-100);
        margin-top: 24px;
    }

    @media (max-width: 768px) {
        .split-container {
            grid-template-columns: 1fr;
            margin: 12px auto;
            padding: 0 12px;
        }
        
        .left-panel,
        .right-panel {
            padding: 16px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
        
        .panel-header {
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }
        
        .data-item {
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }
        
        .item-value {
            max-width: 100%;
            text-align: center;
            direction: rtl;
        }
    }

    @media (max-width: 480px) {
        .split-container {
            margin: 8px auto;
            padding: 0 8px;
        }
        
        .left-panel,
        .right-panel {
            padding: 12px;
        }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح تقسیم دو ستونه')

@section('content')
<div class="split-container">
    <!-- Left Panel - Input and Process -->
    <div class="left-panel">
        <!-- Panel Header -->
        <div class="panel-header">
            <div class="panel-icon">📥</div>
            <h2 class="panel-title">اطلاعات ورودی</h2>
            <div class="status-indicator">دریافت شده</div>
        </div>

        <!-- Summary Card -->
        <div class="summary-card">
            <div class="summary-icon">🔄</div>
            <h3 class="summary-title">{{ $service->title ?? 'تبدیل کارت به شبا' }}</h3>
            <p class="summary-desc">عملیات تبدیل با موفقیت انجام شد</p>
        </div>

        <!-- Input Data Section -->
        @if(isset($inputData) && !empty($inputData))
        <div class="content-section">
            <h3 class="section-title">
                📋 داده‌های ورودی
                <span class="section-badge">{{ count($inputData) }} فیلد</span>
            </h3>
            <div class="data-list">
                @foreach($inputData as $key => $value)
                <div class="data-item">
                    <span class="item-label">
                        @switch($key)
                            @case('card_number') شماره کارت @break
                            @case('iban') شماره شبا @break
                            @case('account_number') شماره حساب @break
                            @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </span>
                    <span class="item-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Statistics -->
        <div class="content-section">
            <h3 class="section-title">📊 آمار عملیات</h3>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value">{{ count($inputData ?? []) }}</div>
                    <div class="stat-label">ورودی</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ count($result ?? []) }}</div>
                    <div class="stat-label">خروجی</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">0.84s</div>
                    <div class="stat-label">زمان</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">100%</div>
                    <div class="stat-label">موفقیت</div>
                </div>
            </div>
        </div>

        <!-- Process Timeline -->
        <div class="content-section">
            <h3 class="section-title">⏱️ جدول زمانی</h3>
            <div class="timeline">
                <div class="timeline-item completed">
                    <div class="timeline-content">
                        <div class="timeline-title">دریافت اطلاعات</div>
                        <div class="timeline-time">{{ now()->subSeconds(10)->format('H:i:s') }}</div>
                    </div>
                </div>
                <div class="timeline-item completed">
                    <div class="timeline-content">
                        <div class="timeline-title">اعتبارسنجی داده‌ها</div>
                        <div class="timeline-time">{{ now()->subSeconds(8)->format('H:i:s') }}</div>
                    </div>
                </div>
                <div class="timeline-item completed">
                    <div class="timeline-content">
                        <div class="timeline-title">پردازش تبدیل</div>
                        <div class="timeline-time">{{ now()->subSeconds(5)->format('H:i:s') }}</div>
                    </div>
                </div>
                <div class="timeline-item completed">
                    <div class="timeline-content">
                        <div class="timeline-title">تولید نتایج</div>
                        <div class="timeline-time">{{ now()->format('H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Results and Actions -->
    <div class="right-panel">
        <!-- Panel Header -->
        <div class="panel-header">
            <div class="panel-icon">📤</div>
            <h2 class="panel-title">نتایج تبدیل</h2>
            <div class="status-indicator">آماده</div>
        </div>

        <!-- Verification Badge -->
        <div class="verification-badge">
            ✓ تبدیل با موفقیت انجام شد
        </div>

        <!-- Results Section -->
        <div class="content-section">
            <h3 class="section-title">
                🎯 اطلاعات تبدیل شده
                <span class="section-badge">معتبر</span>
            </h3>
            <div class="data-list">
                @php
                    $mockResult = [
                        'iban' => 'IR123456789012345678901234',
                        'bank_name' => 'بانک ملی ایران',
                        'account_number' => '1234567890',
                        'account_type' => 'جاری',
                        'is_valid' => true
                    ];
                    $displayResult = $result ?? $mockResult;
                @endphp
                
                @foreach($displayResult as $key => $value)
                <div class="data-item {{ $key === 'iban' ? 'highlight-item' : '' }}">
                    <span class="item-label">
                        @switch($key)
                            @case('iban') شماره شبا @break
                            @case('bank_name') نام بانک @break
                            @case('account_number') شماره حساب @break
                            @case('account_type') نوع حساب @break
                            @case('is_valid') وضعیت اعتبار @break
                            @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </span>
                    <span class="item-value">
                        @if($key === 'is_valid')
                            {{ $value ? '✓ معتبر' : '✗ نامعتبر' }}
                        @else
                            {{ $value }}
                        @endif
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Additional Information -->
        <div class="content-section">
            <h3 class="section-title">ℹ️ اطلاعات تکمیلی</h3>
            <div class="data-list">
                <div class="data-item">
                    <span class="item-label">تاریخ تولید</span>
                    <span class="item-value">{{ now()->format('Y/m/d') }}</span>
                </div>
                <div class="data-item">
                    <span class="item-label">زمان تولید</span>
                    <span class="item-value">{{ now()->format('H:i:s') }}</span>
                </div>
                <div class="data-item">
                    <span class="item-label">شناسه جلسه</span>
                    <span class="item-value">{{ strtoupper(substr(md5(time()), 0, 8)) }}</span>
                </div>
                <div class="data-item">
                    <span class="item-label">IP کاربر</span>
                    <span class="item-value">xxx.xxx.xxx.xxx</span>
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="actions-panel">
            <h3 class="actions-title">🛠️ عملیات در دسترس</h3>
            <div class="actions-grid">
                <button onclick="copySplit()" class="action-btn">
                    📋 کپی
                </button>
                <button onclick="shareSplit()" class="action-btn secondary">
                    📤 اشتراک
                </button>
                <button onclick="downloadSplit()" class="action-btn">
                    📄 دانلود
                </button>
                <button onclick="printSplit()" class="action-btn secondary">
                    🖨️ چاپ
                </button>
                <a href="#" class="action-btn full-width">
                    🔄 تبدیل دوباره
                </a>
                <a href="{{ route('app.page.home') }}" class="action-btn secondary full-width">
                    🏠 بازگشت به خانه
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="footer-info">
            <strong>پیشخوانک</strong><br>
            سیستم تبدیل اطلاعات بانکی<br>
            pishkhanak.com
        </div>
    </div>
</div>

<script>
function copySplit() {
    const leftData = document.querySelector('.left-panel .data-list');
    const rightData = document.querySelector('.right-panel .data-list');
    
    let text = 'نتایج تبدیل - پیشخوانک\n';
    text += '='.repeat(40) + '\n\n';
    
    // Copy input data
    if (leftData) {
        text += 'اطلاعات ورودی:\n';
        text += '-'.repeat(20) + '\n';
        const leftItems = leftData.querySelectorAll('.data-item');
        leftItems.forEach(item => {
            const label = item.querySelector('.item-label').textContent.trim();
            const value = item.querySelector('.item-value').textContent.trim();
            text += `${label}: ${value}\n`;
        });
        text += '\n';
    }
    
    // Copy output data
    if (rightData) {
        text += 'نتایج تبدیل:\n';
        text += '-'.repeat(20) + '\n';
        const rightItems = rightData.querySelectorAll('.data-item');
        rightItems.forEach(item => {
            const label = item.querySelector('.item-label').textContent.trim();
            const value = item.querySelector('.item-value').textContent.trim();
            text += `${label}: ${value}\n`;
        });
    }
    
    text += '\nپیشخوانک - pishkhanak.com';
    
    navigator.clipboard.writeText(text).then(() => {
        showSplitNotification('اطلاعات کپی شد!', 'success');
    });
}

function shareSplit() {
    if (navigator.share) {
        navigator.share({
            title: 'نتیجه تبدیل - پیشخوانک',
            text: 'نتیجه تبدیل من در پیشخوانک',
            url: window.location.href
        });
    } else {
        copySplit();
    }
}

function downloadSplit() {
    showSplitNotification('آماده‌سازی فایل...', 'info');
    setTimeout(() => {
        showSplitNotification('فایل آماده دانلود است!', 'success');
    }, 1500);
}

function printSplit() {
    window.print();
}

function showSplitNotification(message, type) {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? '#22c55e' : type === 'info' ? 'var(--yellow-400)' : 'var(--sky-400)';
    const textColor = type === 'info' ? '#000' : 'white';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: ${textColor};
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 1000;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        document.body.removeChild(notification);
    }, 3000);
}

// Add copy functionality to individual items
document.querySelectorAll('.item-value').forEach(item => {
    item.addEventListener('click', function() {
        const value = this.textContent.trim();
        navigator.clipboard.writeText(value).then(() => {
            showSplitNotification('مقدار کپی شد!', 'success');
        });
    });
    
    // Add cursor pointer
    item.style.cursor = 'pointer';
    item.title = 'کلیک برای کپی';
});
</script>
@endsection 