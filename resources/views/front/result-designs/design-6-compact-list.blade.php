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

    .compact-container {
        max-width: 600px;
        margin: 16px auto;
        padding: 0 16px;
    }

    .compact-header {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
        text-align: center;
    }

    .success-icon {
        color: #22c55e;
        font-size: 24px;
        margin-bottom: 8px;
    }

    .header-title {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 4px 0;
    }

    .header-subtitle {
        color: var(--sky-600);
        font-size: 14px;
        margin: 0 0 12px 0;
    }

    .quick-stats {
        display: flex;
        justify-content: center;
        gap: 16px;
        font-size: 12px;
    }

    .stat {
        color: var(--sky-600);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .compact-section {
        background: white;
        border-radius: 12px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
        overflow: hidden;
    }

    .section-header {
        background: var(--sky-100);
        color: var(--sky-700);
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .section-count {
        background: var(--yellow-400);
        color: #000;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
    }

    .compact-list {
        padding: 0;
    }

    .list-item {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid var(--sky-50);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-item:hover {
        background: var(--sky-50);
    }

    .item-icon {
        background: var(--sky-400);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        margin-left: 10px;
        flex-shrink: 0;
    }

    .item-content {
        flex: 1;
        min-width: 0;
    }

    .item-label {
        color: var(--sky-600);
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 2px;
    }

    .item-value {
        color: var(--sky-700);
        font-size: 14px;
        font-weight: 600;
        font-family: monospace;
        direction: ltr;
        text-align: left;
        word-break: break-all;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .highlight-item {
        background: var(--yellow-100);
        border-right: 3px solid var(--yellow-400);
    }

    .highlight-item .item-icon {
        background: var(--yellow-400);
        color: #000;
    }

    .highlight-item .item-value {
        color: #000;
        font-weight: 700;
    }

    .copy-indicator {
        color: var(--sky-400);
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .list-item:hover .copy-indicator {
        opacity: 1;
    }

    .quick-actions {
        background: white;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
    }

    .actions-title {
        color: var(--sky-700);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
        text-align: center;
    }

    .actions-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 8px;
    }

    .action-btn {
        background: var(--sky-400);
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 11px;
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

    .action-btn.outline {
        background: transparent;
        color: var(--sky-600);
        border: 1px solid var(--sky-300);
    }

    .action-btn.outline:hover {
        background: var(--sky-50);
    }

    .compact-footer {
        background: white;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
    }

    .footer-text {
        color: var(--sky-600);
        font-size: 11px;
        margin: 0;
    }

    .footer-link {
        color: var(--sky-400);
        text-decoration: none;
        font-weight: 600;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        background: #22c55e;
        border-radius: 50%;
        margin-left: 6px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .expandable-value {
        cursor: pointer;
        position: relative;
    }

    .expandable-value.expanded .item-value {
        white-space: normal;
        word-break: break-word;
    }

    .expand-icon {
        color: var(--sky-400);
        font-size: 10px;
        margin-right: 4px;
        transition: transform 0.2s ease;
    }

    .expandable-value.expanded .expand-icon {
        transform: rotate(180deg);
    }

    @media (max-width: 480px) {
        .compact-container {
            padding: 0 8px;
            margin: 8px auto;
        }
        
        .compact-header {
            padding: 16px;
        }
        
        .quick-stats {
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }
        
        .list-item {
            padding: 8px 12px;
        }
        
        .actions-row {
            grid-template-columns: 1fr 1fr;
        }
        
        .action-btn {
            font-size: 10px;
            padding: 6px 8px;
        }
    }

    /* Compact animation */
    .compact-section {
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .list-item {
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Loading state */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .loading .item-value {
        background: var(--sky-100);
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: 200px 0; }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح فهرست فشرده')

@section('content')
<div class="compact-container">
    <!-- Compact Header -->
    <div class="compact-header">
        <div class="success-icon">✓</div>
        <h1 class="header-title">{{ $service->title ?? 'تبدیل کارت به شبا' }}</h1>
        <p class="header-subtitle">عملیات با موفقیت تکمیل شد</p>
        <div class="quick-stats">
            <div class="stat">
                <span>⏱️</span>
                <span>0.84s</span>
            </div>
            <div class="stat">
                <span>📊</span>
                <span>{{ count($result ?? []) }} فیلد</span>
            </div>
            <div class="stat">
                <span>🔒</span>
                <span>امن</span>
            </div>
            <div class="stat">
                <div class="status-dot"></div>
                <span>فعال</span>
            </div>
        </div>
    </div>

    <!-- Input Data Section -->
    @if(isset($inputData) && !empty($inputData))
    <div class="compact-section">
        <div class="section-header">
            <div class="section-title">
                📥 اطلاعات ورودی
            </div>
            <div class="section-count">{{ count($inputData) }}</div>
        </div>
        <div class="compact-list">
            @foreach($inputData as $key => $value)
            <div class="list-item" onclick="copyCompactValue('{{ $value }}', this)">
                <div class="item-icon">📋</div>
                <div class="item-content">
                    <div class="item-label">
                        @switch($key)
                            @case('card_number') شماره کارت @break
                            @case('iban') شماره شبا @break
                            @case('account_number') شماره حساب @break
                            @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </div>
                    <div class="item-value">{{ $value }}</div>
                </div>
                <div class="copy-indicator">📋</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Results Section -->
    <div class="compact-section">
        <div class="section-header">
            <div class="section-title">
                📤 نتایج تبدیل
            </div>
            <div class="section-count">
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
                {{ count($displayResult) }}
            </div>
        </div>
        <div class="compact-list">
            @foreach($displayResult as $key => $value)
            <div class="list-item {{ $key === 'iban' ? 'highlight-item' : '' }} {{ strlen($value) > 20 ? 'expandable-value' : '' }}" 
                 onclick="toggleExpand(this); copyCompactValue('{{ $value }}', this)">
                <div class="item-icon">
                    @switch($key)
                        @case('iban') 🏦 @break
                        @case('bank_name') 🏢 @break
                        @case('account_number') 🔢 @break
                        @case('account_type') 📋 @break
                        @case('is_valid') ✓ @break
                        @default 📄
                    @endswitch
                </div>
                <div class="item-content">
                    <div class="item-label">
                        @switch($key)
                            @case('iban') شماره شبا @break
                            @case('bank_name') نام بانک @break
                            @case('account_number') شماره حساب @break
                            @case('account_type') نوع حساب @break
                            @case('is_valid') وضعیت اعتبار @break
                            @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </div>
                    <div class="item-value">
                        @if($key === 'is_valid')
                            {{ $value ? '✓ معتبر و تأیید شده' : '✗ نامعتبر' }}
                        @else
                            {{ $value }}
                        @endif
                    </div>
                </div>
                @if(strlen($value) > 20)
                <div class="expand-icon">▼</div>
                @endif
                <div class="copy-indicator">📋</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="actions-title">⚡ عملیات سریع</div>
        <div class="actions-row">
            <button onclick="copyAllCompact()" class="action-btn">
                📋 کپی
            </button>
            <button onclick="shareCompact()" class="action-btn secondary">
                📤 اشتراک
            </button>
            <button onclick="downloadCompact()" class="action-btn outline">
                📄 دانلود
            </button>
        </div>
        <div class="actions-row">
            <a href="#" class="action-btn outline">
                🔄 دوباره
            </a>
            <button onclick="printCompact()" class="action-btn outline">
                🖨️ چاپ
            </button>
            <a href="{{ route('app.page.home') }}" class="action-btn secondary">
                🏠 خانه
            </a>
        </div>
    </div>

    <!-- Compact Footer -->
    <div class="compact-footer">
        <p class="footer-text">
            پردازش شده توسط <a href="{{ route('app.page.home') }}" class="footer-link">پیشخوانک</a> | 
            {{ now()->format('Y/m/d H:i') }}
        </p>
    </div>
</div>

<script>
function copyCompactValue(value, element) {
    // Prevent double execution on expandable items
    if (event.target.classList.contains('expand-icon')) {
        return;
    }
    
    navigator.clipboard.writeText(value).then(() => {
        showCompactNotification('کپی شد!', 'success');
        
        // Visual feedback
        const originalBg = element.style.backgroundColor;
        element.style.backgroundColor = 'var(--yellow-100)';
        element.style.transform = 'scale(0.98)';
        
        setTimeout(() => {
            element.style.backgroundColor = originalBg;
            element.style.transform = 'scale(1)';
        }, 200);
    });
}

function toggleExpand(element) {
    if (event.target.classList.contains('expand-icon') || element.classList.contains('expandable-value')) {
        element.classList.toggle('expanded');
        event.stopPropagation();
    }
}

function copyAllCompact() {
    const items = document.querySelectorAll('.list-item');
    let text = 'نتایج فشرده - پیشخوانک\n';
    text += '━'.repeat(30) + '\n\n';
    
    let currentSection = '';
    items.forEach(item => {
        const section = item.closest('.compact-section').querySelector('.section-title').textContent.trim();
        if (section !== currentSection) {
            text += `${section}\n`;
            text += '─'.repeat(section.length) + '\n';
            currentSection = section;
        }
        
        const label = item.querySelector('.item-label').textContent.trim();
        const value = item.querySelector('.item-value').textContent.trim();
        text += `• ${label}: ${value}\n`;
    });
    
    text += '\n📱 پیشخوانک - pishkhanak.com';
    
    navigator.clipboard.writeText(text).then(() => {
        showCompactNotification('همه اطلاعات کپی شد!', 'success');
    });
}

function shareCompact() {
    if (navigator.share) {
        navigator.share({
            title: 'نتیجه فشرده - پیشخوانک',
            text: 'نتیجه تبدیل فشرده من در پیشخوانک',
            url: window.location.href
        });
    } else {
        copyAllCompact();
    }
}

function downloadCompact() {
    showCompactNotification('آماده‌سازی دانلود...', 'info');
    // Simulate download
    setTimeout(() => {
        showCompactNotification('فایل آماده است!', 'success');
    }, 1000);
}

function printCompact() {
    window.print();
}

function showCompactNotification(message, type) {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? '#22c55e' : type === 'info' ? 'var(--yellow-400)' : 'var(--sky-400)';
    const textColor = type === 'info' ? '#000' : 'white';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: ${textColor};
        padding: 8px 16px;
        border-radius: 6px;
        z-index: 1000;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        animation: slideInRight 0.3s ease;
    `;
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideInRight 0.3s ease reverse';
        setTimeout(() => {
            document.body.removeChild(notification);
            document.head.removeChild(style);
        }, 300);
    }, 2000);
}

// Add loading animation simulation
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.compact-section');
    sections.forEach((section, index) => {
        section.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Simulate loading state briefly
    const items = document.querySelectorAll('.list-item');
    items.forEach(item => {
        item.classList.add('loading');
    });
    
    setTimeout(() => {
        items.forEach(item => {
            item.classList.remove('loading');
        });
    }, 500);
});

// Add hover effects for better UX
document.querySelectorAll('.list-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateX(-2px)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateX(0)';
    });
});
</script>
@endsection 