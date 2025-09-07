<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>انتقال امن به درگاه پرداخت | {{ config('app.name', 'پیشخوانک') }}</title>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @font-face {
            font-family: 'IRANSansWeb';
            src: url('/assets/fonts/IRANSansWeb.woff2') format('woff2'),
                 url('/assets/fonts/IRANSansWeb.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'IRANSansWeb', 'Tahoma', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .container {
            width: 90%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .security-badge {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 8px 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            font-size: 13px;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        .security-badge i {
            font-size: 16px;
            color: #4ade80;
        }

        .content {
            padding: 30px;
        }

        .loading-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .loading-spinner {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            position: relative;
        }

        .spinner-circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid transparent;
            border-top-color: #667eea;
            animation: spin 1s linear infinite;
        }

        .spinner-circle:nth-child(2) {
            width: 75%;
            height: 75%;
            top: 12.5%;
            left: 12.5%;
            border-top-color: #764ba2;
            animation: spin 1.5s linear infinite reverse;
        }

        .spinner-circle:nth-child(3) {
            width: 50%;
            height: 50%;
            top: 25%;
            left: 25%;
            border-top-color: #4ade80;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            color: #4b5563;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .countdown {
            color: #9ca3af;
            font-size: 14px;
        }

        .countdown span {
            color: #667eea;
            font-weight: bold;
            font-size: 18px;
        }

        .transaction-details {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
        }

        .transaction-details h3 {
            color: #374151;
            font-size: 14px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .transaction-details h3 i {
            color: #667eea;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 13px;
        }

        .detail-value {
            color: #111827;
            font-size: 14px;
            font-weight: 500;
        }

        .amount-value {
            color: #667eea;
            font-size: 16px;
            font-weight: bold;
        }

        .trust-indicators {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .trust-item {
            text-align: center;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .trust-item:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .trust-item i {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 8px;
            display: block;
        }

        .trust-item span {
            font-size: 11px;
            color: #4b5563;
            display: block;
        }

        .manual-submit {
            text-align: center;
            margin-top: 20px;
            opacity: 0;
            animation: fadeIn 3s ease-in-out 3s forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        .manual-submit button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .manual-submit button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .manual-submit button i {
            font-size: 18px;
        }

        .info-text {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 20px;
            line-height: 1.6;
        }

        .payment-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .payment-logos img {
            height: 30px;
            opacity: 0.6;
            filter: grayscale(100%);
            transition: all 0.3s ease;
        }

        .payment-logos img:hover {
            opacity: 1;
            filter: grayscale(0%);
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .trust-indicators {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .loading-spinner {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <!-- Header -->
            <div class="header">
                <h1>درگاه امن پرداخت الکترونیک</h1>
                <p>در حال انتقال به درگاه پرداخت بانکی</p>
                <div class="security-badge">
                    <i class="fas fa-shield-check"></i>
                    <span>اتصال امن SSL</span>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Loading Section -->
                <div class="loading-section">
                    <div class="loading-spinner">
                        <div class="spinner-circle"></div>
                        <div class="spinner-circle"></div>
                        <div class="spinner-circle"></div>
                    </div>
                    <div class="loading-text">در حال برقراری ارتباط امن...</div>
                    <div class="countdown">
                        انتقال خودکار در <span id="countdown">5</span> ثانیه
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="transaction-details">
                    <h3>
                        <i class="fas fa-receipt"></i>
                        جزئیات تراکنش
                    </h3>
                    <div class="detail-row">
                        <span class="detail-label">مبلغ قابل پرداخت:</span>
                        <span class="detail-value amount-value">
                            {{ number_format($transaction->amount) }} تومان
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">درگاه پرداخت:</span>
                        <span class="detail-value">
                            @if($transaction->gateway)
                                {{ $transaction->gateway->name }}
                            @elseif(isset($gateway_data['gateway_name']))
                                {{ $gateway_data['gateway_name'] }}
                            @else
                                بانک سپه
                            @endif
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">شماره پیگیری:</span>
                        <span class="detail-value">{{ $transaction->reference_id }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">تاریخ و زمان:</span>
                        <span class="detail-value">{{ \Verta::instance($transaction->created_at)->format('Y/m/d - H:i') }}</span>
                    </div>
                </div>

                <!-- Trust Indicators -->
                <div class="trust-indicators">
                    <div class="trust-item">
                        <i class="fas fa-lock"></i>
                        <span>رمزنگاری 256 بیتی</span>
                    </div>
                    <div class="trust-item">
                        <i class="fas fa-certificate"></i>
                        <span>دارای نماد اعتماد</span>
                    </div>
                    <div class="trust-item">
                        <i class="fas fa-user-shield"></i>
                        <span>حفاظت از اطلاعات</span>
                    </div>
                </div>

                <!-- Manual Submit Button -->
                <div class="manual-submit">
                    <button type="button" onclick="submitPaymentForm()">
                        <i class="fas fa-arrow-left"></i>
                        ورود به درگاه پرداخت
                    </button>
                </div>

                <!-- Info Text -->
                <div class="info-text">
                    <p>
                        <i class="fas fa-info-circle"></i>
                        اطلاعات کارت بانکی شما به صورت مستقیم به بانک ارسال می‌شود
                        <br>
                        و در سرورهای ما ذخیره نمی‌گردد.
                    </p>
                </div>

                <!-- Payment Logos -->
                <div class="payment-logos">
                    <img src="/assets/images/banks/shetab.svg" alt="شتاب" title="عضو شبکه شتاب">
                    <img src="/assets/images/banks/sepah.svg" alt="بانک سپه" title="بانک سپه">
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Payment Form -->
    {!! $payment_form ?? '' !!}

    <script>
        // Countdown timer
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                submitPaymentForm();
            }
        }, 1000);

        // Submit payment form function
        function submitPaymentForm() {
            const form = document.querySelector('form[method="post"]');
            if (form) {
                // Show submitting state
                document.querySelector('.loading-text').textContent = 'در حال انتقال به درگاه بانک...';
                document.querySelector('.countdown').style.display = 'none';
                
                // Submit the form
                form.submit();
            }
        }

        // Manual submit on button click
        document.querySelector('.manual-submit button').addEventListener('click', function() {
            clearInterval(countdownInterval);
            submitPaymentForm();
        });
    </script>
</body>
</html> 