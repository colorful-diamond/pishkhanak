<?php

namespace App\Services;

use App\Models\GatewayTransaction;

/**
 * Helper service for generating SEP payment redirects and forms
 */
class SepFormHelper
{
    /**
     * Generate redirect URL for SEP payment
     * SEP uses direct GET redirect to payment page
     */
    public static function generatePaymentUrl(string $token): string
    {
        return 'https://sep.shaparak.ir/OnlinePG/SendToken?token=' . urlencode($token);
    }

    /**
     * Generate HTML form for redirecting to SEP payment page (alternative method)
     * This can be used if POST method is preferred over direct redirect
     */
    public static function generatePaymentForm(
        string $token,
        array $options = []
    ): string {
        $formId = $options['form_id'] ?? 'sep-payment-form';
        $autoSubmit = $options['auto_submit'] ?? true;
        $submitText = $options['submit_text'] ?? 'پرداخت';
        $loadingText = $options['loading_text'] ?? 'در حال انتقال به درگاه پرداخت سامان...';

        $html = "<form method=\"post\" action=\"https://sep.shaparak.ir/OnlinePG/OnlinePG\" id=\"{$formId}\">\n";
        $html .= "    <input type=\"hidden\" name=\"Token\" value=\"{$token}\" />\n";
        
        $html .= "    <div class=\"text-center\">\n";
        $html .= "        <button type=\"submit\" class=\"btn btn-primary btn-lg\">{$submitText}</button>\n";
        $html .= "        <div id=\"loading-message\" style=\"display: none; margin-top: 10px;\">\n";
        $html .= "            <i class=\"fas fa-spinner fa-spin\"></i> {$loadingText}\n";
        $html .= "        </div>\n";
        $html .= "    </div>\n";
        $html .= "    <noscript>\n";
        $html .= "        <div class=\"text-center mt-3\">\n";
        $html .= "            <p>{$loadingText}</p>\n";
        $html .= "            <input type=\"submit\" value=\"ادامه پرداخت\" class=\"btn btn-primary\" />\n";
        $html .= "        </div>\n";
        $html .= "    </noscript>\n";
        $html .= "</form>\n";

        if ($autoSubmit) {
            $html .= "<script type=\"text/javascript\">\n";
            $html .= "document.addEventListener('DOMContentLoaded', function() {\n";
            $html .= "    document.getElementById('loading-message').style.display = 'block';\n";
            $html .= "    document.getElementById('{$formId}').submit();\n";
            $html .= "});\n";
            $html .= "</script>\n";
        }

        return $html;
    }

    /**
     * Generate complete payment page HTML
     */
    public static function generatePaymentPage(
        GatewayTransaction $transaction,
        string $token,
        array $options = []
    ): string {
        $title = $options['title'] ?? 'انتقال به درگاه پرداخت سامان';
        $description = $options['description'] ?? 'در حال انتقال به درگاه پرداخت امن سامان...';
        $autoRedirect = $options['auto_redirect'] ?? true;
        $redirectDelay = $options['redirect_delay'] ?? 1000; // milliseconds

        $paymentUrl = self::generatePaymentUrl($token);
        $formattedAmount = number_format($transaction->total_amount);

        $html = "<!DOCTYPE html>\n";
        $html .= "<html lang=\"fa\" dir=\"rtl\">\n";
        $html .= "<head>\n";
        $html .= "    <meta charset=\"UTF-8\">\n";
        $html .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $html .= "    <title>{$title}</title>\n";
        $html .= "    <style>\n";
        $html .= "        body { font-family: 'Vazirmatn', Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }\n";
        $html .= "        .container { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }\n";
        $html .= "        .logo { margin-bottom: 20px; }\n";
        $html .= "        .amount { font-size: 24px; color: #333; margin: 20px 0; }\n";
        $html .= "        .description { color: #666; margin-bottom: 30px; }\n";
        $html .= "        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 20px auto; }\n";
        $html .= "        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }\n";
        $html .= "        .btn { background: #2c5aa0; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }\n";
        $html .= "        .btn:hover { background: #1e3f73; }\n";
        $html .= "    </style>\n";
        $html .= "</head>\n";
        $html .= "<body>\n";
        $html .= "    <div class=\"container\">\n";
        $html .= "        <div class=\"logo\">\n";
        $html .= "            <h2>درگاه پرداخت سامان</h2>\n";
        $html .= "        </div>\n";
        $html .= "        <div class=\"amount\">{$formattedAmount} تومان</div>\n";
        $html .= "        <div class=\"description\">{$description}</div>\n";
        
        if ($autoRedirect) {
            $html .= "        <div class=\"spinner\"></div>\n";
            $html .= "        <p>در حال انتقال...</p>\n";
        } else {
            $html .= "        <a href=\"{$paymentUrl}\" class=\"btn\">ادامه پرداخت</a>\n";
        }
        
        $html .= "    </div>\n";

        if ($autoRedirect) {
            $html .= "    <script>\n";
            $html .= "        setTimeout(function() {\n";
            $html .= "            window.location.href = '{$paymentUrl}';\n";
            $html .= "        }, {$redirectDelay});\n";
            $html .= "    </script>\n";
        }

        $html .= "</body>\n";
        $html .= "</html>\n";

        return $html;
    }

    /**
     * Generate minimal redirect HTML
     */
    public static function generateMinimalRedirect(string $token, int $delay = 0): string
    {
        $paymentUrl = self::generatePaymentUrl($token);
        
        $html = "<!DOCTYPE html>\n";
        $html .= "<html>\n";
        $html .= "<head>\n";
        $html .= "    <meta charset=\"UTF-8\">\n";
        $html .= "    <title>انتقال به درگاه پرداخت</title>\n";
        if ($delay > 0) {
            $html .= "    <meta http-equiv=\"refresh\" content=\"{$delay};url={$paymentUrl}\">\n";
        } else {
            $html .= "    <meta http-equiv=\"refresh\" content=\"0;url={$paymentUrl}\">\n";
        }
        $html .= "</head>\n";
        $html .= "<body>\n";
        $html .= "    <p>در حال انتقال به درگاه پرداخت...</p>\n";
        $html .= "    <p><a href=\"{$paymentUrl}\">اگر به صورت خودکار منتقل نشدید، اینجا کلیک کنید</a></p>\n";
        $html .= "</body>\n";
        $html .= "</html>\n";

        return $html;
    }

    /**
     * Generate JavaScript redirect
     */
    public static function generateJavaScriptRedirect(string $token, int $delay = 1000): string
    {
        $paymentUrl = self::generatePaymentUrl($token);
        
        return "<script type=\"text/javascript\">\n" .
               "setTimeout(function() {\n" .
               "    window.location.href = '{$paymentUrl}';\n" .
               "}, {$delay});\n" .
               "</script>\n";
    }

    /**
     * Parse callback data from SEP
     */
    public static function parseCallbackData(array $requestData): array
    {
        return [
            'token' => $requestData['Token'] ?? null,
            'ref_num' => $requestData['RefNum'] ?? null,
            'status' => 'unknown', // Will be determined by receipt API
            'raw_data' => $requestData,
        ];
    }

    /**
     * Validate callback data
     */
    public static function validateCallbackData(array $callbackData): bool
    {
        // SEP callback must have either Token or RefNum
        return !empty($callbackData['Token']) || !empty($callbackData['RefNum']);
    }

    /**
     * Generate transaction summary for display
     */
    public static function generateTransactionSummary(GatewayTransaction $transaction): array
    {
        return [
            'transaction_id' => $transaction->uuid,
            'amount' => $transaction->total_amount,
            'formatted_amount' => number_format($transaction->total_amount) . ' تومان',
            'description' => $transaction->description,
            'created_at' => $transaction->created_at->format('Y/m/d H:i'),
            'status' => $transaction->status,
            'gateway_name' => 'سامان',
            'reference_id' => $transaction->reference_id,
        ];
    }

    /**
     * Generate error page HTML
     */
    public static function generateErrorPage(string $errorMessage, string $transactionId = null): string
    {
        $html = "<!DOCTYPE html>\n";
        $html .= "<html lang=\"fa\" dir=\"rtl\">\n";
        $html .= "<head>\n";
        $html .= "    <meta charset=\"UTF-8\">\n";
        $html .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $html .= "    <title>خطا در پرداخت</title>\n";
        $html .= "    <style>\n";
        $html .= "        body { font-family: 'Vazirmatn', Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }\n";
        $html .= "        .container { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }\n";
        $html .= "        .error { color: #e74c3c; margin: 20px 0; }\n";
        $html .= "        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }\n";
        $html .= "    </style>\n";
        $html .= "</head>\n";
        $html .= "<body>\n";
        $html .= "    <div class=\"container\">\n";
        $html .= "        <h2>خطا در پرداخت</h2>\n";
        $html .= "        <div class=\"error\">{$errorMessage}</div>\n";
        if ($transactionId) {
            $html .= "        <p>شماره تراکنش: {$transactionId}</p>\n";
        }
        $html .= "        <a href=\"javascript:history.back()\" class=\"btn\">بازگشت</a>\n";
        $html .= "    </div>\n";
        $html .= "</body>\n";
        $html .= "</html>\n";

        return $html;
    }

    /**
     * Generate success page HTML
     */
    public static function generateSuccessPage(array $paymentData): string
    {
        $html = "<!DOCTYPE html>\n";
        $html .= "<html lang=\"fa\" dir=\"rtl\">\n";
        $html .= "<head>\n";
        $html .= "    <meta charset=\"UTF-8\">\n";
        $html .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $html .= "    <title>پرداخت موفق</title>\n";
        $html .= "    <style>\n";
        $html .= "        body { font-family: 'Vazirmatn', Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }\n";
        $html .= "        .container { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }\n";
        $html .= "        .success { color: #27ae60; margin: 20px 0; }\n";
        $html .= "        .details { text-align: right; margin: 20px 0; }\n";
        $html .= "        .btn { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }\n";
        $html .= "    </style>\n";
        $html .= "</head>\n";
        $html .= "<body>\n";
        $html .= "    <div class=\"container\">\n";
        $html .= "        <h2>پرداخت موفق</h2>\n";
        $html .= "        <div class=\"success\">✓ پرداخت شما با موفقیت انجام شد</div>\n";
        
        if (isset($paymentData['amount'])) {
            $html .= "        <div class=\"details\">\n";
            $html .= "            <p><strong>مبلغ:</strong> " . number_format($paymentData['amount']) . " تومان</p>\n";
            if (isset($paymentData['digital_receipt'])) {
                $html .= "            <p><strong>شماره پیگیری:</strong> {$paymentData['digital_receipt']}</p>\n";
            }
            if (isset($paymentData['rrn'])) {
                $html .= "            <p><strong>شماره مرجع:</strong> {$paymentData['rrn']}</p>\n";
            }
            $html .= "        </div>\n";
        }
        
        $html .= "        <a href=\"/\" class=\"btn\">بازگشت به صفحه اصلی</a>\n";
        $html .= "    </div>\n";
        $html .= "</body>\n";
        $html .= "</html>\n";

        return $html;
    }
} 