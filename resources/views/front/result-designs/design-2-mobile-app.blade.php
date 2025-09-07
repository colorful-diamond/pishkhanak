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

    .mobile-container {
        max-width: 400px;
        margin: 0 auto;
        min-height: 100vh;
        background: white;
        position: relative;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
    }

    .status-bar {
        background: var(--sky-400);
        color: white;
        padding: 8px 16px;
        font-size: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .app-header {
        background: white;
        padding: 20px 16px;
        border-bottom: 1px solid var(--sky-100);
        position: relative;
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .back-button {
        background: var(--sky-100);
        color: var(--sky-600);
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .menu-button {
        background: transparent;
        border: none;
        color: var(--sky-600);
        font-size: 20px;
    }

    .app-title {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        text-align: center;
    }

    .success-indicator {
        background: #22c55e;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
    }

    .content-area {
        padding: 20px 16px;
    }

    .result-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 2px 12px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .card-icon {
        background: var(--sky-400);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .card-title {
        color: var(--sky-700);
        font-size: 16px;
        font-weight: 600;
        margin: 0;
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
    }

    .item-value {
        background: var(--sky-50);
        color: var(--sky-700);
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid var(--sky-100);
        text-align: left;
        direction: ltr;
        max-width: 60%;
        word-break: break-all;
    }

    .highlight-value {
        background: var(--yellow-100);
        border-color: var(--yellow-400);
        color: #000;
        font-weight: 700;
    }

    .floating-actions {
        position: fixed;
        bottom: 20px;
        left: 16px;
        right: 16px;
        max-width: 368px;
        margin: 0 auto;
    }

    .action-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    .action-button {
        background: var(--sky-400);
        color: white;
        border: none;
        padding: 14px 16px;
        border-radius: 12px;
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

    .fab {
        background: var(--yellow-400);
        color: #000;
        border: none;
        width: 56px;
        height: 56px;
        border-radius: 28px;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(251, 191, 36, 0.3);
        transition: all 0.3s ease;
        margin: 0 auto;
    }

    .fab:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
    }

    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        max-width: 400px;
        margin: 0 auto;
        background: white;
        padding: 12px 16px;
        border-top: 1px solid var(--sky-100);
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }

    .nav-items {
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .nav-item {
        text-align: center;
        color: var(--sky-500);
        font-size: 10px;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .nav-item.active {
        background: var(--sky-100);
        color: var(--sky-700);
    }

    .nav-icon {
        font-size: 18px;
        margin-bottom: 4px;
    }

    .progress-bar {
        background: var(--sky-100);
        height: 4px;
        border-radius: 2px;
        overflow: hidden;
        margin: 16px 0;
    }

    .progress-fill {
        background: var(--yellow-400);
        height: 100%;
        width: 100%;
        border-radius: 2px;
        animation: progressLoad 1.5s ease-out;
    }

    @keyframes progressLoad {
        from { width: 0%; }
        to { width: 100%; }
    }

    .notification-toast {
        position: fixed;
        top: 80px;
        left: 16px;
        right: 16px;
        max-width: 368px;
        margin: 0 auto;
        background: var(--sky-600);
        color: white;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        z-index: 1000;
        transform: translateY(-100px);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .notification-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    @media (max-width: 480px) {
        .mobile-container {
            max-width: 100%;
        }
        
        .floating-actions {
            left: 12px;
            right: 12px;
            max-width: none;
        }
        
        .bottom-nav {
            max-width: 100%;
        }
        
        .item-value {
            max-width: 50%;
            font-size: 12px;
        }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح موبایل اپ')

@section('content')
<div class="mobile-container">
    <!-- Status Bar -->
    <div class="status-bar">
        <span>{{ now()->format('H:i') }}</span>
        <span>پیشخوانک</span>
        <span>📶 🔋</span>
    </div>

    <!-- App Header -->
    <div class="app-header">
        <div class="header-actions">
            <button class="back-button" onclick="goBack()">←</button>
            <button class="menu-button">⋯</button>
        </div>
        <h1 class="app-title">{{ $service->title ?? 'تبدیل کارت به شبا' }}</h1>
        <div class="success-indicator">
            ✓ تکمیل شده
        </div>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
        <!-- Input Data Card -->
        @if(isset($inputData) && !empty($inputData))
        <div class="result-card">
            <div class="card-header">
                <div class="card-icon">📥</div>
                <h3 class="card-title">اطلاعات ورودی</h3>
            </div>
            <div class="data-list">
                @foreach($inputData as $key => $value)
                <div class="data-item">
                    <span class="item-label">
                        @switch($key)
                            @case('card_number') کارت @break
                            @case('iban') شبا @break
                            @case('account_number') حساب @break
                            @default {{ $key }}
                        @endswitch
                    </span>
                    <span class="item-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Results Card -->
        <div class="result-card">
            <div class="card-header">
                <div class="card-icon">📤</div>
                <h3 class="card-title">نتایج تبدیل</h3>
            </div>
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
                <div class="data-item">
                    <span class="item-label">
                        @switch($key)
                            @case('iban') شبا @break
                            @case('bank_name') بانک @break
                            @case('account_number') حساب @break
                            @case('account_type') نوع @break
                            @case('is_valid') وضعیت @break
                            @default {{ $key }}
                        @endswitch
                    </span>
                    <span class="item-value {{ $key === 'iban' ? 'highlight-value' : '' }}">
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

        <!-- Spacing for floating actions -->
        <div style="height: 140px;"></div>
    </div>

    <!-- Floating Actions -->
    <div class="floating-actions">
        <div class="action-row">
            <button onclick="copyMobile()" class="action-button">
                📋 کپی
            </button>
            <button onclick="shareMobile()" class="action-button secondary">
                📤 اشتراک
            </button>
        </div>
        <button onclick="showOptions()" class="fab">
            +
        </button>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="nav-items">
            <div class="nav-item">
                <div class="nav-icon">🏠</div>
                <div>خانه</div>
            </div>
            <div class="nav-item active">
                <div class="nav-icon">📊</div>
                <div>نتایج</div>
            </div>
            <div class="nav-item">
                <div class="nav-icon">⚙️</div>
                <div>تنظیمات</div>
            </div>
            <div class="nav-item">
                <div class="nav-icon">👤</div>
                <div>پروفایل</div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div class="notification-toast" id="toast">
        عملیات انجام شد!
    </div>
</div>

<script>
function copyMobile() {
    const items = document.querySelectorAll('.data-item');
    let text = 'نتایج تبدیل - پیشخوانک\n\n';
    
    items.forEach(item => {
        const label = item.querySelector('.item-label').textContent.trim();
        const value = item.querySelector('.item-value').textContent.trim();
        text += `${label}: ${value}\n`;
    });
    
    text += '\nپیشخوانک - pishkhanak.com';
    
    navigator.clipboard.writeText(text).then(() => {
        showToast('اطلاعات کپی شد!');
    });
}

function shareMobile() {
    if (navigator.share) {
        navigator.share({
            title: 'نتیجه تبدیل - پیشخوانک',
            text: 'نتیجه تبدیل من در پیشخوانک',
            url: window.location.href
        });
    } else {
        copyMobile();
    }
}

function showOptions() {
    const options = [
        'دانلود گزارش',
        'ارسال ایمیل',
        'ذخیره در گالری',
        'تبدیل دوباره'
    ];
    
    const choice = Math.floor(Math.random() * options.length);
    showToast(options[choice] + ' انجام شد!');
}

function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '{{ route("app.page.home") }}';
    }
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Auto-hide status after load
window.addEventListener('load', () => {
    setTimeout(() => {
        showToast('نتایج با موفقیت بارگذاری شد');
    }, 500);
});

// Add haptic feedback on touch (if supported)
document.addEventListener('touchstart', function(e) {
    if (e.target.classList.contains('action-button') || e.target.classList.contains('fab')) {
        if (navigator.vibrate) {
            navigator.vibrate(10);
        }
    }
});
</script>
@endsection 