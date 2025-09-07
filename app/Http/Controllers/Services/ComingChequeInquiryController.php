<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class ComingChequeInquiryController extends BaseFinnotechController
{
    /**
     * Constructor
     */
    public function __construct(FinnotechService $finnotechService, SmsAuthorizationService $smsAuthService)
    {
        parent::__construct($finnotechService, $smsAuthService);
        $this->configureService();
    }

    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        $this->apiEndpoint = 'coming-check-inquiry';
        $this->scope = 'banking:coming-check:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['mobile', 'national_code'];
        
        $this->validationRules = [
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'national_code' => 'required|string|digits:10',
            'account_number' => 'nullable|string|min:10|max:20',
            'bank_code' => 'nullable|string|size:3',
        ];
        
        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است',
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'account_number.min' => 'شماره حساب باید حداقل 10 رقم باشد',
            'bank_code.size' => 'کد بانک باید 3 رقم باشد',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        $params = [
            'mobile' => $serviceData['mobile'] ?? '',
            'nationalCode' => $serviceData['national_code'] ?? '',
        ];

        // Add optional account number and bank code if provided
        if (!empty($serviceData['account_number'])) {
            $params['accountNumber'] = $serviceData['account_number'];
        }

        if (!empty($serviceData['bank_code'])) {
            $params['bankCode'] = $serviceData['bank_code'];
        }

        return $params;
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات چک‌های صادرشده'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'issued_checks' => $this->processIssuedChecks($result['issuedChecks'] ?? []),
                'summary' => $this->getChecksSummary($result),
                'account_info' => $this->getAccountInfo($result),
                'alerts' => $this->getAlerts($result),
                'recommendations' => $this->getRecommendations($result),
            ]
        ];
    }

    /**
     * Process issued checks
     */
    private function processIssuedChecks(array $issuedChecks): array
    {
        $processed = [];
        
        foreach ($issuedChecks as $check) {
            $processed[] = [
                'check_number' => $check['checkNumber'] ?? '',
                'bank_name' => $check['bankName'] ?? '',
                'branch_name' => $check['branchName'] ?? '',
                'account_number' => $check['accountNumber'] ?? '',
                'amount' => $check['amount'] ?? 0,
                'formatted_amount' => number_format($check['amount'] ?? 0) . ' ریال',
                'issue_date' => $check['issueDate'] ?? '',
                'due_date' => $check['dueDate'] ?? '',
                'payee_name' => $check['payeeName'] ?? '',
                'status' => $check['status'] ?? '',
                'status_persian' => $this->translateCheckStatus($check['status'] ?? ''),
                'payment_date' => $check['paymentDate'] ?? null,
                'bounce_reason' => $check['bounceReason'] ?? null,
                'bounce_code' => $check['bounceCode'] ?? null,
                'is_overdue' => $this->isOverdue($check['dueDate'] ?? ''),
                'days_to_due' => $this->getDaysToDue($check['dueDate'] ?? ''),
                'can_stop_payment' => $this->canStopPayment($check),
            ];
        }

        return array_reverse($processed); // Show newest first
    }

    /**
     * Get checks summary
     */
    private function getChecksSummary(array $result): array
    {
        $checks = $result['issuedChecks'] ?? [];
        $totalAmount = array_sum(array_column($checks, 'amount'));
        
        $statusCounts = [
            'PENDING' => 0,
            'PAID' => 0,
            'BOUNCED' => 0,
            'CANCELLED' => 0,
        ];

        foreach ($checks as $check) {
            $status = $check['status'] ?? 'UNKNOWN';
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }
        }

        return [
            'total_checks' => count($checks),
            'total_amount' => $totalAmount,
            'formatted_total_amount' => number_format($totalAmount) . ' ریال',
            'pending_checks' => $statusCounts['PENDING'],
            'paid_checks' => $statusCounts['PAID'],
            'bounced_checks' => $statusCounts['BOUNCED'],
            'cancelled_checks' => $statusCounts['CANCELLED'],
            'overdue_checks' => count(array_filter($checks, fn($c) => $this->isOverdue($c['dueDate'] ?? ''))),
            'due_this_week' => count(array_filter($checks, fn($c) => $this->isDueThisWeek($c['dueDate'] ?? ''))),
            'average_amount' => count($checks) > 0 ? round($totalAmount / count($checks)) : 0,
            'formatted_average_amount' => count($checks) > 0 ? number_format(round($totalAmount / count($checks))) . ' ریال' : '0 ریال',
        ];
    }

    /**
     * Get account information
     */
    private function getAccountInfo(array $result): array
    {
        $accountInfo = $result['accountInfo'] ?? [];
        
        return [
            'account_number' => $accountInfo['accountNumber'] ?? '',
            'bank_name' => $accountInfo['bankName'] ?? '',
            'branch_name' => $accountInfo['branchName'] ?? '',
            'account_holder' => $accountInfo['accountHolder'] ?? '',
            'account_status' => $accountInfo['accountStatus'] ?? '',
            'current_balance' => $accountInfo['currentBalance'] ?? 0,
            'formatted_current_balance' => number_format($accountInfo['currentBalance'] ?? 0) . ' ریال',
            'available_balance' => $accountInfo['availableBalance'] ?? 0,
            'formatted_available_balance' => number_format($accountInfo['availableBalance'] ?? 0) . ' ریال',
            'check_book_status' => $accountInfo['checkBookStatus'] ?? '',
            'last_check_number' => $accountInfo['lastCheckNumber'] ?? '',
        ];
    }

    /**
     * Translate check status
     */
    private function translateCheckStatus(string $status): string
    {
        $statuses = [
            'PENDING' => 'در انتظار پرداخت',
            'PAID' => 'پرداخت شده',
            'BOUNCED' => 'برگشت خورده',
            'CANCELLED' => 'لغو شده',
            'STOPPED' => 'متوقف شده',
            'EXPIRED' => 'منقضی شده',
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Check if check is overdue
     */
    private function isOverdue(string $dueDate): bool
    {
        if (empty($dueDate)) return false;
        return strtotime($dueDate) < time();
    }

    /**
     * Get days to due date
     */
    private function getDaysToDue(string $dueDate): int
    {
        if (empty($dueDate)) return 0;
        return floor((strtotime($dueDate) - time()) / 86400);
    }

    /**
     * Check if due this week
     */
    private function isDueThisWeek(string $dueDate): bool
    {
        if (empty($dueDate)) return false;
        $daysToDue = $this->getDaysToDue($dueDate);
        return $daysToDue >= 0 && $daysToDue <= 7;
    }

    /**
     * Check if payment can be stopped
     */
    private function canStopPayment(array $check): bool
    {
        $status = $check['status'] ?? '';
        $dueDate = $check['dueDate'] ?? '';
        
        // Can stop if pending and not yet due
        return $status === 'PENDING' && !$this->isOverdue($dueDate);
    }

    /**
     * Get alerts and warnings
     */
    private function getAlerts(array $result): array
    {
        $alerts = [];
        $checks = $result['issuedChecks'] ?? [];
        $accountInfo = $result['accountInfo'] ?? [];

        // Check for bounced checks
        $bouncedChecks = array_filter($checks, fn($c) => $c['status'] === 'BOUNCED');
        if (!empty($bouncedChecks)) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'چک‌های برگشتی',
                'message' => count($bouncedChecks) . ' چک برگشت خورده است'
            ];
        }

        // Check for overdue checks
        $overdueChecks = array_filter($checks, fn($c) => $this->isOverdue($c['dueDate'] ?? ''));
        if (!empty($overdueChecks)) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'چک‌های سررسید گذشته',
                'message' => count($overdueChecks) . ' چک سررسید گذشته دارد'
            ];
        }

        // Check for due this week
        $dueThisWeek = array_filter($checks, fn($c) => $this->isDueThisWeek($c['dueDate'] ?? ''));
        if (!empty($dueThisWeek)) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'چک‌های نزدیک به سررسید',
                'message' => count($dueThisWeek) . ' چک تا یک هفته دیگر سررسید می‌شود'
            ];
        }

        // Check account balance
        $currentBalance = $accountInfo['currentBalance'] ?? 0;
        $pendingAmount = array_sum(array_column(
            array_filter($checks, fn($c) => $c['status'] === 'PENDING'), 
            'amount'
        ));

        if ($currentBalance < $pendingAmount) {
            $shortage = $pendingAmount - $currentBalance;
            $alerts[] = [
                'type' => 'warning',
                'title' => 'کسری موجودی',
                'message' => 'موجودی حساب ' . number_format($shortage) . ' ریال کمتر از چک‌های صادرشده است'
            ];
        }

        return $alerts;
    }

    /**
     * Get recommendations
     */
    private function getRecommendations(array $result): array
    {
        $recommendations = [];
        $checks = $result['issuedChecks'] ?? [];
        $accountInfo = $result['accountInfo'] ?? [];

        $bouncedCount = count(array_filter($checks, fn($c) => $c['status'] === 'BOUNCED'));
        if ($bouncedCount > 0) {
            $recommendations[] = 'فوراً چک‌های برگشتی را پیگیری و تسویه کنید';
            $recommendations[] = 'برای جلوگیری از مشکلات قانونی اقدام کنید';
        }

        $overdueCount = count(array_filter($checks, fn($c) => $this->isOverdue($c['dueDate'] ?? '')));
        if ($overdueCount > 0) {
            $recommendations[] = 'چک‌های سررسید گذشته را بررسی کنید';
            $recommendations[] = 'با دریافت‌کنندگان چک تماس بگیرید';
        }

        $currentBalance = $accountInfo['currentBalance'] ?? 0;
        $pendingAmount = array_sum(array_column(
            array_filter($checks, fn($c) => $c['status'] === 'PENDING'), 
            'amount'
        ));

        if ($currentBalance < $pendingAmount) {
            $recommendations[] = 'موجودی حساب را افزایش دهید';
            $recommendations[] = 'در صورت نیاز پاره‌ای از چک‌ها را متوقف کنید';
        }

        $dueThisWeekCount = count(array_filter($checks, fn($c) => $this->isDueThisWeek($c['dueDate'] ?? '')));
        if ($dueThisWeekCount > 0) {
            $recommendations[] = 'برای چک‌های نزدیک به سررسید آماده باشید';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'وضعیت چک‌های شما مناسب است';
            $recommendations[] = 'به نظارت بر موجودی حساب ادامه دهید';
        }

        return $recommendations;
    }
} 