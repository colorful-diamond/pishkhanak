@extends('front.layouts.app')

@push('styles')
<style>
    :root {
        --sky-50: #f0f9ff;
        --sky-100: #e0f2fe;
        --sky-200: #bae6fd;
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

    .result-container {
        max-width: 768px;
        margin: 0 auto;
        padding: 16px;
    }

    .result-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.1);
        margin-bottom: 16px;
        overflow: hidden;
    }

    .card-header {
        background: var(--sky-400);
        color: white;
        padding: 20px;
        text-align: center;
    }

    .card-header h1 {
        margin: 0 0 8px 0;
        font-size: 20px;
        font-weight: 700;
    }

    .success-badge {
        background: var(--yellow-400);
        color: #000;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
    }

    .card-content {
        padding: 20px;
    }

    .data-group {
        margin-bottom: 24px;
    }

    .group-title {
        color: var(--sky-700);
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 12px;
        border-bottom: 2px solid var(--sky-100);
        padding-bottom: 8px;
    }

    .data-item {
        background: var(--sky-50);
        border: 1px solid var(--sky-200);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .data-label {
        color: var(--sky-600);
        font-size: 14px;
        font-weight: 500;
    }

    .data-value {
        background: var(--yellow-100);
        color: #000;
        padding: 4px 8px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 14px;
        font-weight: 600;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 20px;
    }

    .action-btn {
        background: var(--sky-400);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
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

    .footer-info {
        text-align: center;
        background: var(--sky-100);
        padding: 16px;
        color: var(--sky-700);
        font-size: 14px;
    }

    @media (max-width: 480px) {
        .result-container {
            padding: 8px;
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
        
        .data-item {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        
        .data-value {
            text-align: center;
        }
    }
</style>
@endpush

@section('title', 'نتیجه تبدیل - طرح کارت‌های ساده')

@section('content')
<div class="result-container">
    <!-- Main Result Card -->
    <div class="result-card">
        <div class="card-header">
            <div class="success-badge">✓ موفق</div>
            <h1>{{ $service->title ?? 'تبدیل کارت به شبا' }}</h1>
            <p style="margin: 0; opacity: 0.9; font-size: 14px;">نتیجه عملیات شما آماده است</p>
        </div>

        <div class="card-content">
            <!-- Input Data -->
            @if(isset($inputData) && !empty($inputData))
            <div class="data-group">
                <div class="group-title">اطلاعات ورودی</div>
                @foreach($inputData as $key => $value)
                <div class="data-item">
                    <span class="data-label">
                        @switch($key)
                            @case('card_number') شماره کارت @break
                            @case('iban') شماره شبا @break
                            @case('account_number') شماره حساب @break
                            @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </span>
                    <span class="data-value">{{ $value }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Results -->
            <div class="data-group">
                <div class="group-title">نتایج تبدیل</div>
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
                    <span class="data-label">
                        @switch($key)
                            @case('iban') شماره شبا @break
                            @case('bank_name') نام بانک @break
                            @case('account_number') شماره حساب @break
                            @case('account_type') نوع حساب @break
                            @case('is_valid') وضعیت اعتبار @break
                            @default {{ ucfirst(str_replace('_', ' ', $key)) }}
                        @endswitch
                    </span>
                    <span class="data-value">
                        @if($key === 'is_valid')
                            {{ $value ? '✓ معتبر' : '✗ نامعتبر' }}
                        @else
                            {{ $value }}
                        @endif
                    </span>
                </div>
                @endforeach
            </div>

            <!-- Actions -->
            <div class="actions-grid">
                <button onclick="copyResults()" class="action-btn">کپی نتایج</button>
                <a href="#" class="action-btn secondary">تبدیل دوباره</a>
                <button onclick="shareResults()" class="action-btn">اشتراک‌گذاری</button>
                <a href="{{ route('app.page.home') }}" class="action-btn secondary">صفحه اصلی</a>
            </div>
        </div>

        <div class="footer-info">
            <strong>پیشخوانک</strong> - pishkhanak.com<br>
            ارائه‌دهنده خدمات استعلام بانکی
        </div>
    </div>
</div>

<script>
function copyResults() {
    const results = document.querySelectorAll('.data-value');
    let text = '';
    results.forEach(result => {
        text += result.textContent + '\n';
    });
    navigator.clipboard.writeText(text).then(() => {
        alert('نتایج کپی شد!');
    });
}

function shareResults() {
    if (navigator.share) {
        navigator.share({
            title: 'نتیجه تبدیل - پیشخوانک',
            text: 'نتیجه تبدیل من در پیشخوانک',
            url: window.location.href
        });
    } else {
        copyResults();
    }
}
</script>
@endsection 