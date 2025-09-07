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
        background: #ffffff;
        font-family: 'IRANSansWebFaNum', system-ui, sans-serif;
        line-height: 1.8;
    }

    .minimal-container {
        max-width: 640px;
        margin: 60px auto;
        padding: 0 32px;
    }

    .minimal-header {
        text-align: center;
        margin-bottom: 80px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        margin-bottom: 24px;
    }

    .main-title {
        font-size: 28px;
        font-weight: 300;
        color: var(--sky-700);
        margin: 0 0 12px 0;
        letter-spacing: -0.02em;
    }

    .subtitle {
        font-size: 16px;
        color: var(--sky-600);
        font-weight: 400;
        margin: 0;
        opacity: 0.7;
    }

    .content-section {
        margin-bottom: 80px;
    }

    .section-number {
        color: var(--yellow-400);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 32px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .data-grid {
        display: grid;
        gap: 24px;
    }

    .data-entry {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 32px;
        padding: 24px 0;
        border-bottom: 1px solid #f1f5f9;
        align-items: center;
    }

    .data-entry:last-child {
        border-bottom: none;
    }

    .entry-label {
        color: var(--sky-600);
        font-size: 14px;
        font-weight: 500;
        text-align: right;
    }

    .entry-value {
        color: #1e293b;
        font-size: 16px;
        font-weight: 400;
        font-family: monospace;
        direction: ltr;
        text-align: left;
        padding: 12px 16px;
        background: var(--sky-50);
        border-radius: 4px;
        border: 1px solid var(--sky-100);
        word-break: break-all;
    }

    .highlight-value {
        background: var(--yellow-100);
        border-color: var(--yellow-400);
        font-weight: 600;
        font-size: 18px;
    }

    .actions-minimal {
        margin-top: 80px;
        text-align: center;
    }

    .action-link {
        display: inline-block;
        color: var(--sky-600);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        padding: 12px 0;
        margin: 0 24px;
        border-bottom: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .action-link:hover {
        color: var(--sky-700);
        border-bottom-color: var(--yellow-400);
    }

    .action-link.primary {
        color: var(--yellow-500);
        font-weight: 600;
    }

    .action-link.primary:hover {
        color: var(--yellow-600);
        border-bottom-color: var(--yellow-500);
    }

    .minimal-footer {
        margin-top: 120px;
        text-align: center;
        padding: 32px 0;
        border-top: 1px solid #f1f5f9;
    }

    .footer-brand {
        color: var(--sky-700);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .footer-meta {
        color: var(--sky-500);
        font-size: 12px;
        opacity: 0.6;
    }

    .spacer {
        height: 40px;
    }

    .validation-mark {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #22c55e;
        font-size: 14px;
        font-weight: 500;
        margin-top: 8px;
    }

    .validation-mark::before {
        content: '✓';
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .minimal-container {
            margin: 40px auto;
            padding: 0 16px;
        }
        
        .minimal-header {
            margin-bottom: 60px;
        }
        
        .content-section {
            margin-bottom: 60px;
        }
        
        .data-entry {
            grid-template-columns: 1fr;
            gap: 12px;
            text-align: center;
        }
        
        .entry-label {
            text-align: center;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--sky-500);
        }
        
        .entry-value {
            text-align: center;
            direction: rtl;
        }
        
        .action-link {
            display: block;
            margin: 8px 0;
        }
        
        .minimal-footer {
            margin-top: 80px;
        }
    }

    @media (max-width: 480px) {
        .main-title {
            font-size: 24px;
        }
        
        .entry-value {
            font-size: 14px;
            padding: 10px 12px;
        }
    }

    /* Subtle animations */
    .data-entry {
        opacity: 0;
        animation: fadeInUp 0.6s ease forwards;
    }

    .data-entry:nth-child(1) { animation-delay: 0.1s; }
    .data-entry:nth-child(2) { animation-delay: 0.2s; }
    .data-entry:nth-child(3) { animation-delay: 0.3s; }
    .data-entry:nth-child(4) { animation-delay: 0.4s; }
    .data-entry:nth-child(5) { animation-delay: 0.5s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح مینیمال مدرن')

@section('content')
<div class="minimal-container">
    <!-- Header -->
    <div class="minimal-header">
        <div class="status-dot"></div>
        <h1 class="main-title">{{ $service->title ?? 'تبدیل کارت به شبا' }}</h1>
        <p class="subtitle">عملیات با موفقیت تکمیل شد</p>
    </div>

    <!-- Input Section -->
    @if(isset($inputData) && !empty($inputData))
    <div class="content-section">
        <div class="section-number">01 — اطلاعات ورودی</div>
        <div class="data-grid">
            @foreach($inputData as $key => $value)
            <div class="data-entry">
                <div class="entry-label">
                    @switch($key)
                        @case('card_number') شماره کارت @break
                        @case('iban') شماره شبا @break
                        @case('account_number') شماره حساب @break
                        @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                    @endswitch
                </div>
                <div class="entry-value">{{ $value }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Results Section -->
    <div class="content-section">
        <div class="section-number">02 — نتایج تبدیل</div>
        <div class="data-grid">
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
            <div class="data-entry">
                <div class="entry-label">
                    @switch($key)
                        @case('iban') شماره شبا @break
                        @case('bank_name') نام بانک @break
                        @case('account_number') شماره حساب @break
                        @case('account_type') نوع حساب @break
                        @case('is_valid') وضعیت اعتبار @break
                        @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                    @endswitch
                </div>
                <div class="entry-value {{ $key === 'iban' ? 'highlight-value' : '' }}">
                    @if($key === 'is_valid')
                        {{ $value ? 'معتبر' : 'نامعتبر' }}
                        @if($value)
                        <div class="validation-mark">تأیید شده</div>
                        @endif
                    @else
                        {{ $value }}
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="spacer"></div>

    <!-- Actions -->
    <div class="actions-minimal">
        <a href="#" onclick="copyMinimal(); return false;" class="action-link primary">کپی اطلاعات</a>
        <a href="#" onclick="shareMinimal(); return false;" class="action-link">اشتراک‌گذاری</a>
        <a href="#" class="action-link">تبدیل دوباره</a>
        <a href="{{ route('app.page.home') }}" class="action-link">صفحه اصلی</a>
    </div>

    <!-- Footer -->
    <div class="minimal-footer">
        <div class="footer-brand">پیشخوانک</div>
        <div class="footer-meta">
            pishkhanak.com — {{ now()->format('Y/m/d H:i') }}
        </div>
    </div>
</div>

<script>
function copyMinimal() {
    const values = document.querySelectorAll('.entry-value');
    let text = '';
    
    values.forEach((value, index) => {
        const label = value.closest('.data-entry').querySelector('.entry-label').textContent.trim();
        const val = value.textContent.trim().replace(/\s+/g, ' ');
        text += `${label}: ${val}\n`;
    });
    
    text += '\nپیشخوانک - pishkhanak.com';
    
    navigator.clipboard.writeText(text).then(() => {
        showMinimalNotification('اطلاعات کپی شد');
    });
}

function shareMinimal() {
    if (navigator.share) {
        navigator.share({
            title: 'نتیجه تبدیل - پیشخوانک',
            text: 'نتیجه تبدیل من در پیشخوانک',
            url: window.location.href
        });
    } else {
        copyMinimal();
    }
}

function showMinimalNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 40px;
        right: 40px;
        background: var(--sky-700);
        color: white;
        padding: 16px 24px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        opacity: 0;
        animation: slideIn 0.3s ease forwards;
    `;
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    `;
    document.head.appendChild(style);
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => {
            document.body.removeChild(notification);
            document.head.removeChild(style);
        }, 300);
    }, 2000);
}
</script>
@endsection 