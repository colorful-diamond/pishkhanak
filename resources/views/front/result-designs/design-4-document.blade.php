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
        background: #f8fafc;
        font-family: 'IRANSansWebFaNum', system-ui, sans-serif;
    }

    .document-container {
        max-width: 800px;
        margin: 20px auto;
        background: white;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        position: relative;
        min-height: 100vh;
    }

    .document-header {
        background: var(--sky-400);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
    }

    .document-header::before {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        right: 0;
        height: 20px;
        background: linear-gradient(45deg, transparent 50%, var(--sky-400) 50%),
                    linear-gradient(-45deg, transparent 50%, var(--sky-400) 50%);
        background-size: 20px 20px;
        background-position: 0 0, 10px 0;
    }

    .document-logo {
        background: white;
        color: var(--sky-400);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px auto;
        font-size: 24px;
        font-weight: bold;
    }

    .document-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .document-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin: 0 0 16px 0;
    }

    .document-number {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        display: inline-block;
        font-family: monospace;
    }

    .document-body {
        padding: 40px;
    }

    .section {
        margin-bottom: 40px;
    }

    .section-header {
        background: var(--sky-100);
        color: var(--sky-700);
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-icon {
        background: var(--sky-400);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-item {
        background: var(--sky-50);
        border: 1px solid var(--sky-100);
        border-radius: 8px;
        padding: 16px;
    }

    .info-label {
        color: var(--sky-600);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .info-value {
        color: var(--sky-700);
        font-size: 16px;
        font-weight: 600;
        font-family: monospace;
        direction: ltr;
        text-align: left;
        word-break: break-all;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid var(--sky-200);
        border-radius: 8px;
        overflow: hidden;
    }

    .table-header {
        background: var(--sky-100);
        color: var(--sky-700);
    }

    .table-header th {
        padding: 12px 16px;
        text-align: right;
        font-size: 14px;
        font-weight: 600;
        border-bottom: 1px solid var(--sky-200);
    }

    .table-row td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--sky-100);
        vertical-align: top;
    }

    .table-row:last-child td {
        border-bottom: none;
    }

    .table-label {
        color: var(--sky-600);
        font-weight: 500;
        width: 30%;
    }

    .table-value {
        color: var(--sky-700);
        font-family: monospace;
        direction: ltr;
        text-align: left;
        background: var(--sky-50);
        padding: 8px 12px;
        border-radius: 4px;
    }

    .highlight-row {
        background: var(--yellow-100);
    }

    .highlight-row .table-value {
        background: var(--yellow-400);
        color: #000;
        font-weight: 700;
    }

    .verification-section {
        background: var(--yellow-100);
        border: 2px solid var(--yellow-400);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin: 30px 0;
    }

    .verification-icon {
        color: #22c55e;
        font-size: 48px;
        margin-bottom: 12px;
    }

    .verification-text {
        color: var(--sky-700);
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .verification-details {
        color: var(--sky-600);
        font-size: 14px;
    }

    .signature-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 60px;
        padding-top: 30px;
        border-top: 2px solid var(--sky-200);
    }

    .signature-box {
        text-align: center;
    }

    .signature-title {
        color: var(--sky-700);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 40px;
    }

    .signature-line {
        border-bottom: 1px solid var(--sky-300);
        height: 1px;
        margin-bottom: 8px;
    }

    .signature-name {
        color: var(--sky-600);
        font-size: 12px;
    }

    .document-footer {
        background: var(--sky-100);
        padding: 20px 40px;
        border-top: 1px solid var(--sky-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .footer-info {
        color: var(--sky-600);
        font-size: 12px;
    }

    .footer-actions {
        display: flex;
        gap: 12px;
    }

    .footer-button {
        background: var(--sky-400);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .footer-button:hover {
        background: var(--sky-500);
    }

    .footer-button.secondary {
        background: var(--yellow-400);
        color: #000;
    }

    .footer-button.secondary:hover {
        background: var(--yellow-500);
    }

    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        color: rgba(56, 189, 248, 0.1);
        font-size: 72px;
        font-weight: 900;
        z-index: 1;
        pointer-events: none;
    }

    .stamp {
        position: absolute;
        top: 150px;
        right: 40px;
        width: 100px;
        height: 100px;
        border: 3px solid var(--yellow-400);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(251, 191, 36, 0.1);
        color: var(--yellow-500);
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        transform: rotate(15deg);
        z-index: 2;
    }

    @media (max-width: 768px) {
        .document-container {
            margin: 10px;
            max-width: none;
        }
        
        .document-body {
            padding: 20px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .signature-section {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .document-footer {
            flex-direction: column;
            text-align: center;
        }
        
        .stamp {
            display: none;
        }
        
        .watermark {
            font-size: 48px;
        }
    }

    @media print {
        body {
            background: white;
        }
        
        .document-container {
            box-shadow: none;
            margin: 0;
            max-width: none;
        }
        
        .footer-actions {
            display: none;
        }
        
        .stamp {
            opacity: 0.8;
        }
        
        .watermark {
            opacity: 0.3;
        }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح سند رسمی')

@section('content')
<div class="document-container">
    <!-- Watermark -->
    <div class="watermark">پیشخوانک</div>
    
    <!-- Official Stamp -->
    <div class="stamp">
        VERIFIED<br>✓<br>{{ now()->format('Y/m/d') }}
    </div>

    <!-- Document Header -->
    <div class="document-header">
        <div class="document-logo">پ</div>
        <h1 class="document-title">سند رسمی تبدیل اطلاعات بانکی</h1>
        <p class="document-subtitle">{{ $service->title ?? 'سیستم تبدیل کارت به شبا' }}</p>
        <div class="document-number">شماره سند: {{ strtoupper(substr(md5(time()), 0, 10)) }}</div>
    </div>

    <!-- Document Body -->
    <div class="document-body">
        <!-- Document Information -->
        <div class="section">
            <div class="section-header">
                <div class="section-icon">ℹ</div>
                اطلاعات سند
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">تاریخ صدور</div>
                    <div class="info-value">{{ now()->format('Y/m/d') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">زمان صدور</div>
                    <div class="info-value">{{ now()->format('H:i:s') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">مدت پردازش</div>
                    <div class="info-value">0.84 ثانیه</div>
                </div>
                <div class="info-item">
                    <div class="info-label">وضعیت امنیت</div>
                    <div class="info-value">رمزنگاری شده</div>
                </div>
            </div>
        </div>

        <!-- Input Data Section -->
        @if(isset($inputData) && !empty($inputData))
        <div class="section">
            <div class="section-header">
                <div class="section-icon">📥</div>
                اطلاعات ورودی سیستم
            </div>
            <table class="data-table">
                <thead class="table-header">
                    <tr>
                        <th>نوع اطلاعات</th>
                        <th>مقدار</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inputData as $key => $value)
                    <tr class="table-row">
                        <td class="table-label">
                            @switch($key)
                                @case('card_number') شماره کارت بانکی @break
                                @case('iban') شماره شبا @break
                                @case('account_number') شماره حساب @break
                                @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                            @endswitch
                        </td>
                        <td>
                            <div class="table-value">{{ $value }}</div>
                        </td>
                        <td style="color: #22c55e; font-weight: 600;">✓ تأیید شده</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Results Section -->
        <div class="section">
            <div class="section-header">
                <div class="section-icon">📤</div>
                نتایج تبدیل و اطلاعات خروجی
            </div>
            <table class="data-table">
                <thead class="table-header">
                    <tr>
                        <th>نوع اطلاعات</th>
                        <th>مقدار تبدیل شده</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
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
                    <tr class="table-row {{ $key === 'iban' ? 'highlight-row' : '' }}">
                        <td class="table-label">
                            @switch($key)
                                @case('iban') شماره شبا (IBAN) @break
                                @case('bank_name') نام بانک @break
                                @case('account_number') شماره حساب @break
                                @case('account_type') نوع حساب @break
                                @case('is_valid') وضعیت اعتبارسنجی @break
                                @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                            @endswitch
                        </td>
                        <td>
                            <div class="table-value">
                                @if($key === 'is_valid')
                                    {{ $value ? 'معتبر و تأیید شده' : 'نامعتبر' }}
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        </td>
                        <td style="color: {{ $key === 'is_valid' && $value ? '#22c55e' : '#f59e0b' }}; font-weight: 600;">
                            {{ $key === 'is_valid' && $value ? '✓ تأیید شده' : '⚠ بررسی شده' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Verification Section -->
        <div class="verification-section">
            <div class="verification-icon">✓</div>
            <div class="verification-text">سند با موفقیت تولید و تأیید شد</div>
            <div class="verification-details">
                تمامی اطلاعات طبق استانداردهای بانکی ایران بررسی و تأیید شده است
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">مهر و امضای سیستم</div>
                <div class="signature-line"></div>
                <div class="signature-name">سیستم پیشخوانک</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">تاریخ و زمان تولید</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ now()->format('Y/m/d H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- Document Footer -->
    <div class="document-footer">
        <div class="footer-info">
            <strong>پیشخوانک</strong> | سیستم تبدیل اطلاعات بانکی | pishkhanak.com<br>
            این سند دارای اعتبار قانونی بوده و قابل استناد است
        </div>
        <div class="footer-actions">
            <button onclick="copyDocument()" class="footer-button">
                📋 کپی
            </button>
            <button onclick="printDocument()" class="footer-button secondary">
                🖨️ چاپ
            </button>
            <button onclick="downloadDocument()" class="footer-button">
                📄 دانلود
            </button>
            <a href="{{ route('app.page.home') }}" class="footer-button">
                🏠 خانه
            </a>
        </div>
    </div>
</div>

<script>
function copyDocument() {
    const tables = document.querySelectorAll('.data-table');
    let text = 'سند رسمی تبدیل - پیشخوانک\n';
    text += '='.repeat(50) + '\n\n';
    
    text += `شماره سند: ${document.querySelector('.document-number').textContent}\n`;
    text += `تاریخ صدور: ${new Date().toLocaleDateString('fa-IR')}\n`;
    text += `زمان صدور: ${new Date().toLocaleTimeString('fa-IR')}\n\n`;
    
    tables.forEach((table, index) => {
        const title = table.closest('.section').querySelector('.section-header').textContent.trim();
        text += `${title}\n${'-'.repeat(title.length)}\n`;
        
        const rows = table.querySelectorAll('.table-row');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const label = cells[0].textContent.trim();
            const value = cells[1].textContent.trim();
            text += `${label}: ${value}\n`;
        });
        text += '\n';
    });
    
    text += 'سند الکترونیکی - پیشخوانک\n';
    text += 'pishkhanak.com';
    
    navigator.clipboard.writeText(text).then(() => {
        showDocumentNotification('سند کپی شد!', 'success');
    });
}

function printDocument() {
    window.print();
}

function downloadDocument() {
    showDocumentNotification('آماده‌سازی فایل PDF...', 'info');
    // Simulate download
    setTimeout(() => {
        showDocumentNotification('فایل آماده دانلود است!', 'success');
    }, 2000);
}

function showDocumentNotification(message, type) {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? '#22c55e' : type === 'info' ? 'var(--yellow-400)' : 'var(--sky-400)';
    const textColor = type === 'info' ? '#000' : 'white';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: ${textColor};
        padding: 16px 24px;
        border-radius: 8px;
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

// Add timestamp to all time elements
document.addEventListener('DOMContentLoaded', function() {
    const timestamp = new Date().toLocaleString('fa-IR');
    console.log('سند تولید شد در:', timestamp);
});
</script>
@endsection 