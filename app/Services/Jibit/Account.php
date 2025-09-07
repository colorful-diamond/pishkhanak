<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Account
 * 
 * Handles Jibit Account & Reporting Services including account balance
 * and daily consumption report.
 */
class Account
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Account constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    /**
     * Retrieve the current balance of your wallets.
     *
     * @return object|null
     */
    public function getAccountBalance(): ?object
    {
        try {
            return $this->jibitService->makeApiRequest('/v1/balances');
        } catch (Exception $e) {
            Log::error('Error getting account balance: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve a daily usage report for each service.
     *
     * @param string $yearMonthDay The desired date in yyMMdd format (e.g., 000708 for the 8th of Mehr, 1400)
     * @return object|null
     */
    public function getDailyConsumptionReport(string $yearMonthDay): ?object
    {
        try {
            $params = ['yearMonthDay' => $yearMonthDay];
            return $this->jibitService->makeApiRequest('/v1/reports/daily', $params);
        } catch (Exception $e) {
            Log::error('Error getting daily consumption report: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get balance information with formatted output.
     *
     * @return array
     */
    public function getFormattedBalance(): array
    {
        $result = [
            'balances' => [],
            'total_prepaid' => 0,
            'total_postpaid' => 0,
            'error' => null
        ];

        try {
            $balanceResponse = $this->getAccountBalance();
            
            if ($balanceResponse && isset($balanceResponse->balances)) {
                foreach ($balanceResponse->balances as $balance) {
                    $formattedBalance = [
                        'type' => $balance->balanceType ?? 'Unknown',
                        'currency' => $balance->currency ?? 'IRT',
                        'amount' => $balance->amount ?? 0,
                        'formatted_amount' => number_format($balance->amount ?? 0),
                        'description' => $this->getBalanceTypeDescription($balance->balanceType ?? '')
                    ];

                    $result['balances'][] = $formattedBalance;

                    // Sum totals by type
                    if (($balance->balanceType ?? '') === 'PRF') {
                        $result['total_prepaid'] += $balance->amount ?? 0;
                    } elseif (($balance->balanceType ?? '') === 'POF') {
                        $result['total_postpaid'] += $balance->amount ?? 0;
                    }
                }

                $result['total_prepaid_formatted'] = number_format($result['total_prepaid']);
                $result['total_postpaid_formatted'] = number_format($result['total_postpaid']);
            } else {
                $result['error'] = 'Failed to retrieve balance information';
            }
        } catch (Exception $e) {
            $result['error'] = 'Error getting formatted balance: ' . $e->getMessage();
            Log::error('Error in getFormattedBalance: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Get daily report with formatted output.
     *
     * @param string $yearMonthDay The desired date in yyMMdd format
     * @return array
     */
    public function getFormattedDailyReport(string $yearMonthDay): array
    {
        $result = [
            'date' => $yearMonthDay,
            'services' => [],
            'total_calls' => 0,
            'error' => null
        ];

        try {
            $reportResponse = $this->getDailyConsumptionReport($yearMonthDay);
            
            if ($reportResponse && isset($reportResponse->report)) {
                foreach ($reportResponse->report as $reportItem) {
                    if (isset($reportItem->services)) {
                        foreach ($reportItem->services as $service) {
                            $serviceInfo = [
                                'service' => $service->service ?? 'Unknown',
                                'persian_name' => $service->persianName ?? '',
                                'count' => $service->count ?? 0,
                                'formatted_count' => number_format($service->count ?? 0)
                            ];

                            $result['services'][] = $serviceInfo;
                            $result['total_calls'] += $service->count ?? 0;
                        }
                    }
                }

                $result['total_calls_formatted'] = number_format($result['total_calls']);
            } else {
                $result['error'] = 'Failed to retrieve daily report';
            }
        } catch (Exception $e) {
            $result['error'] = 'Error getting formatted daily report: ' . $e->getMessage();
            Log::error('Error in getFormattedDailyReport: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Get description for balance type.
     *
     * @param string $balanceType
     * @return string
     */
    private function getBalanceTypeDescription(string $balanceType): string
    {
        switch ($balanceType) {
            case 'PRF':
                return 'Prepaid Wallet';
            case 'POF':
                return 'Postpaid Wallet';
            default:
                return 'Unknown Wallet Type';
        }
    }

    /**
     * Validate date format for daily reports (yyMMdd).
     *
     * @param string $yearMonthDay
     * @return bool
     */
    public function validateDateFormat(string $yearMonthDay): bool
    {
        return preg_match('/^[0-9]{6}$/', $yearMonthDay);
    }

    /**
     * Get multiple days reports.
     *
     * @param array $dates Array of dates in yyMMdd format
     * @return array
     */
    public function getMultipleDaysReports(array $dates): array
    {
        $result = [
            'reports' => [],
            'errors' => []
        ];

        foreach ($dates as $date) {
            if (!$this->validateDateFormat($date)) {
                $result['errors'][] = "Invalid date format for: {$date}";
                continue;
            }

            $reportData = $this->getFormattedDailyReport($date);
            if ($reportData['error']) {
                $result['errors'][] = "Error for date {$date}: " . $reportData['error'];
            } else {
                $result['reports'][$date] = $reportData;
            }
        }

        return $result;
    }

    /**
     * Get account summary including balance and recent usage.
     *
     * @param string|null $reportDate Optional date for daily report in yyMMdd format
     * @return array
     */
    public function getAccountSummary(?string $reportDate = null): array
    {
        $result = [
            'balance_info' => null,
            'daily_report' => null,
            'errors' => []
        ];

        // Get balance information
        $balanceInfo = $this->getFormattedBalance();
        if ($balanceInfo['error']) {
            $result['errors'][] = $balanceInfo['error'];
        } else {
            $result['balance_info'] = $balanceInfo;
        }

        // Get daily report if date provided
        if ($reportDate) {
            if ($this->validateDateFormat($reportDate)) {
                $dailyReport = $this->getFormattedDailyReport($reportDate);
                if ($dailyReport['error']) {
                    $result['errors'][] = $dailyReport['error'];
                } else {
                    $result['daily_report'] = $dailyReport;
                }
            } else {
                $result['errors'][] = 'Invalid date format for daily report';
            }
        }

        return $result;
    }
} 