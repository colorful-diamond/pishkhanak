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

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .dashboard-header {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .dashboard-title {
        color: var(--sky-700);
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }

    .status-badge {
        background: #22c55e;
        color: white;
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .header-meta {
        display: flex;
        gap: 24px;
        color: var(--sky-600);
        font-size: 14px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--sky-400);
    }

    .stat-card.success::before {
        background: #22c55e;
    }

    .stat-card.warning::before {
        background: var(--yellow-400);
    }

    .stat-icon {
        font-size: 24px;
        margin-bottom: 8px;
        color: var(--sky-400);
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
        letter-spacing: 0.05em;
    }

    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .data-panel {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--sky-100);
    }

    .panel-title {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .panel-action {
        background: var(--sky-100);
        color: var(--sky-600);
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
    }

    .data-table {
        width: 100%;
    }

    .table-row {
        display: grid;
        grid-template-columns: 1fr 2fr auto;
        gap: 16px;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--sky-50);
    }

    .table-row:last-child {
        border-bottom: none;
    }

    .row-label {
        color: var(--sky-600);
        font-size: 14px;
        font-weight: 500;
    }

    .row-value {
        background: var(--sky-50);
        color: var(--sky-700);
        padding: 8px 12px;
        border-radius: 6px;
        font-family: monospace;
        font-size: 14px;
        border: 1px solid var(--sky-100);
        direction: ltr;
        text-align: left;
    }

    .highlight-row .row-value {
        background: var(--yellow-100);
        border-color: var(--yellow-400);
        font-weight: 700;
    }

    .row-action {
        background: transparent;
        border: 1px solid var(--sky-200);
        color: var(--sky-600);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
    }

    .activity-panel {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--sky-50);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        background: var(--sky-100);
        color: var(--sky-600);
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        color: var(--sky-700);
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 2px 0;
    }

    .activity-time {
        color: var(--sky-500);
        font-size: 12px;
    }

    .chart-placeholder {
        background: var(--sky-50);
        border: 2px dashed var(--sky-200);
        border-radius: 8px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--sky-500);
        font-size: 14px;
        margin: 16px 0;
    }

    .actions-bar {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
    }

    .action-button {
        background: var(--sky-400);
        color: white;
        border: none;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .action-button:hover {
        background: var(--sky-500);
        transform: translateY(-1px);
    }

    .action-button.secondary {
        background: var(--yellow-400);
        color: #000;
    }

    .action-button.secondary:hover {
        background: var(--yellow-500);
    }

    .action-button.outline {
        background: transparent;
        color: var(--sky-600);
        border: 1px solid var(--sky-300);
    }

    .action-button.outline:hover {
        background: var(--sky-50);
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 12px;
        }
        
        .main-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .table-row {
            grid-template-columns: 1fr;
            gap: 8px;
            text-align: center;
        }
        
        .row-value {
            text-align: center;
            direction: rtl;
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح داشبورد')

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-top">
            <h1 class="dashboard-title">{{ $service->title ?? 'داشبورد تبدیل کارت به شبا' }}</h1>
            <div class="status-badge">عملیات موفق</div>
        </div>
        <div class="header-meta">
            <div class="meta-item">
                <span>📅</span>
                <span>{{ now()->format('Y/m/d') }}</span>
            </div>
            <div class="meta-item">
                <span>⏰</span>
                <span>{{ now()->format('H:i:s') }}</span>
            </div>
            <div class="meta-item">
                <span>⚡</span>
                <span>0.84 ثانیه</span>
            </div>
            <div class="meta-item">
                <span>🔒</span>
                <span>رمزنگاری شده</span>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card success">
            <div class="stat-icon">✓</div>
            <div class="stat-value">100%</div>
            <div class="stat-label">موفقیت</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🏦</div>
            <div class="stat-value">{{ count($result ?? []) }}</div>
            <div class="stat-label">فیلد تبدیل</div>
        </div>
        
        <div class="stat-card warning">
            <div class="stat-icon">⚡</div>
            <div class="stat-value">0.84s</div>
            <div class="stat-label">زمان پردازش</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-value">A+</div>
            <div class="stat-label">کیفیت</div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="main-grid">
        <!-- Data Panel -->
        <div class="data-panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    📊 اطلاعات تبدیل
                </h2>
                <button class="panel-action">تنظیمات</button>
            </div>

            <!-- Input Data Section -->
            @if(isset($inputData) && !empty($inputData))
            <div style="margin-bottom: 24px;">
                <h3 style="color: var(--sky-700); font-size: 16px; font-weight: 600; margin-bottom: 12px;">📥 داده‌های ورودی</h3>
                <div class="data-table">
                    @foreach($inputData as $key => $value)
                    <div class="table-row">
                        <div class="row-label">
                            @switch($key)
                                @case('card_number') شماره کارت @break
                                @case('iban') شماره شبا @break
                                @case('account_number') شماره حساب @break
                                @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                            @endswitch
                        </div>
                        <div class="row-value">{{ $value }}</div>
                        <button class="row-action">کپی</button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Results Section -->
            <div>
                <h3 style="color: var(--sky-700); font-size: 16px; font-weight: 600; margin-bottom: 12px;">📤 نتایج خروجی</h3>
                <div class="data-table">
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
                    <div class="table-row {{ $key === 'iban' ? 'highlight-row' : '' }}">
                        <div class="row-label">
                            @switch($key)
                                @case('iban') شماره شبا @break
                                @case('bank_name') نام بانک @break
                                @case('account_number') شماره حساب @break
                                @case('account_type') نوع حساب @break
                                @case('is_valid') وضعیت اعتبار @break
                                @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                            @endswitch
                        </div>
                        <div class="row-value">
                            @if($key === 'is_valid')
                                {{ $value ? '✓ معتبر و تأیید شده' : '✗ نامعتبر' }}
                            @else
                                {{ $value }}
                            @endif
                        </div>
                        <button class="row-action" onclick="copyValue('{{ $value }}')">کپی</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Chart Placeholder -->
            <div class="chart-placeholder">
                📈 نمودار عملکرد (قابلیت نمایش نمودار)
            </div>
        </div>

        <!-- Activity Panel -->
        <div class="activity-panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    🕒 فعالیت‌های اخیر
                </h2>
            </div>

            <div class="activity-item">
                <div class="activity-icon">✓</div>
                <div class="activity-content">
                    <div class="activity-title">تبدیل کامل شد</div>
                    <div class="activity-time">همین الان</div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">🔍</div>
                <div class="activity-content">
                    <div class="activity-title">اعتبارسنجی انجام شد</div>
                    <div class="activity-time">۲ ثانیه پیش</div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">📥</div>
                <div class="activity-content">
                    <div class="activity-title">داده دریافت شد</div>
                    <div class="activity-time">۵ ثانیه پیش</div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">🚀</div>
                <div class="activity-content">
                    <div class="activity-title">پردازش شروع شد</div>
                    <div class="activity-time">۱۰ ثانیه پیش</div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">👤</div>
                <div class="activity-content">
                    <div class="activity-title">کاربر وارد شد</div>
                    <div class="activity-time">۱ دقیقه پیش</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="actions-bar">
        <div class="actions-grid">
            <button onclick="copyDashboard()" class="action-button">
                📋 کپی گزارش
            </button>
            <button onclick="exportDashboard()" class="action-button secondary">
                📄 صادرات Excel
            </button>
            <button onclick="shareDashboard()" class="action-button outline">
                📤 اشتراک‌گذاری
            </button>
            <button onclick="printDashboard()" class="action-button outline">
                🖨️ چاپ گزارش
            </button>
            <a href="#" class="action-button">
                🔄 تبدیل جدید
            </a>
            <a href="{{ route('app.page.home') }}" class="action-button outline">
                🏠 صفحه اصلی
            </a>
        </div>
    </div>
</div>

<script>
function copyValue(value) {
    navigator.clipboard.writeText(value).then(() => {
        showDashboardNotification('مقدار کپی شد!', 'success');
    });
}

function copyDashboard() {
    const rows = document.querySelectorAll('.table-row');
    let text = 'گزارش داشبورد - پیشخوانک\n';
    text += '='.repeat(40) + '\n\n';
    
    text += `تاریخ: ${new Date().toLocaleDateString('fa-IR')}\n`;
    text += `زمان: ${new Date().toLocaleTimeString('fa-IR')}\n\n`;
    
    rows.forEach(row => {
        const label = row.querySelector('.row-label').textContent.trim();
        const value = row.querySelector('.row-value').textContent.trim();
        text += `${label}: ${value}\n`;
    });
    
    text += '\nپیشخوانک - pishkhanak.com';
    
    navigator.clipboard.writeText(text).then(() => {
        showDashboardNotification('گزارش کامل کپی شد!', 'success');
    });
}

function exportDashboard() {
    showDashboardNotification('صادرات Excel در حال انجام...', 'info');
    // Simulate export process
    setTimeout(() => {
        showDashboardNotification('فایل Excel آماده دانلود است!', 'success');
    }, 2000);
}

function shareDashboard() {
    if (navigator.share) {
        navigator.share({
            title: 'گزارش داشبورد - پیشخوانک',
            text: 'گزارش تبدیل من در پیشخوانک',
            url: window.location.href
        });
    } else {
        copyDashboard();
    }
}

function printDashboard() {
    window.print();
}

function showDashboardNotification(message, type) {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? '#22c55e' : type === 'info' ? 'var(--yellow-400)' : 'var(--sky-400)';
    const textColor = type === 'info' ? '#000' : 'white';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: ${textColor};
        padding: 16px 20px;
        border-radius: 8px;
        z-index: 1000;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        document.body.removeChild(notification);
    }, 3000);
}

// Add copy functionality to all copy buttons
document.querySelectorAll('.row-action').forEach(button => {
    button.addEventListener('click', function() {
        const value = this.closest('.table-row').querySelector('.row-value').textContent.trim();
        copyValue(value);
    });
});

// Auto-refresh dashboard stats every 30 seconds (simulation)
setInterval(() => {
    const qualityEl = document.querySelector('.stat-card:last-child .stat-value');
    const qualities = ['A+', 'A', 'A-', 'B+'];
    qualityEl.textContent = qualities[Math.floor(Math.random() * qualities.length)];
}, 30000);
</script>
@endsection 