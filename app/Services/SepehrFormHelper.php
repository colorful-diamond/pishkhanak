<?php

namespace App\Services;

use App\Models\GatewayTransaction;

/**
 * Helper service for generating Sepehr payment forms
 */
class SepehrFormHelper
{
    /**
     * Generate HTML form for redirecting to Sepehr payment page
     */
    public static function generatePaymentForm(
        string $paymentUrl,
        string $terminalId,
        string $accessToken,
        array $options = []
    ): string {
        $formId = $options['form_id'] ?? 'sepehr-payment-form';
        $autoSubmit = $options['auto_submit'] ?? true;
        $nationalCode = $options['national_code'] ?? null;
        $getMethod = $options['get_method'] ?? false;
        $submitText = $options['submit_text'] ?? 'پرداخت';
        $loadingText = $options['loading_text'] ?? 'در حال انتقال به درگاه پرداخت...';

        $html = "<form method=\"post\" action=\"{$paymentUrl}\" id=\"{$formId}\">\n";
        $html .= "    <input type=\"hidden\" name=\"TerminalID\" value=\"{$terminalId}\" />\n";
        $html .= "    <input type=\"hidden\" name=\"token\" value=\"{$accessToken}\" />\n";
        
        if ($nationalCode) {
            $html .= "    <input type=\"hidden\" name=\"nationalCode\" value=\"{$nationalCode}\" />\n";
        }
        
        if ($getMethod) {
            $html .= "    <input type=\"hidden\" name=\"getMethod\" value=\"1\" />\n";
        }
        
        $html .= "    <div class=\"text-center\">\n";
        $html .= "        <button type=\"submit\" class=\"btn btn-primary btn-lg\">{$submitText}</button>\n";
        $html .= "        <div id=\"loading-message\" style=\"display: none; margin-top: 10px;\">\n";
        $html .= "            <i class=\"fas fa-spinner fa-spin\"></i> {$loadingText}\n";
        $html .= "        </div>\n";
        $html .= "    </div>\n";
        $html .= "</form>\n";

        if ($autoSubmit) {
            $html .= "<script>\n";
            $html .= "document.addEventListener('DOMContentLoaded', function() {\n";
            $html .= "    document.getElementById('{$formId}').addEventListener('submit', function() {\n";
            $html .= "        document.querySelector('button[type=\"submit\"]').style.display = 'none';\n";
            $html .= "        document.getElementById('loading-message').style.display = 'block';\n";
            $html .= "    });\n";
            $html .= "    \n";
            $html .= "    // Auto-submit after 3 seconds\n";
            $html .= "    setTimeout(function() {\n";
            $html .= "        document.getElementById('{$formId}').submit();\n";
            $html .= "    }, 3000);\n";
            $html .= "});\n";
            $html .= "</script>\n";
        }

        return $html;
    }

    /**
     * Generate payment form from transaction result
     */
    public static function generateFromTransactionResult(
        array $result,
        array $options = []
    ): string {
        if (!$result['success']) {
            throw new \InvalidArgumentException('Cannot generate form from failed transaction result');
        }

        return self::generatePaymentForm(
            $result['payment_url'],
            $result['terminal_id'],
            $result['access_token'],
            $options
        );
    }

    /**
     * Generate bill payment form with additional validation
     */
    public static function generateBillPaymentForm(
        string $paymentUrl,
        string $terminalId,
        string $accessToken,
        string $billId,
        string $payId,
        array $options = []
    ): string {
        // Validate bill ID (13 or 18 digits)
        if (!preg_match('/^\d{13}$|^\d{18}$/', $billId)) {
            throw new \InvalidArgumentException('Bill ID must be 13 or 18 digits');
        }

        // Validate pay ID
        if (!preg_match('/^\d+$/', $payId)) {
            throw new \InvalidArgumentException('Pay ID must contain only digits');
        }

        $options['submit_text'] = $options['submit_text'] ?? 'پرداخت قبض';
        $options['loading_text'] = $options['loading_text'] ?? 'در حال انتقال به درگاه پرداخت قبض...';

        return self::generatePaymentForm($paymentUrl, $terminalId, $accessToken, $options);
    }

    /**
     * Generate mobile top-up form
     */
    public static function generateMobileTopupForm(
        string $paymentUrl,
        string $terminalId,
        string $accessToken,
        string $mobileNumber,
        array $options = []
    ): string {
        // Validate mobile number
        if (!preg_match('/^09\d{9}$/', $mobileNumber)) {
            throw new \InvalidArgumentException('Mobile number must be in format 09xxxxxxxxx');
        }

        $options['submit_text'] = $options['submit_text'] ?? 'شارژ موبایل';
        $options['loading_text'] = $options['loading_text'] ?? 'در حال انتقال به درگاه شارژ...';

        return self::generatePaymentForm($paymentUrl, $terminalId, $accessToken, $options);
    }

    /**
     * Generate identified purchase form (with national code)
     */
    public static function generateIdentifiedPurchaseForm(
        string $paymentUrl,
        string $terminalId,
        string $accessToken,
        string $nationalCode,
        array $options = []
    ): string {
        // Validate national code (10 digits)
        if (!preg_match('/^\d{10}$/', $nationalCode)) {
            throw new \InvalidArgumentException('National code must be 10 digits');
        }

        $options['national_code'] = $nationalCode;
        $options['submit_text'] = $options['submit_text'] ?? 'پرداخت شناسه‌دار';
        $options['loading_text'] = $options['loading_text'] ?? 'در حال تایید هویت و انتقال به درگاه...';

        return self::generatePaymentForm($paymentUrl, $terminalId, $accessToken, $options);
    }

    /**
     * Generate a complete payment page HTML
     */
    public static function generatePaymentPage(
        array $result,
        array $pageOptions = []
    ): string {
        $title = $pageOptions['title'] ?? 'انتقال به درگاه پرداخت';
        $description = $pageOptions['description'] ?? 'لطفا منتظر بمانید تا به درگاه پرداخت منتقل شوید';
        $amount = $pageOptions['amount'] ?? null;
        $currency = $pageOptions['currency'] ?? 'تومان';

        $html = "<!DOCTYPE html>\n";
        $html .= "<html lang=\"fa\" dir=\"rtl\">\n";
        $html .= "<head>\n";
        $html .= "    <meta charset=\"UTF-8\">\n";
        $html .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $html .= "    <title>{$title}</title>\n";
        $html .= "    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
        $html .= "    <link href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css\" rel=\"stylesheet\">\n";
        $html .= "    <style>\n";
        $html .= "        body { font-family: 'Tahoma', sans-serif; background: #f8f9fa; }\n";
        $html .= "        .payment-card { max-width: 500px; margin: 50px auto; }\n";
        $html .= "        .sepehr-logo { width: 120px; height: auto; }\n";
        $html .= "    </style>\n";
        $html .= "</head>\n";
        $html .= "<body>\n";
        $html .= "    <div class=\"container\">\n";
        $html .= "        <div class=\"payment-card\">\n";
        $html .= "            <div class=\"card shadow\">\n";
        $html .= "                <div class=\"card-header text-center bg-primary text-white\">\n";
        $html .= "                    <h4><i class=\"fas fa-credit-card\"></i> {$title}</h4>\n";
        $html .= "                </div>\n";
        $html .= "                <div class=\"card-body text-center\">\n";
        
        if ($amount) {
            $html .= "                    <div class=\"alert alert-info\">\n";
            $html .= "                        <h5>مبلغ قابل پرداخت: " . number_format($amount) . " {$currency}</h5>\n";
            $html .= "                    </div>\n";
        }
        
        $html .= "                    <p class=\"mb-4\">{$description}</p>\n";
        $html .= "                    " . self::generateFromTransactionResult($result) . "\n";
        $html .= "                    <div class=\"mt-3\">\n";
        $html .= "                        <small class=\"text-muted\">\n";
        $html .= "                            <i class=\"fas fa-shield-alt\"></i> پرداخت از طریق درگاه امن سپهر الکترونیک\n";
        $html .= "                        </small>\n";
        $html .= "                    </div>\n";
        $html .= "                </div>\n";
        $html .= "            </div>\n";
        $html .= "        </div>\n";
        $html .= "    </div>\n";
        $html .= "</body>\n";
        $html .= "</html>\n";

        return $html;
    }
} 