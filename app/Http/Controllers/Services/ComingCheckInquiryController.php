<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;

class ComingCheckInquiryController extends BaseFinnotechController
{
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
            'mobile.required' => 'PERSIAN_TEXT_3d192440',
            'mobile.regex' => 'PERSIAN_TEXT_b491c86a',
            'national_code.required' => 'PERSIAN_TEXT_9f012346',
            'national_code.digits' => 'PERSIAN_TEXT_3d75e880',
            'account_number.min' => 'PERSIAN_TEXT_f862b85c',
            'bank_code.size' => 'PERSIAN_TEXT_ff165121',
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'PERSIAN_TEXT_63be00c7'];
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
                'formatted_amount' => number_format($check['amount'] ?? 0) . 'PERSIAN_TEXT_56f734e6',
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
            'formatted_total_amount' => number_format($totalAmount) . 'PERSIAN_TEXT_56f734e6',
            'pending_checks' => $statusCounts['PENDING'],
            'paid_checks' => $statusCounts['PAID'],
            'bounced_checks' => $statusCounts['BOUNCED'],
            'cancelled_checks' => $statusCounts['CANCELLED'],
            'overdue_checks' => count(array_filter($checks, fn($c) => $this->isOverdue($c['dueDate'] ?? ''))),
            'due_this_week' => count(array_filter($checks, fn($c) => $this->isDueThisWeek($c['dueDate'] ?? ''))),
            'average_amount' => count($checks) > 0 ? round($totalAmount / count($checks)) : 0,
            'formatted_average_amount' => count($checks) > 0 ? number_format(round($totalAmount / count($checks))) . 'PERSIAN_TEXT_56f734e6' : 'PERSIAN_TEXT_30455ba4',
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
            'formatted_current_balance' => number_format($accountInfo['currentBalance'] ?? 0) . 'PERSIAN_TEXT_56f734e6',
            'available_balance' => $accountInfo['availableBalance'] ?? 0,
            'formatted_available_balance' => number_format($accountInfo['availableBalance'] ?? 0) . 'PERSIAN_TEXT_56f734e6',
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
            'PENDING' => 'PERSIAN_TEXT_5bd8e6bd',
            'PAID' => 'PERSIAN_TEXT_c31607de',
            'BOUNCED' => 'PERSIAN_TEXT_50aa7ded',
            'CANCELLED' => 'PERSIAN_TEXT_df91c21d',
            'STOPPED' => 'PERSIAN_TEXT_0bfd0745',
            'EXPIRED' => 'PERSIAN_TEXT_1ec03633',
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
                'title' => 'PERSIAN_TEXT_d1243070',
                'message' => count($bouncedChecks) . 'PERSIAN_TEXT_20b8c3b5'
            ];
        }

        // Check for overdue checks
        $overdueChecks = array_filter($checks, fn($c) => $this->isOverdue($c['dueDate'] ?? ''));
        if (!empty($overdueChecks)) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'PERSIAN_TEXT_0b73315e',
                'message' => count($overdueChecks) . 'PERSIAN_TEXT_62ac84b7'
            ];
        }

        // Check for due this week
        $dueThisWeek = array_filter($checks, fn($c) => $this->isDueThisWeek($c['dueDate'] ?? ''));
        if (!empty($dueThisWeek)) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'PERSIAN_TEXT_229e57f0',
                'message' => count($dueThisWeek) . 'PERSIAN_TEXT_ad4feda0'
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
                'title' => 'PERSIAN_TEXT_65ffbc11',
                'message' => 'PERSIAN_TEXT_a4b4d700' . number_format($shortage) . 'PERSIAN_TEXT_d6c9537d'
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
            $recommendations[] = 'PERSIAN_TEXT_b0399e1d';
            $recommendations[] = 'PERSIAN_TEXT_a3da5bf6';
        }

        $overdueCount = count(array_filter($checks, fn($c) => $this->isOverdue($c['dueDate'] ?? '')));
        if ($overdueCount > 0) {
            $recommendations[] = 'PERSIAN_TEXT_c596f6a1';
            $recommendations[] = 'PERSIAN_TEXT_c4b83bf0';
        }

        $currentBalance = $accountInfo['currentBalance'] ?? 0;
        $pendingAmount = array_sum(array_column(
            array_filter($checks, fn($c) => $c['status'] === 'PENDING'), 
            'amount'
        ));

        if ($currentBalance < $pendingAmount) {
            $recommendations[] = 'PERSIAN_TEXT_f761acf9';
            $recommendations[] = 'PERSIAN_TEXT_4af9df90';
        }

        $dueThisWeekCount = count(array_filter($checks, fn($c) => $this->isDueThisWeek($c['dueDate'] ?? '')));
        if ($dueThisWeekCount > 0) {
            $recommendations[] = 'PERSIAN_TEXT_489f7c54';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'PERSIAN_TEXT_04dad234';
            $recommendations[] = 'PERSIAN_TEXT_a8c43142';
        }

        return $recommendations;
    }
} 