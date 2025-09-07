<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseSmsFinnotechController;
use App\Contracts\ServicePreviewInterface;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class ChequeInquiryController extends BaseSmsFinnotechController implements ServicePreviewInterface
{
    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 ChequeInquiryController configureService called');
        
        $this->apiEndpoint = 'cheque_inquiery';
        $this->scope = 'credit:sms-back-cheques:get';
        $this->requiresSms = true;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code', 'mobile'];
        $this->validationRules = [
            'national_code' => 'required|string|min:10|max:11',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
        ];
        $this->validationMessages = [
            'national_code.required' => 'کد ملی یا شناسه ملی الزامی است',
            'national_code.min' => 'کد ملی باید 10 رقم و شناسه ملی باید 11 رقم باشد',
            'national_code.max' => 'کد ملی باید 10 رقم و شناسه ملی باید 11 رقم باشد',
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است',
        ];
        
        Log::info('🔧 ChequeInquiryController configuration completed', [
            'requiresSms' => $this->requiresSms,
            'apiEndpoint' => $this->apiEndpoint,
            'scope' => $this->scope
        ]);
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        Log::info('🔄 ChequeInquiryController formatResponseData called', [
            'response_keys' => array_keys($responseData)
        ]);


        $result = $responseData['result'] ?? [];
        $nationalCode = $result['nid'] ?? null;
        $legalId = $result['legalId'] ?? null;
        $name = $result['name'] ?? null;
        $chequeList = $result['chequeList'] ?? [];

        // Handle case where no cheques found
        if (isset($result['result']) && $result['result'] === 110) {
            return [
                'status' => 'no_cheques',
                'user_info' => [
                    'national_id' => $nationalCode,
                    'legal_id' => $legalId,
                    'name' => $name,
                ],
                'message' => $result['message'] ?? 'هیچ چک برگشتی یافت نشد',
                'cheques' => [],
                'summary' => [
                    'total_cheques' => 0,
                    'total_amount' => [
                        'raw' => 0,
                        'formatted' => '0 تومان'
                    ]
                ]
            ];
        }

        // Process cheques list
        $formattedCheques = [];
        $totalAmount = 0;

        foreach ($chequeList as $cheque) {
            $amount = isset($cheque['amount']) ? (int) $cheque['amount'] : 0;
            $totalAmount += $amount;

            $formattedCheques[] = [
                'id' => $cheque['id'] ?? null,
                'number' => $cheque['number'] ?? null,
                'account_number' => $cheque['accountNumber'] ?? null,
                'amount' => $amount,
                'amount_formatted' => $this->formatCurrency($amount),
                'date' => $cheque['date'] ?? null,
                'date_formatted' => $this->formatPersianDate($cheque['date'] ?? null),
                'back_date' => $cheque['backDate'] ?? null,
                'back_date_formatted' => $this->formatPersianDate($cheque['backDate'] ?? null),
                'bank_code' => $cheque['bankCode'] ?? null,
                'branch_code' => $cheque['branchCode'] ?? null,
                'branch_description' => $cheque['branchDescription'] ?? null,
            ];
        }

        Log::info('📊 ChequeInquiryController processed cheques', [
            'total_cheques' => count($formattedCheques),
            'total_amount' => $totalAmount,
            'national_code' => $nationalCode
        ]);

        return [
            'status' => 'success',
            'user_info' => [
                'national_id' => $nationalCode,
                'legal_id' => $legalId,
                'name' => $name,
            ],
            'cheques' => $formattedCheques,
            'summary' => [
                'total_cheques' => count($formattedCheques),
                'total_amount' => [
                    'raw' => $totalAmount,
                    'formatted' => $this->formatCurrency($totalAmount)
                ]
            ]
        ];
    }

    /**
     * Format currency amount in tooman (stored as tooman in database)
     */
    private function formatCurrency(int $amount): string
    {
        return number_format($amount) . ' تومان';
    }

    /**
     * Format Persian date from YYYYMMDD to YYYY/MM/DD
     */
    private function formatPersianDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        // If date is in YYYYMMDD format, convert to YYYY/MM/DD
        if (strlen($date) === 8 && is_numeric($date)) {
            return substr($date, 0, 4) . '/' . substr($date, 4, 2) . '/' . substr($date, 6, 2);
        }
        
        return $date;
    }

    /**
     * Show the result page
     */
    public function show(string $resultId, Service $service)
    {
        $result = $this->getServiceResult($resultId, $service);

        if (!$result) {
            abort(404, 'نتیجه سرویس یافت نشد');
        }

        if ($result->isExpired()) {
            return view('front.services.results.expired');
        }

        // Use the existing view for cheque inquiry
        return view('front.services.results.cheque-inquiry', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Check if this service supports preview functionality
     */
    public function supportsPreview(): bool
    {
        return true;
    }

    /**
     * Get preview data for this service
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        try {
            // Return sample preview data for cheque inquiry
            $previewData = [
                'checks_summary' => [
                    'approved' => 3,
                    'pending' => 1,
                    'bounced' => 0
                ],
                'total_amount' => '35,000,000',
                'checks' => [
                    [
                        'number' => '1234567',
                        'amount' => '25,000,000',
                        'due_date' => '1403/10/20',
                        'recipient' => 'احمد احمدی',
                        'account' => '123456789',
                        'status' => 'تصویب شده'
                    ],
                    [
                        'number' => '7890123',
                        'amount' => '10,000,000',
                        'due_date' => '1403/11/05',
                        'recipient' => 'علی علوی',
                        'account' => '987654321',
                        'status' => 'در انتظار'
                    ]
                ]
            ];
            
            return [
                'success' => true,
                'preview_data' => $previewData,
                'from_cache' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('Error generating cheque inquiry preview data', [
                'error' => $e->getMessage(),
                'service_data' => $serviceData
            ]);
            
            return [
                'success' => false,
                'error' => 'خطا در تولید داده‌های پیش‌نمایش'
            ];
        }
    }

    /**
     * Get preview template name
     */
    public function getPreviewTemplate(): string
    {
        return 'front.services.custom.cheque-inquiry.preview';
    }
} 