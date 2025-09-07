<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>انتقال به درگاه پرداخت سامان</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="در حال انتقال به درگاه پرداخت امن سامان">
    <meta name="author" content="{{ config('app.name', 'پیشخوانک') }}">
    
    <!-- Security Headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' 'unsafe-inline' sep.shaparak.ir">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Vazirmatn', 'IRANSans', 'Tahoma', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            direction: rtl;
        }

        .payment-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .payment-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #2c5aa0, #1e3f73, #2c5aa0);
        }

        .gateway-logo {
            margin-bottom: 30px;
        }

        .gateway-logo h1 {
            color: #2c5aa0;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .gateway-logo p {
            color: #666;
            font-size: 14px;
            font-weight: 400;
        }

        .payment-info {
            background: #f8fafc;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }

        .amount-display {
            font-size: 32px;
            color: #2c5aa0;
            font-weight: 700;
            margin-bottom: 15px;
            direction: ltr;
            unicode-bidi: embed;
        }

        .payment-description {
            color: #4a5568;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .transaction-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
            font-size: 14px;
        }

        .detail-item {
            background: white;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .detail-label {
            color: #718096;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #2d3748;
            font-weight: 600;
        }

        .loading-section {
            margin: 30px 0;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2c5aa0;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            color: #4a5568;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            margin: 20px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #2c5aa0, #667eea);
            width: 0%;
            animation: progress 3s ease-in-out forwards;
        }

        @keyframes progress {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }

        .manual-redirect {
            margin-top: 30px;
            padding: 20px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 12px;
            display: none;
        }

        .manual-redirect.show {
            display: block;
        }

        .redirect-button {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3f73 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(44, 90, 160, 0.3);
        }

        .redirect-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(44, 90, 160, 0.4);
        }

        .security-info {
            margin-top: 30px;
            padding: 20px;
            background: #f0fff4;
            border: 1px solid #9ae6b4;
            border-radius: 12px;
            font-size: 14px;
        }

        .security-icon {
            color: #38a169;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .countdown {
            font-size: 18px;
            color: #2c5aa0;
            font-weight: 600;
            margin-top: 15px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .payment-container {
                padding: 25px;
                margin: 10px;
            }

            .gateway-logo h1 {
                font-size: 24px;
            }

            .amount-display {
                font-size: 28px;
            }

            .transaction-details {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .redirect-button {
                padding: 12px 25px;
                font-size: 15px;
            }
        }

        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .spinner {
                animation: none;
            }
            
            .progress-fill {
                animation: none;
                width: 100%;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            }

            .payment-container {
                background: #2d3748;
                color: white;
            }

            .payment-info {
                background: #4a5568;
                border-color: #718096;
            }

            .detail-item {
                background: #4a5568;
                border-color: #718096;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <!-- Gateway Logo -->
        <div class="gateway-logo">
            <h1>درگاه پرداخت سامان</h1>
            <p>پرداخت امن و مطمئن</p>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <div class="amount-display">
                {{ number_format($transaction->total_amount ?? 0) }} تومان
            </div>
            
            <div class="payment-description">
                {{ $transaction->description ?? 'پرداخت آنلاین' }}
            </div>

            <!-- Transaction Details -->
            <div class="transaction-details">
                <div class="detail-item">
                    <div class="detail-label">شماره تراکنش</div>
                    <div class="detail-value">{{ $transaction->uuid ?? 'نامشخص' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">درگاه پرداخت</div>
                    <div class="detail-value">سامان الکترونیک</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">تاریخ</div>
                    <div class="detail-value">{{ now()->format('Y/m/d') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">زمان</div>
                    <div class="detail-value">{{ now()->format('H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Loading Section -->
        <div class="loading-section">
            <div class="spinner"></div>
            <div class="loading-text">در حال انتقال به درگاه پرداخت...</div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <div class="countdown" id="countdown">
                انتقال در <span id="timer">3</span> ثانیه...
            </div>
        </div>

        <!-- Manual Redirect (Fallback) -->
        <div class="manual-redirect" id="manual-redirect">
            <p style="margin-bottom: 15px; color: #d69e2e;">
                اگر به صورت خودکار منتقل نشدید، روی دکمه زیر کلیک کنید:
            </p>
            <a href="{{ $paymentUrl ?? '#' }}" class="redirect-button">
                ادامه پرداخت
            </a>
        </div>

        <!-- Security Information -->
        <div class="security-info">
            <div class="security-icon">🔒</div>
            <div>
                <strong>اطمینان خاطر:</strong>
                تمامی اطلاعات شما با استفاده از پروتکل امنیتی SSL رمزگذاری شده و در محیطی کاملاً امن پردازش می‌شود.
            </div>
        </div>
    </div>

    <!-- JavaScript for Auto-Redirect and Enhanced UX -->
    <script>
        (function() {
            'use strict';
            
            // Configuration
            const PAYMENT_URL = @json($paymentUrl ?? '');
            const REDIRECT_DELAY = {{ $redirectDelay ?? 3000 }};
            const SHOW_MANUAL_AFTER = 10000; // Show manual redirect after 10 seconds
            
            // Countdown timer
            let countdown = Math.ceil(REDIRECT_DELAY / 1000);
            const timerElement = document.getElementById('timer');
            const countdownElement = document.getElementById('countdown');
            
            // Update countdown every second
            const countdownInterval = setInterval(() => {
                countdown--;
                if (timerElement) {
                    timerElement.textContent = countdown;
                }
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    if (countdownElement) {
                        countdownElement.textContent = 'در حال انتقال...';
                    }
                }
            }, 1000);

            // Auto-redirect function
            function performRedirect() {
                if (!PAYMENT_URL || PAYMENT_URL === '#') {
                    console.error('Payment URL not provided');
                    showManualRedirect();
                    return;
                }

                try {
                    // Log redirect attempt
                    console.log('Redirecting to SEP gateway:', PAYMENT_URL);
                    
                    // Perform redirect
                    window.location.href = PAYMENT_URL;
                } catch (error) {
                    console.error('Redirect failed:', error);
                    showManualRedirect();
                }
            }

            // Show manual redirect option
            function showManualRedirect() {
                const manualRedirect = document.getElementById('manual-redirect');
                if (manualRedirect) {
                    manualRedirect.classList.add('show');
                }
                
                // Update loading text
                const loadingText = document.querySelector('.loading-text');
                if (loadingText) {
                    loadingText.textContent = 'مشکلی در انتقال خودکار رخ داد';
                }
            }

            // Perform auto-redirect after delay
            setTimeout(performRedirect, REDIRECT_DELAY);
            
            // Show manual redirect as fallback
            setTimeout(showManualRedirect, SHOW_MANUAL_AFTER);

            // Handle page visibility change (user switched tabs)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    console.log('Page hidden, user may have been redirected');
                } else {
                    console.log('Page visible again, redirect may have failed');
                    setTimeout(showManualRedirect, 2000);
                }
            });

            // Handle beforeunload (redirect is happening)
            window.addEventListener('beforeunload', function() {
                console.log('Page unloading, redirect in progress');
            });

            // Prevent back button during redirect
            history.pushState(null, null, location.href);
            window.addEventListener('popstate', function(event) {
                history.pushState(null, null, location.href);
            });

            // Analytics tracking (if available)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'sep_payment_redirect', {
                    'event_category': 'payment',
                    'event_label': 'sep_gateway',
                    'value': {{ $transaction->total_amount ?? 0 }}
                });
            }

            // Enhanced error handling
            window.addEventListener('error', function(event) {
                console.error('JavaScript error during redirect:', event.error);
                showManualRedirect();
            });

            // Accessibility improvements
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                // Reduce animations for users who prefer reduced motion
                const style = document.createElement('style');
                style.textContent = `
                    .spinner { animation-duration: 3s; }
                    .progress-fill { animation-duration: 1s; }
                `;
                document.head.appendChild(style);
            }

            // Debug information (only in development)
            @if(config('app.debug'))
            console.log('SEP Payment Redirect Debug Info:', {
                paymentUrl: PAYMENT_URL,
                redirectDelay: REDIRECT_DELAY,
                transactionUuid: @json($transaction->uuid ?? null),
                transactionAmount: {{ $transaction->total_amount ?? 0 }},
                timestamp: new Date().toISOString()
            });
            @endif

        })();
    </script>

    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "PaymentService",
        "name": "درگاه پرداخت سامان",
        "provider": {
            "@type": "Organization",
            "name": "سامان الکترونیک"
        },
        "serviceType": "Online Payment Processing"
    }
    </script>
</body>
</html> 