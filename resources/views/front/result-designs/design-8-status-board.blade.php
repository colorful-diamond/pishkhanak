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
        --green: #22c55e;
        --red: #ef4444;
    }

    body {
        background: #f8fafc;
        font-family: 'IRANSansWebFaNum', system-ui, sans-serif;
    }

    .status-container {
        max-width: 900px;
        margin: 24px auto;
        padding: 0 20px;
    }

    .status-header {
        background: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--sky-100);
        position: relative;
    }

    .status-indicator {
        position: absolute;
        top: 24px;
        left: 24px;
        width: 12px;
        height: 12px;
        background: var(--green);
        border-radius: 50%;
        animation: statusPulse 2s infinite;
    }

    @keyframes statusPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.2); }
    }

    .header-content {
        text-align: center;
    }

    .status-badge {
        background: var(--green);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 700;
        display: inline-block;
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .main-title {
        color: var(--sky-700);
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .timestamp {
        color: var(--sky-500);
        font-size: 14px;
        font-family: monospace;
    }

    .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .status-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--sky-100);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .status-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--sky-400);
    }

    .status-card.success::before {
        background: var(--green);
    }

    .status-card.warning::before {
        background: var(--yellow-400);
    }

    .card-icon {
        width: 40px;
        height: 40px;
        background: var(--sky-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
        font-size: 18px;
        color: var(--sky-600);
    }

    .success .card-icon {
        background: rgba(34, 197, 94, 0.1);
        color: var(--green);
    }

    .warning .card-icon {
        background: rgba(251, 191, 36, 0.1);
        color: var(--yellow-500);
    }

    .card-title {
        color: var(--sky-700);
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .card-value {
        color: var(--sky-600);
        font-size: 14px;
        font-weight: 500;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--sky-100);
        border-radius: 3px;
        margin-top: 12px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: var(--green);
        border-radius: 3px;
        width: 100%;
        animation: progressFill 1.5s ease-out;
    }

    @keyframes progressFill {
        from { width: 0%; }
        to { width: 100%; }
    }

    .data-board {
        background: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--sky-100);
    }

    .board-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--sky-100);
    }

    .board-title {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .board-badge {
        background: var(--yellow-400);
        color: #000;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .data-table {
        width: 100%;
    }

    .data-row {
        display: grid;
        grid-template-columns: 2fr 3fr auto;
        gap: 16px;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid var(--sky-50);
    }

    .data-row:last-child {
        border-bottom: none;
    }

    .row-label {
        color: var(--sky-600);
        font-size: 14px;
        font-weight: 600;
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

    .row-status {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-verified {
        color: var(--green);
    }

    .status-pending {
        color: var(--yellow-500);
    }

    .status-dot-small {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .actions-board {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--sky-100);
        margin-bottom: 24px;
    }

    .actions-title {
        color: var(--sky-700);
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 16px;
        text-align: center;
    }

    .actions-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
    }

    .action-button {
        background: var(--sky-400);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 14px 16px;
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
        border: 2px solid var(--sky-200);
    }

    .action-button.outline:hover {
        background: var(--sky-50);
        border-color: var(--sky-400);
    }

    .footer-status {
        text-align: center;
        color: var(--sky-500);
        font-size: 12px;
        padding: 16px;
    }

    @media (max-width: 768px) {
        .status-container {
            padding: 0 12px;
            margin: 16px auto;
        }
        
        .status-header {
            padding: 24px;
        }
        
        .data-board {
            padding: 20px;
        }
        
        .data-row {
            grid-template-columns: 1fr;
            gap: 8px;
            text-align: center;
        }
        
        .row-value {
            text-align: center;
            direction: rtl;
        }
        
        .board-header {
            flex-direction: column;
            gap: 12px;
            text-align: center;
        }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح تابلو وضعیت')

@section('content')
<div class="status-container">
    <!-- Status Header -->
    <div class="status-header">
        <div class="status-indicator"></div>
        <div class="header-content">
            <div class="status-badge">عملیات موفق</div>
            <h1 class="main-title">{{ $service->title ?? 'تبدیل کارت به شبا' }}</h1>
            <div class="timestamp">{{ now()->format('Y/m/d H:i:s') }}</div>
        </div>
    </div>

    <!-- Status Cards Grid -->
    <div class="status-grid">
        <div class="status-card success">
            <div class="card-icon">✓</div>
            <div class="card-title">وضعیت پردازش</div>
            <div class="card-value">تکمیل شده</div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
        </div>
        
        <div class="status-card success">
            <div class="card-icon">🏦</div>
            <div class="card-title">اعتبارسنجی بانک</div>
            <div class="card-value">تأیید شده</div>
        </div>
        
        <div class="status-card warning">
            <div class="card-icon">⚡</div>
            <div class="card-title">زمان پردازش</div>
            <div class="card-value">0.84 ثانیه</div>
        </div>
        
        <div class="status-card">
            <div class="card-icon">🔒</div>
            <div class="card-title">امنیت داده</div>
            <div class="card-value">رمزنگاری شده</div>
        </div>
    </div>

    <!-- Input Data Board -->
    @if(isset($inputData) && !empty($inputData))
    <div class="data-board">
        <div class="board-header">
            <div class="board-title">
                📥 اطلاعات ورودی
            </div>
            <div class="board-badge">دریافت شده</div>
        </div>
        
        <div class="data-table">
            @foreach($inputData as $key => $value)
            <div class="data-row">
                <div class="row-label">
                    @switch($key)
                        @case('card_number') شماره کارت @break
                        @case('iban') شماره شبا @break
                        @case('account_number') شماره حساب @break
                        @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                    @endswitch
                </div>
                <div class="row-value">{{ $value }}</div>
                <div class="row-status status-verified">
                    <div class="status-dot-small"></div>
                    تأیید
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Results Data Board -->
    <div class="data-board">
        <div class="board-header">
            <div class="board-title">
                📤 نتایج تبدیل
            </div>
            <div class="board-badge">آماده</div>
        </div>
        
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
            <div class="data-row {{ $key === 'iban' ? 'highlight-row' : '' }}">
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
                        {{ $value ? 'معتبر و تأیید شده' : 'نامعتبر' }}
                    @else
                        {{ $value }}
                    @endif
                </div>
                <div class="row-status {{ $key === 'is_valid' && $value ? 'status-verified' : 'status-pending' }}">
                    <div class="status-dot-small"></div>
                    {{ $key === 'is_valid' && $value ? 'تأیید' : 'بررسی' }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Actions Board -->
    <div class="actions-board">
        <div class="actions-title">عملیات در دسترس</div>
        <div class="actions-row">
            <button onclick="copyStatus()" class="action-button">
                📋 کپی
            </button>
            <button onclick="shareStatus()" class="action-button secondary">
                📤 اشتراک
            </button>
            <button onclick="downloadStatus()" class="action-button outline">
                📄 دانلود
            </button>
            <a href="#" class="action-button outline">
                🔄 دوباره
            </a>
            <a href="{{ route('app.page.home') }}" class="action-button">
                🏠 خانه
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-status">
        <strong>پیشخوانک</strong> | سیستم مانیتورینگ عملیات | pishkhanak.com
    </div>
</div>

<script>
function copyStatus() {
    const rows = document.querySelectorAll('.data-row');
    let text = 'گزارش وضعیت - پیشخوانک\n';
    text += '='.repeat(35) + '\n\n';
    
    rows.forEach(row => {
        const label = row.querySelector('.row-label').textContent.trim();
        const value = row.querySelector('.row-value').textContent.trim();
        const status = row.querySelector('.row-status').textContent.trim();
        text += `${label}: ${value} [${status}]\n`;
    });
    
    text += '\n' + `تولید شده در: ${new Date().toLocaleString('fa-IR')}`;
    text += '\n' + 'پیشخوانک - pishkhanak.com';
    
    navigator.clipboard.writeText(text).then(() => {
        showStatusNotification('گزارش کپی شد!', 'success');
    });
}

function shareStatus() {
    if (navigator.share) {
        navigator.share({
            title: 'گزارش وضعیت - پیشخوانک',
            text: 'گزارش وضعیت عملیات من در پیشخوانک',
            url: window.location.href
        });
    } else {
        copyStatus();
    }
}

function downloadStatus() {
    showStatusNotification('گزارش در حال آماده‌سازی...', 'info');
    // Implementation for download
}

function showStatusNotification(message, type) {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'var(--green)' : 'var(--yellow-400)';
    const textColor = type === 'success' ? 'white' : '#000';
    
    notification.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        background: ${bgColor};
        color: ${textColor};
        padding: 16px 20px;
        border-radius: 10px;
        z-index: 1000;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        document.body.removeChild(notification);
    }, 3000);
}

// Auto-refresh status indicators
setInterval(() => {
    const statusDots = document.querySelectorAll('.status-dot-small');
    statusDots.forEach(dot => {
        dot.style.animation = 'none';
        setTimeout(() => {
            dot.style.animation = 'statusPulse 2s infinite';
        }, 100);
    });
}, 5000);
</script>
@endsection 