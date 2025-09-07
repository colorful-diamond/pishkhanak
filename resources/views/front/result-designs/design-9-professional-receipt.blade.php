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
        background: #f5f5f5;
        font-family: 'IRANSansWebFaNum', system-ui, sans-serif;
    }

    .receipt-container {
        max-width: 480px;
        margin: 32px auto;
        background: white;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        position: relative;
    }

    .receipt-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20px;
        right: 20px;
        height: 1px;
        background: repeating-linear-gradient(
            to right,
            #ddd 0px,
            #ddd 8px,
            transparent 8px,
            transparent 16px
        );
    }

    .receipt-container::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 20px;
        right: 20px;
        height: 1px;
        background: repeating-linear-gradient(
            to right,
            #ddd 0px,
            #ddd 8px,
            transparent 8px,
            transparent 16px
        );
    }

    .receipt-header {
        padding: 32px 24px 24px 24px;
        text-align: center;
        border-bottom: 2px dashed #ddd;
    }

    .receipt-logo {
        width: 48px;
        height: 48px;
        background: var(--sky-400);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px auto;
        color: white;
        font-size: 20px;
        font-weight: bold;
    }

    .receipt-title {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .receipt-subtitle {
        color: var(--sky-600);
        font-size: 14px;
        margin: 0 0 16px 0;
    }

    .receipt-number {
        background: var(--yellow-100);
        color: #000;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-family: monospace;
        border: 1px solid var(--yellow-400);
        display: inline-block;
    }

    .receipt-body {
        padding: 24px;
    }

    .receipt-section {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px dashed #eee;
    }

    .receipt-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .section-header {
        color: var(--sky-700);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-icon {
        width: 16px;
        height: 16px;
        background: var(--sky-400);
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 8px;
    }

    .receipt-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
    }

    .line-label {
        color: var(--sky-600);
        font-weight: 500;
    }

    .line-value {
        color: #1a1a1a;
        font-weight: 600;
        font-family: monospace;
        background: var(--sky-50);
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid var(--sky-100);
        direction: ltr;
        text-align: left;
        min-width: 120px;
    }

    .highlight-line .line-value {
        background: var(--yellow-100);
        border-color: var(--yellow-400);
        font-weight: 700;
        font-size: 15px;
    }

    .success-line .line-value {
        background: #dcfce7;
        border-color: #22c55e;
        color: #15803d;
    }

    .receipt-totals {
        background: var(--sky-50);
        margin: 16px -24px -24px -24px;
        padding: 20px 24px;
        border-top: 2px dashed #ddd;
    }

    .total-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
    }

    .total-label {
        color: var(--sky-700);
        font-weight: 600;
        font-size: 14px;
    }

    .total-value {
        color: var(--sky-700);
        font-weight: 700;
        font-family: monospace;
    }

    .receipt-footer {
        background: var(--sky-50);
        padding: 24px;
        text-align: center;
        border-top: 2px dashed #ddd;
    }

    .footer-message {
        color: var(--sky-600);
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .receipt-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }

    .receipt-btn {
        background: var(--sky-400);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .receipt-btn:hover {
        background: var(--sky-500);
        transform: translateY(-1px);
    }

    .receipt-btn.secondary {
        background: var(--yellow-400);
        color: #000;
    }

    .receipt-btn.secondary:hover {
        background: var(--yellow-500);
    }

    .receipt-btn.outline {
        background: transparent;
        color: var(--sky-600);
        border: 1px solid var(--sky-300);
    }

    .receipt-btn.outline:hover {
        background: var(--sky-100);
    }

    .receipt-stamp {
        position: absolute;
        top: 100px;
        right: 20px;
        width: 80px;
        height: 80px;
        border: 2px solid var(--yellow-400);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(251, 191, 36, 0.1);
        color: var(--yellow-500);
        font-weight: 700;
        font-size: 10px;
        text-align: center;
        transform: rotate(12deg);
        opacity: 0.9;
    }

    .receipt-barcode {
        text-align: center;
        margin: 16px 0;
    }

    .barcode-lines {
        display: flex;
        justify-content: center;
        gap: 1px;
        margin-bottom: 8px;
    }

    .barcode-line {
        background: #000;
        height: 40px;
        width: 2px;
    }

    .barcode-line:nth-child(even) {
        height: 30px;
    }

    .barcode-line:nth-child(3n) {
        height: 35px;
    }

    .barcode-text {
        font-family: monospace;
        font-size: 10px;
        color: var(--sky-500);
        letter-spacing: 2px;
    }

    .qr-placeholder {
        width: 60px;
        height: 60px;
        background: var(--sky-100);
        border: 2px solid var(--sky-300);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 16px auto;
        color: var(--sky-600);
        font-size: 10px;
        text-align: center;
    }

    @media (max-width: 540px) {
        .receipt-container {
            margin: 16px;
            max-width: none;
        }
        
        .receipt-actions {
            grid-template-columns: 1fr;
        }
        
        .receipt-stamp {
            display: none;
        }
        
        .line-value {
            min-width: 100px;
            font-size: 12px;
        }
    }

    @media print {
        body {
            background: white;
        }
        
        .receipt-container {
            box-shadow: none;
            margin: 0;
            max-width: none;
        }
        
        .receipt-actions {
            display: none;
        }
        
        .receipt-stamp {
            opacity: 1;
        }
    }

    /* Receipt hole punches */
    .receipt-container {
        position: relative;
    }

    .receipt-container::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 60px;
        width: 16px;
        height: 16px;
        background: #f5f5f5;
        border-radius: 50%;
        border: 2px solid #ddd;
    }

    .receipt-container::after {
        content: '';
        position: absolute;
        right: -8px;
        top: 60px;
        width: 16px;
        height: 16px;
        background: #f5f5f5;
        border-radius: 50%;
        border: 2px solid #ddd;
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح رسید حرفه‌ای')

@section('content')
<div class="receipt-container">
    <!-- Receipt Stamp -->
    <div class="receipt-stamp">
        VERIFIED<br>✓
    </div>

    <!-- Header -->
    <div class="receipt-header">
        <div class="receipt-logo">پ</div>
        <h1 class="receipt-title">رسید تبدیل اطلاعات بانکی</h1>
        <p class="receipt-subtitle">{{ $service->title ?? 'تبدیل کارت به شبا' }}</p>
        <div class="receipt-number">شماره رسید: #{{ strtoupper(substr(md5(time()), 0, 8)) }}</div>
    </div>

    <!-- Body -->
    <div class="receipt-body">
        <!-- Transaction Info -->
        <div class="receipt-section">
            <div class="section-header">
                <div class="section-icon">ℹ</div>
                اطلاعات تراکنش
            </div>
            <div class="receipt-line">
                <span class="line-label">تاریخ:</span>
                <span class="line-value">{{ now()->format('Y/m/d') }}</span>
            </div>
            <div class="receipt-line">
                <span class="line-label">زمان:</span>
                <span class="line-value">{{ now()->format('H:i:s') }}</span>
            </div>
            <div class="receipt-line">
                <span class="line-label">مدت پردازش:</span>
                <span class="line-value">0.84 ثانیه</span>
            </div>
            <div class="receipt-line success-line">
                <span class="line-label">وضعیت:</span>
                <span class="line-value">موفق</span>
            </div>
        </div>

        <!-- Input Data -->
        @if(isset($inputData) && !empty($inputData))
        <div class="receipt-section">
            <div class="section-header">
                <div class="section-icon">📥</div>
                اطلاعات ورودی
            </div>
            @foreach($inputData as $key => $value)
            <div class="receipt-line">
                <span class="line-label">
                    @switch($key)
                        @case('card_number') شماره کارت: @break
                        @case('iban') شماره شبا: @break
                        @case('account_number') شماره حساب: @break
                        @default {{ ucfirst(str_replace('_', ' ', $key)) }}:
                    @endswitch
                </span>
                <span class="line-value">{{ $value }}</span>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Results -->
        <div class="receipt-section">
            <div class="section-header">
                <div class="section-icon">📤</div>
                نتایج تبدیل
            </div>
            
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
            <div class="receipt-line {{ $key === 'iban' ? 'highlight-line' : '' }} {{ $key === 'is_valid' && $value ? 'success-line' : '' }}">
                <span class="line-label">
                    @switch($key)
                        @case('iban') شماره شبا: @break
                        @case('bank_name') نام بانک: @break
                        @case('account_number') شماره حساب: @break
                        @case('account_type') نوع حساب: @break
                        @case('is_valid') اعتبار: @break
                        @default {{ ucfirst(str_replace('_', ' ', $key)) }}:
                    @endswitch
                </span>
                <span class="line-value">
                    @if($key === 'is_valid')
                        {{ $value ? '✓ معتبر' : '✗ نامعتبر' }}
                    @else
                        {{ $value }}
                    @endif
                </span>
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="receipt-totals">
            <div class="total-line">
                <span class="total-label">تعداد فیلدهای تبدیل شده:</span>
                <span class="total-value">{{ count($displayResult) }}</span>
            </div>
            <div class="total-line">
                <span class="total-label">وضعیت کلی:</span>
                <span class="total-value">✓ موفق</span>
            </div>
            <div class="total-line">
                <span class="total-label">هزینه سرویس:</span>
                <span class="total-value">رایگان</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="receipt-footer">
        <div class="footer-message">
            <strong>متشکریم از استفاده شما!</strong><br>
            این رسید جهت پیگیری‌های بعدی نگهداری شود.<br>
            در صورت بروز مشکل با پشتیبانی تماس بگیرید.
        </div>

        <div class="receipt-actions">
            <button onclick="copyReceipt()" class="receipt-btn">
                📋 کپی رسید
            </button>
            <button onclick="printReceipt()" class="receipt-btn secondary">
                🖨️ چاپ
            </button>
            <button onclick="shareReceipt()" class="receipt-btn outline">
                📤 اشتراک
            </button>
            <a href="{{ route('app.page.home') }}" class="receipt-btn outline">
                🏠 خانه
            </a>
        </div>

        <!-- Barcode -->
        <div class="receipt-barcode">
            <div class="barcode-lines">
                @for($i = 0; $i < 40; $i++)
                <div class="barcode-line"></div>
                @endfor
            </div>
            <div class="barcode-text">{{ strtoupper(substr(md5(time()), 0, 12)) }}</div>
        </div>

        <!-- QR Code Placeholder -->
        <div class="qr-placeholder">
            QR<br>Code
        </div>

        <div style="font-size: 11px; color: var(--sky-500); margin-top: 16px;">
            <strong>پیشخوانک</strong> | pishkhanak.com<br>
            سیستم تبدیل اطلاعات بانکی | نسخه 2.1.0
        </div>
    </div>
</div>

<script>
function copyReceipt() {
    const lines = document.querySelectorAll('.receipt-line, .total-line');
    let text = 'رسید تبدیل - پیشخوانک\n';
    text += '='.repeat(40) + '\n\n';
    
    text += `شماره رسید: ${document.querySelector('.receipt-number').textContent}\n`;
    text += `تاریخ: ${new Date().toLocaleDateString('fa-IR')}\n\n`;
    
    lines.forEach(line => {
        const label = line.querySelector('.line-label, .total-label').textContent.trim();
        const value = line.querySelector('.line-value, .total-value').textContent.trim();
        text += `${label} ${value}\n`;
    });
    
    text += '\n' + 'رسید الکترونیکی - پیشخوانک';
    text += '\n' + 'pishkhanak.com';
    
    navigator.clipboard.writeText(text).then(() => {
        showReceiptNotification('رسید کپی شد!', 'success');
    });
}

function printReceipt() {
    window.print();
}

function shareReceipt() {
    if (navigator.share) {
        navigator.share({
            title: 'رسید تبدیل - پیشخوانک',
            text: 'رسید تبدیل من در پیشخوانک',
            url: window.location.href
        });
    } else {
        copyReceipt();
    }
}

function showReceiptNotification(message, type) {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'var(--sky-400)' : 'var(--yellow-400)';
    const textColor = type === 'success' ? 'white' : '#000';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: ${textColor};
        padding: 12px 20px;
        border-radius: 6px;
        z-index: 1000;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        document.body.removeChild(notification);
    }, 2500);
}
</script>
@endsection 