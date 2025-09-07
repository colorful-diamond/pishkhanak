<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function history(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Validate per_page parameter (default 10, max 100)
        $perPage = min(max((int) $request->get('per_page', 10), 1), 100);
        
        // Build query with filters
        $query = $user->transactions()
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            // Map status values to transaction confirmed status
            $status = $request->input('status');
            if ($status === 'completed') {
                $query->where('confirmed', true);
            } elseif ($status === 'pending') {
                $query->where('confirmed', false);
            } elseif ($status === 'failed') {
                $query->where('confirmed', false)->where('meta->status', 'failed');
            }
        }

        if ($request->filled('date_from')) {
            try {
                // Convert Persian date to Gregorian if needed
                $dateFrom = $this->convertPersianToGregorian($request->input('date_from'));
                $query->whereDate('created_at', '>=', $dateFrom);
            } catch (\Exception $e) {
                // If date conversion fails, ignore this filter
            }
        }

        if ($request->filled('date_to')) {
            try {
                // Convert Persian date to Gregorian if needed
                $dateTo = $this->convertPersianToGregorian($request->input('date_to'));
                $query->whereDate('created_at', '<=', $dateTo);
            } catch (\Exception $e) {
                // If date conversion fails, ignore this filter
            }
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $transactions */
        $transactions = $query->paginate($perPage);

        // Calculate statistics
        $successfulTransactions = $user->transactions()->where('confirmed', true)->count();
        $failedTransactions = $user->transactions()->where('confirmed', false)->count();

        // Get user's wallet
        $wallet = $user->wallet;

        return view('front.user.history', compact('transactions', 'wallet', 'successfulTransactions', 'failedTransactions'));
    }

    /**
     * Convert Persian date to Gregorian date
     * 
     * @param string $persianDate
     * @return string
     * @throws \Exception
     */
    private function convertPersianToGregorian(string $persianDate): string
    {
        // If the date is already in Gregorian format (YYYY-MM-DD), return as is
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $persianDate)) {
            // Check if it's a valid Gregorian date
            $parts = explode('-', $persianDate);
            $year = (int) $parts[0];
            $month = (int) $parts[1];
            $day = (int) $parts[2];
            
            // If year is > 1500, it's likely already Gregorian
            if ($year > 1500) {
                return $persianDate;
            }
        }
        
        // Parse Persian date and convert to Gregorian
        $parts = explode('-', $persianDate);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid date format');
        }
        
        $year = (int) $parts[0];
        $month = (int) $parts[1];
        $day = (int) $parts[2];
        
        // Validate Persian date ranges
        if ($year < 1300 || $year > 1500 || $month < 1 || $month > 12 || $day < 1 || $day > 31) {
            throw new \Exception('Invalid Persian date');
        }
        
        // Use Verta to convert Persian to Gregorian
        $verta = new \Hekmatinasser\Verta\Verta($year, $month, $day);
        return $verta->formatGregorian('Y-m-d');
    }

    public function wallet(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Get the user's wallet
        $wallet = $user->wallet;
        
        // Get latest transactions for display using Bavix
        /** @var \Illuminate\Database\Eloquent\Collection $latestTransactions */
        $latestTransactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate statistics
        $totalTransactions = $user->transactions()->count();
        
        // Calculate total deposits (only confirmed deposit transactions)
        $totalDeposits = $user->transactions()
            ->where('type', 'deposit')
            ->where('confirmed', true)
            ->sum('amount');

        // Get available payment gateways
        $gateways = PaymentGateway::where('is_active', true)->get();

        // Note: $user->balance and $user->balanceFloat are available via Bavix HasWallet trait
        return view('front.user.wallet', compact('wallet', 'latestTransactions', 'gateways', 'totalTransactions', 'totalDeposits'));
    }

    public function chargeWallet(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000|max:50000000', // Minimum 1,000 IRT, Maximum 50,000,000 IRT
            'gateway_id' => 'required|exists:payment_gateways,id',
            'continue_service' => 'nullable|string',
            'service_request_hash' => 'nullable|string',
            'service_session_key' => 'nullable|string'
        ]);

        try {
            // Validate gateway is active and supports the amount
            $gateway = \App\Models\PaymentGateway::findOrFail($request->gateway_id);
            
            if (!$gateway->is_active) {
                throw new \Exception('درگاه پرداخت انتخاب شده در دسترس نیست');
            }

            if (!$gateway->supportsAmount($request->amount)) {
                throw new \Exception('مبلغ وارد شده توسط درگاه انتخاب شده پشتیبانی نمی‌شود');
            }

            // Prepare metadata for service continuation
            $metadata = [
                'type' => 'wallet_charge',
                'user_id' => Auth::id(), // This will be null for non-authenticated users
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            // Add service continuation data if provided
            if ($request->has('continue_service')) {
                $serviceSlug = $request->continue_service;
                
                // Fetch service information to store in metadata
                $service = \App\Models\Service::where('slug', $serviceSlug)->first();
                if ($service) {
                    $metadata['continue_service'] = $serviceSlug;
                    $metadata['service_id'] = $service->id;
                    $metadata['service_title'] = $service->title;
                    $metadata['service_name'] = $service->title; // For backward compatibility
                    $metadata['service_request_hash'] = $request->service_request_hash;
                    $metadata['service_session_key'] = $request->service_session_key;
                    $metadata['type'] = 'wallet_charge_for_service';
                    
                    // Store service information for display purposes
                    $metadata['service_price'] = $service->price;
                    $metadata['service_category'] = $service->category?->name ?? 'بدون دسته‌بندی';
                } else {
                    $metadata['continue_service'] = $serviceSlug;
                    $metadata['service_request_hash'] = $request->service_request_hash;
                    $metadata['service_session_key'] = $request->service_session_key;
                    $metadata['type'] = 'wallet_charge_for_service';
                }
            }

            // For non-authenticated users, store session data for post-payment login
            if (!Auth::check()) {
                $metadata['guest_session'] = session()->getId();
                $metadata['requires_login'] = true;
                
                // Store the payment intent in session
                session(['pending_wallet_charge' => [
                    'amount' => $request->amount,
                    'service_continuation' => $request->only(['continue_service', 'service_request_hash', 'service_session_key'])
                ]]);
            }

            // Create payment request for the new payment system
            $paymentRequest = new Request([
                'gateway_id' => $request->gateway_id,
                'amount' => $request->amount,
                'currency' => 'IRT',
                'description' => $request->has('continue_service') ? 
                    'شارژ کیف‌پول برای ادامه سرویس' : 'شارژ کیف‌پول',
                'metadata' => $metadata
            ]);

            // Use the new PaymentController to initialize payment
            $paymentController = app(\App\Http\Controllers\PaymentController::class);
            return $paymentController->initializePayment($paymentRequest);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Wallet charge failed', [
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'gateway_id' => $request->gateway_id,
                'error' => $e->getMessage()
            ]);

            $redirectRoute = Auth::check() ? 'app.user.wallet' : 'app.page.home';
            return redirect()->route($redirectRoute)
                ->with('error', 'خطا در ایجاد پرداخت: ' . $e->getMessage());
        }
    }

    public function paymentCallback(Request $request, $gateway)
    {
        // This would handle payment gateway callbacks
        $transactionId = $request->query('transaction_id');
        $status = $request->query('status', 'failed');

        if (!$transactionId) {
            return redirect()->route('app.user.wallet')
                ->with('error', 'شناسه تراکنش معتبر نیست.');
        }

        $transaction = Transaction::where('uuid', $transactionId)->first();

        if (!$transaction) {
            return redirect()->route('app.user.wallet')
                ->with('error', 'تراکنش یافت نشد.');
        }

        if ($status === 'success') {
            $transaction->update([
                'confirmed' => true,
                'meta' => array_merge($transaction->meta ?? [], [
                    'payment_verified_at' => now(),
                    'gateway_reference' => $request->query('ref_id'),
                ])
            ]);

            return redirect()->route('app.user.wallet')
                ->with('success', 'پرداخت با موفقیت انجام شد. کیف‌پول شما شارژ شد.');
        }

        return redirect()->route('app.user.wallet')
            ->with('error', 'پرداخت ناموفق بود. مبلغ به حساب شما بازگردانده نشد.');
    }



    public function refundWallet(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'bank_account' => 'required|string|max:16',
            'description' => 'required|string|max:500'
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        if ($user->balance < $amount) {
            return redirect()->route('app.user.wallet')
                ->with('error', 'موجودی کیف‌پول شما کافی نیست.');
        }

        try {
            DB::beginTransaction();

            // Using Bavix: Create withdrawal transaction (unconfirmed for admin approval)
            $transaction = $user->withdraw($amount, [
                'description' => 'درخواست عودت وجه به حساب بانکی',
                'refund_request' => true,
                'bank_account' => $request->bank_account,
                'user_description' => $request->description,
            ], false); // false = needs admin approval
            
            $transaction->update([
                'confirmed' => false, // Needs admin approval
                'meta' => array_merge($transaction->meta ?? [], [
                    'refund_request' => true,
                    'bank_account' => $request->bank_account,
                    'user_description' => $request->description,
                    'status' => 'pending_approval'
                ])
            ]);

            DB::commit();

            return redirect()->route('app.user.wallet')
                ->with('success', 'درخواست عودت وجه ثبت شد. پس از بررسی، مبلغ به حساب شما واریز می‌شود.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('app.user.wallet')
                ->with('error', 'خطا در ثبت درخواست: ' . $e->getMessage());
        }
    }

    private function processPayment($transaction, $gateway)
    {
        // This is where you would integrate with actual payment gateways
        // For demonstration, we'll return a mock payment URL
        
        switch ($gateway) {
            case 'zarinpal':
                return "https://sandbox.zarinpal.com/pg/StartPay/{$transaction->uuid}";
            case 'idpay':
                return "https://idpay.ir/p/sandbox/{$transaction->uuid}";
            case 'nextpay':
                return "https://nextpay.org/nx/gateway/payment/{$transaction->uuid}";
            default:
                return null;
        }
    }
}
