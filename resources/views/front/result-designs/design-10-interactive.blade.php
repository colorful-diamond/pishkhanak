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
        background: linear-gradient(135deg, var(--sky-50) 0%, #ffffff 50%, var(--yellow-100) 100%);
        font-family: 'IRANSansWebFaNum', system-ui, sans-serif;
        overflow-x: hidden;
    }

    .interactive-container {
        max-width: 800px;
        margin: 32px auto;
        padding: 0 20px;
        position: relative;
    }

    .floating-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }

    .particle {
        position: absolute;
        background: var(--sky-400);
        border-radius: 50%;
        opacity: 0.1;
        animation: float 6s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .hero-section {
        background: white;
        border-radius: 24px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 8px 32px rgba(56, 189, 248, 0.1);
        border: 1px solid var(--sky-100);
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.4s ease;
    }

    .hero-section:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(56, 189, 248, 0.2);
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(56, 189, 248, 0.1), transparent);
        transform: rotate(45deg);
        transition: all 0.6s ease;
        opacity: 0;
    }

    .hero-section:hover::before {
        opacity: 1;
        animation: shimmer 1.5s ease-in-out;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: var(--sky-400);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px auto;
        color: white;
        font-size: 32px;
        position: relative;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .success-icon::after {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        border: 2px solid var(--yellow-400);
        border-radius: 50%;
        animation: spin 3s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .hero-title {
        color: var(--sky-700);
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 12px 0;
        position: relative;
    }

    .hero-subtitle {
        color: var(--sky-600);
        font-size: 16px;
        margin: 0;
    }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .interactive-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(56, 189, 248, 0.08);
        border: 1px solid var(--sky-100);
        position: relative;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .interactive-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 40px rgba(56, 189, 248, 0.15);
        border-color: var(--yellow-400);
    }

    .interactive-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
        transition: left 0.5s ease;
    }

    .interactive-card:hover::before {
        left: 100%;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        background: var(--sky-400);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .interactive-card:hover .card-icon {
        background: var(--yellow-400);
        color: #000;
        transform: rotateY(180deg);
    }

    .card-title {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    .data-items {
        space-y: 16px;
    }

    .data-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--sky-50);
        position: relative;
        transition: all 0.3s ease;
    }

    .data-item:hover {
        background: var(--sky-50);
        margin: 0 -28px;
        padding: 12px 28px;
        border-radius: 8px;
        border-bottom: 1px solid transparent;
    }

    .data-item:last-child {
        border-bottom: none;
    }

    .item-label {
        color: var(--sky-600);
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .data-item:hover .item-label {
        color: var(--sky-700);
        font-weight: 600;
    }

    .item-value {
        background: var(--yellow-100);
        color: #000;
        padding: 6px 12px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid var(--yellow-400);
        position: relative;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .item-value:hover {
        background: var(--yellow-400);
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
    }

    .copy-tooltip {
        position: absolute;
        top: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--sky-700);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .copy-tooltip.show {
        opacity: 1;
        transform: translateX(-50%) translateY(-4px);
    }

    .highlight-value {
        background: linear-gradient(135deg, var(--yellow-400), var(--yellow-500));
        color: white;
        font-size: 16px;
        font-weight: 700;
        animation: glow 2s infinite alternate;
    }

    @keyframes glow {
        from { box-shadow: 0 0 10px rgba(251, 191, 36, 0.5); }
        to { box-shadow: 0 0 20px rgba(251, 191, 36, 0.8); }
    }

    .action-section {
        background: white;
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(56, 189, 248, 0.08);
        border: 1px solid var(--sky-100);
        text-align: center;
        margin-bottom: 32px;
    }

    .action-title {
        color: var(--sky-700);
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 24px;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .action-btn {
        background: var(--sky-400);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 16px 20px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .action-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(56, 189, 248, 0.3);
    }

    .action-btn:active {
        transform: translateY(-2px);
    }

    .action-btn.secondary {
        background: var(--yellow-400);
        color: #000;
    }

    .action-btn.secondary:hover {
        box-shadow: 0 8px 24px rgba(251, 191, 36, 0.3);
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .action-btn:hover::before {
        left: 100%;
    }

    .floating-badge {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: var(--sky-400);
        color: white;
        padding: 12px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 20px rgba(56, 189, 248, 0.3);
        z-index: 1000;
        cursor: pointer;
        transition: all 0.3s ease;
        animation: bounce 2s infinite;
    }

    .floating-badge:hover {
        transform: scale(1.1);
        background: var(--yellow-400);
        color: #000;
    }

    @keyframes bounce {
        0%, 20%, 53%, 80%, 100% {
            transform: translate3d(0, 0, 0);
        }
        40%, 43% {
            transform: translate3d(0, -10px, 0);
        }
        70% {
            transform: translate3d(0, -5px, 0);
        }
        90% {
            transform: translate3d(0, -2px, 0);
        }
    }

    @media (max-width: 768px) {
        .interactive-container {
            padding: 0 12px;
            margin: 16px auto;
        }
        
        .hero-section {
            padding: 24px;
        }
        
        .interactive-card {
            padding: 20px;
        }
        
        .cards-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            grid-template-columns: 1fr;
        }
        
        .floating-badge {
            bottom: 16px;
            right: 16px;
            font-size: 12px;
            padding: 10px 16px;
        }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح تعاملی')

@section('content')
<!-- Floating Particles -->
<div class="floating-particles">
    <div class="particle" style="left: 10%; top: 20%; width: 4px; height: 4px; animation-delay: 0s;"></div>
    <div class="particle" style="left: 80%; top: 30%; width: 6px; height: 6px; animation-delay: 1s;"></div>
    <div class="particle" style="left: 50%; top: 60%; width: 3px; height: 3px; animation-delay: 2s;"></div>
    <div class="particle" style="left: 20%; top: 70%; width: 5px; height: 5px; animation-delay: 3s;"></div>
    <div class="particle" style="left: 90%; top: 80%; width: 4px; height: 4px; animation-delay: 4s;"></div>
</div>

<div class="interactive-container">
    <!-- Hero Section -->
    <div class="hero-section" onclick="celebrateSuccess()">
        <div class="success-icon">✓</div>
        <h1 class="hero-title">{{ $service->title ?? 'تبدیل کارت به شبا' }}</h1>
        <p class="hero-subtitle">عملیات با موفقیت انجام شد! روی این کارت کلیک کنید</p>
    </div>

    <!-- Data Cards -->
    <div class="cards-grid">
        <!-- Input Card -->
        @if(isset($inputData) && !empty($inputData))
        <div class="interactive-card">
            <div class="card-header">
                <div class="card-icon">📥</div>
                <div class="card-title">اطلاعات ورودی</div>
            </div>
            <div class="data-items">
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
                    <span class="item-value" onclick="copyInteractive(this, '{{ $value }}')">
                        {{ $value }}
                        <span class="copy-tooltip">کلیک برای کپی</span>
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Results Card -->
        <div class="interactive-card">
            <div class="card-header">
                <div class="card-icon">📤</div>
                <div class="card-title">نتایج تبدیل</div>
            </div>
            <div class="data-items">
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
                            @case('iban') شماره شبا @break
                            @case('bank_name') نام بانک @break
                            @case('account_number') شماره حساب @break
                            @case('account_type') نوع حساب @break
                            @case('is_valid') وضعیت اعتبار @break
                            @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </span>
                    <span class="item-value {{ $key === 'iban' ? 'highlight-value' : '' }}" onclick="copyInteractive(this, '{{ $value }}')">
                        @if($key === 'is_valid')
                            {{ $value ? '✓ معتبر' : '✗ نامعتبر' }}
                        @else
                            {{ $value }}
                        @endif
                        <span class="copy-tooltip">کلیک برای کپی</span>
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="action-section">
        <h2 class="action-title">عملیات در دسترس</h2>
        <div class="action-buttons">
            <button onclick="copyAllInteractive()" class="action-btn">
                📋 کپی همه نتایج
            </button>
            <button onclick="shareInteractive()" class="action-btn secondary">
                📤 اشتراک‌گذاری
            </button>
            <button onclick="downloadInteractive()" class="action-btn">
                📄 دانلود گزارش
            </button>
            <a href="#" class="action-btn secondary">
                🔄 تبدیل دوباره
            </a>
            <a href="{{ route('app.page.home') }}" class="action-btn">
                🏠 بازگشت به خانه
            </a>
            <button onclick="fullScreenMode()" class="action-btn secondary">
                🔍 نمایش تمام صفحه
            </button>
        </div>
    </div>
</div>

<!-- Floating Badge -->
<div class="floating-badge" onclick="showHelp()">
    💡 راهنما
</div>

<script>
let interactionCount = 0;

function copyInteractive(element, value) {
    navigator.clipboard.writeText(value).then(() => {
        const tooltip = element.querySelector('.copy-tooltip');
        tooltip.textContent = 'کپی شد!';
        tooltip.classList.add('show');
        
        // Add celebration effect
        element.style.transform = 'scale(1.1) rotate(5deg)';
        element.style.background = 'var(--sky-400)';
        element.style.color = 'white';
        
        setTimeout(() => {
            tooltip.classList.remove('show');
            tooltip.textContent = 'کلیک برای کپی';
            element.style.transform = '';
            element.style.background = '';
            element.style.color = '';
        }, 1500);
        
        // Create floating animation
        createFloatingText('کپی شد!', element);
        interactionCount++;
        updateInteractionBadge();
    });
}

function copyAllInteractive() {
    const values = document.querySelectorAll('.item-value');
    let text = 'نتایج تبدیل - پیشخوانک\n';
    text += '✨'.repeat(20) + '\n\n';
    
    values.forEach((value, index) => {
        const label = value.closest('.data-item').querySelector('.item-label').textContent.trim();
        const val = value.textContent.replace('کلیک برای کپی', '').trim();
        text += `${label}: ${val}\n`;
    });
    
    text += '\n🚀 تولید شده با پیشخوانک (pishkhanak.com)';
    
    navigator.clipboard.writeText(text).then(() => {
        showInteractiveNotification('همه نتایج کپی شد!', 'success');
        createConfetti();
    });
}

function shareInteractive() {
    if (navigator.share) {
        navigator.share({
            title: '🎉 نتیجه تبدیل - پیشخوانک',
            text: 'نتیجه فوق‌العاده تبدیل من در پیشخوانک!',
            url: window.location.href
        });
    } else {
        copyAllInteractive();
    }
}

function downloadInteractive() {
    showInteractiveNotification('گزارش در حال آماده‌سازی...', 'info');
    // Simulate download progress
    let progress = 0;
    const interval = setInterval(() => {
        progress += 20;
        showInteractiveNotification(`پیشرفت: ${progress}%`, 'info');
        if (progress >= 100) {
            clearInterval(interval);
            showInteractiveNotification('گزارش آماده شد!', 'success');
        }
    }, 500);
}

function celebrateSuccess() {
    createConfetti();
    showInteractiveNotification('🎉 تبریک! عملیات موفق بود!', 'success');
    
    // Animate hero section
    const hero = document.querySelector('.hero-section');
    hero.style.animation = 'bounce 0.6s ease';
    setTimeout(() => {
        hero.style.animation = '';
    }, 600);
}

function fullScreenMode() {
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
        showInteractiveNotification('حالت تمام صفحه فعال شد', 'info');
    }
}

function showHelp() {
    const helpText = `
🎯 راهنمای استفاده:
• روی مقادیر کلیک کنید تا کپی شوند
• روی کارت‌ها هاور کنید
• از دکمه‌های عملیات استفاده کنید
• لذت ببرید! 🚀
    `;
    
    showInteractiveNotification(helpText, 'info');
}

function createFloatingText(text, element) {
    const floating = document.createElement('div');
    floating.textContent = text;
    floating.style.cssText = `
        position: absolute;
        left: ${element.offsetLeft + element.offsetWidth/2}px;
        top: ${element.offsetTop}px;
        color: var(--sky-400);
        font-weight: bold;
        pointer-events: none;
        z-index: 1000;
        animation: floatUp 2s ease-out forwards;
    `;
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes floatUp {
            0% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-50px); }
        }
    `;
    document.head.appendChild(style);
    
    element.closest('.interactive-card').appendChild(floating);
    
    setTimeout(() => {
        floating.remove();
        style.remove();
    }, 2000);
}

function createConfetti() {
    for (let i = 0; i < 30; i++) {
        const confetti = document.createElement('div');
        confetti.style.cssText = `
            position: fixed;
            left: ${Math.random() * 100}%;
            top: -10px;
            width: 10px;
            height: 10px;
            background: ${Math.random() > 0.5 ? 'var(--sky-400)' : 'var(--yellow-400)'};
            border-radius: 50%;
            pointer-events: none;
            z-index: 1000;
            animation: confettiFall 3s ease-out forwards;
        `;
        
        document.body.appendChild(confetti);
        
        setTimeout(() => {
            confetti.remove();
        }, 3000);
    }
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes confettiFall {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
    
    setTimeout(() => {
        style.remove();
    }, 3000);
}

function updateInteractionBadge() {
    const badge = document.querySelector('.floating-badge');
    if (interactionCount > 0) {
        badge.textContent = `🎯 ${interactionCount} تعامل`;
    }
}

function showInteractiveNotification(message, type) {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'var(--sky-400)' : type === 'info' ? 'var(--yellow-400)' : 'var(--sky-600)';
    const textColor = type === 'info' ? '#000' : 'white';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: ${textColor};
        padding: 16px 24px;
        border-radius: 12px;
        z-index: 1000;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        max-width: 300px;
        white-space: pre-line;
        animation: slideInBounce 0.5s ease-out;
    `;
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInBounce {
            0% { transform: translateX(100%) scale(0.8); opacity: 0; }
            60% { transform: translateX(-10%) scale(1.05); opacity: 1; }
            100% { transform: translateX(0) scale(1); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideInBounce 0.3s ease-out reverse';
        setTimeout(() => {
            notification.remove();
            style.remove();
        }, 300);
    }, 4000);
}

// Initialize particles
window.addEventListener('load', () => {
    showInteractiveNotification('صفحه تعاملی بارگذاری شد! 🎉', 'success');
});

// Add mouse trail effect
document.addEventListener('mousemove', (e) => {
    if (Math.random() > 0.98) {
        const trail = document.createElement('div');
        trail.style.cssText = `
            position: fixed;
            left: ${e.clientX}px;
            top: ${e.clientY}px;
            width: 4px;
            height: 4px;
            background: var(--sky-400);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            animation: fadeOut 1s ease-out forwards;
        `;
        
        document.body.appendChild(trail);
        
        setTimeout(() => {
            trail.remove();
        }, 1000);
    }
});

const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        0% { opacity: 0.6; transform: scale(1); }
        100% { opacity: 0; transform: scale(0); }
    }
`;
document.head.appendChild(style);
</script>
@endsection 