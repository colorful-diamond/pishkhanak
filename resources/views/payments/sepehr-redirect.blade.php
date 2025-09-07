<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انتقال به درگاه پرداخت سپهر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .payment-card {
            max-width: 500px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            background: white;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .card-body {
            padding: 2rem;
        }
        .amount-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            background: #ecf0f1;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .countdown {
            font-size: 1.2rem;
            color: #e74c3c;
            margin: 1rem 0;
        }
        .sepehr-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        .btn-pay {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .loading-spinner {
            display: none;
        }
        .loading-spinner.show {
            display: inline-block;
        }
        @media (max-width: 576px) {
            .payment-card {
                margin: 1rem;
                border-radius: 10px;
            }
            .card-header, .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="card-header">
            <h3><i class="fas fa-credit-card me-2"></i>درگاه پرداخت سپهر</h3>
            <p class="mb-0">پرداخت امن و مطمئن</p>
        </div>
        
        <div class="card-body text-center">
            @if(isset($transaction))
            <div class="amount-display text-center">
                <i class="fas fa-money-bill-wave me-2"></i>
                مبلغ قابل پرداخت: {{ number_format($transaction->total_amount) }} تومان
            </div>
            
            <div class="mb-3">
                <small class="text-muted">
                    <i class="fas fa-receipt me-1"></i>
                    کد پیگیری: {{ $transaction->reference_id }}
                </small>
            </div>

            <div class="mb-3">
                <p class="text-muted">{{ $transaction->description }}</p>
            </div>
            @endif

            <div class="countdown" id="countdown">
                <i class="fas fa-clock me-1"></i>
                انتقال خودکار در <span id="timer">5</span> ثانیه...
            </div>

            <!-- Sepehr Payment Form -->
            <form method="post" action="{{ $paymentUrl }}" id="sepehr-payment-form">
                <input type="hidden" name="TerminalID" value="{{ $terminalId }}" />
                <input type="hidden" name="token" value="{{ $accessToken }}" />
                
                @if(isset($nationalCode))
                <input type="hidden" name="nationalCode" value="{{ $nationalCode }}" />
                @endif
                
                @if(isset($getMethod) && $getMethod)
                <input type="hidden" name="getMethod" value="1" />
                @endif
                
                <button type="submit" class="btn btn-pay btn-lg w-100" id="pay-button">
                    <i class="fas fa-shield-alt me-2"></i>
                    ورود به درگاه پرداخت
                </button>
            </form>

            <div class="loading-spinner mt-3" id="loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">در حال بارگذاری...</span>
                </div>
                <p class="mt-2 text-muted">در حال انتقال به درگاه پرداخت...</p>
            </div>

            <div class="sepehr-info">
                <div class="row text-center">
                    <div class="col-4">
                        <i class="fas fa-lock text-success mb-2"></i>
                        <small class="d-block">پرداخت امن</small>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-clock text-info mb-2"></i>
                        <small class="d-block">سریع و آسان</small>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-check-circle text-primary mb-2"></i>
                        <small class="d-block">تایید شده</small>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        درگاه پرداخت سپهر الکترونیک
                    </small>
                </div>
            </div>

            @if(isset($transaction) && isset($transaction->metadata['transaction_type']))
            <div class="mt-3">
                <small class="text-info">
                    @switch($transaction->metadata['transaction_type'])
                        @case('bill_payment')
                            <i class="fas fa-file-invoice me-1"></i>پرداخت قبض
                            @break
                        @case('mobile_topup')
                            <i class="fas fa-mobile-alt me-1"></i>شارژ موبایل
                            @break
                        @case('identified_purchase')
                            <i class="fas fa-id-card me-1"></i>پرداخت شناسه‌دار
                            @break
                        @case('fund_splitting')
                            <i class="fas fa-share-alt me-1"></i>پرداخت تسهیم
                            @break
                        @default
                            <i class="fas fa-shopping-cart me-1"></i>پرداخت عادی
                    @endswitch
                </small>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('sepehr-payment-form');
            const payButton = document.getElementById('pay-button');
            const loading = document.getElementById('loading');
            const countdown = document.getElementById('countdown');
            const timerElement = document.getElementById('timer');
            
            let timeLeft = 5;

            // Countdown timer
            const countdownInterval = setInterval(function() {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    countdown.style.display = 'none';
                    submitForm();
                }
            }, 1000);

            // Form submission
            function submitForm() {
                payButton.style.display = 'none';
                loading.classList.add('show');
                countdown.style.display = 'none';
                
                // Add a small delay for better UX
                setTimeout(function() {
                    form.submit();
                }, 500);
            }

            // Manual form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearInterval(countdownInterval);
                submitForm();
            });

            // Handle page visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Page is hidden (user switched tabs/minimized)
                    clearInterval(countdownInterval);
                } else {
                    // Page is visible again
                    if (timeLeft > 0) {
                        // Resume countdown if time is left
                        countdownInterval = setInterval(function() {
                            timeLeft--;
                            timerElement.textContent = timeLeft;
                            
                            if (timeLeft <= 0) {
                                clearInterval(countdownInterval);
                                countdown.style.display = 'none';
                                submitForm();
                            }
                        }, 1000);
                    }
                }
            });
        });
    </script>
</body>
</html> 