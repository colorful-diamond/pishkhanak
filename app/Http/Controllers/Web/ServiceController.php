<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\ServiceControllerFactory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceResult;
use App\Services\BankService;
use App\Traits\SeoTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    use SeoTrait;
    /**
     * Display a single service.
     *
     * @param  string  $slug1
     * @param  string|null  $slug2
     * @return \Illuminate\View\View
     */
    public function show($slug1, $slug2 = null)
    {
        if ($slug2) {
            // This is a child service, e.g., /services/parent/child
            $parent = Service::where('slug', $slug1)->where('status', 'active')->firstOrFail();
            $service = Service::where('slug', $slug2)
                                ->where('parent_id', $parent->id)
                                ->where('status', 'active')
                                ->with(['category', 'author', 'tags'])
                                ->firstOrFail();
        } else {
            // This is a top-level service, e.g., /services/parent
            $service = Service::where('slug', $slug1)
                                ->whereNull('parent_id')
                                ->where('status', 'active')
                                ->with(['category', 'author', 'tags'])
                                ->firstOrFail();
        }

        // Increment the views count
        $service->increment('views');

        // Set SEO for service page
        $parent = $slug2 ? $parent : null;
        $this->setServiceSeo($service, $parent);

        // Add banks data for IBAN-related services
        $banks = null;
        if ($this->isIbanRelatedService($service)) {
            $banks = BankService::getBanksForSlider();
        }

        return view('front.services.single', compact('service', 'banks'));
    }

    public function submit(Request $request, $slug1, $slug2 = null)
    {
        // Find the service
        if ($slug2) {
            // This is a child service, e.g., /services/parent/child
            $parent = Service::where('slug', $slug1)->where('status', 'active')->firstOrFail();
            $service = Service::where('slug', $slug2)
                                ->where('parent_id', $parent->id)
                                ->where('status', 'active')
                                ->with(['category', 'author', 'tags'])
                                ->firstOrFail();
        } else {
            // This is a top-level service, e.g., /services/parent
            $service = Service::where('slug', $slug1)
                                ->whereNull('parent_id')
                                ->where('status', 'active')
                                ->with(['category', 'author', 'tags'])
                                ->firstOrFail();
        }

        // Use the new service payment system
        $servicePaymentService = app(\App\Services\ServicePaymentService::class);
        
        // Get service data from request
        $serviceData = $request->except(['_token', '_method']);
        
        // Handle service submission with payment flow
        $result = $servicePaymentService->handleServiceSubmission($request, $service, $serviceData);
        
        if ($result['success']) {
            // Check if this is a view response (SMS OTP page)
            if (isset($result['view_response'])) {
                return $result['view_response'];
            }
            // Check if this is a redirect response
            elseif (isset($result['redirect'])) {
                return redirect($result['redirect']);
            } else {
                return back()->with('success', $result['message']);
            }
        } else {
            // Check if this is an error redirect
            if (isset($result['redirect'])) {
                return redirect($result['redirect']);
            } else {
                return back()->withErrors([
                    'service_error' => $result['message']
                ])->withInput();
            }
        }
    }

    /**
     * Show service result
     *
     * @param string $resultId
     * @return \Illuminate\Http\Response
     */
    public function showResult(string $resultId)
    {
        Log::info('ServiceController showResult called', [
            'requested_hash' => $resultId,
            'hash_length' => strlen($resultId),
            'current_user_id' => Auth::id(),
            'is_authenticated' => Auth::check(),
            'request_ip' => request()->ip(),
        ]);

        // Debug: Check total records in database
        $totalResults = ServiceResult::count();
        Log::info('Database state before search', [
            'total_service_results' => $totalResults,
            'latest_5_results' => ServiceResult::orderBy('id', 'desc')->limit(5)->get(['id', 'result_hash', 'user_id', 'service_id', 'status'])->toArray(),
        ]);

        // Find the result by hash using Eloquent
        $result = ServiceResult::where('result_hash', $resultId)->first();

        // Additional debugging for the specific hash
        Log::info('Hash search debugging', [
            'searching_for_hash' => $resultId,
            'found_result' => $result ? 'yes' : 'no',
            'exact_hash_matches' => ServiceResult::where('result_hash', $resultId)->count(),
            'similar_hashes' => ServiceResult::where('result_hash', 'LIKE', substr($resultId, 0, 8) . '%')->get(['id', 'result_hash'])->toArray(),
        ]);

        // Log the access attempt for debugging
        Log::info('Service result access attempt', [
            'result_hash' => $resultId,
            'found' => $result ? true : false,
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'result_details' => $result ? [
                'id' => $result->id,
                'user_id' => $result->user_id,
                'service_id' => $result->service_id,
                'status' => $result->status,
                'created_at' => $result->created_at,
            ] : null,
        ]);

        if (!$result) {
            // Enhanced debugging for missing result
            Log::warning('Service result not found - Enhanced debugging', [
                'result_hash' => $resultId,
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
                'total_results_in_db' => ServiceResult::count(),
                'results_for_current_user' => Auth::check() ? ServiceResult::where('user_id', Auth::id())->count() : 0,
                'latest_result_for_user' => Auth::check() ? ServiceResult::where('user_id', Auth::id())->orderBy('id', 'desc')->first(['id', 'result_hash', 'created_at']) : null,
                'database_connection' => DB::connection()->getName(),
            ]);
            
            abort(404, 'نتیجه مورد نظر یافت نشد. ممکن است منقضی شده یا حذف شده باشد.');
        }

        // Check if result has success status
        if ($result->status !== 'success') {
            Log::warning('Service result access denied - wrong status', [
                'result_hash' => $resultId,
                'status' => $result->status,
                'user_id' => Auth::id()
            ]);
            
            abort(404, 'این نتیجه در دسترس نیست.');
        }

        // Check authorization: only the owner can view their results
        if (!Auth::check() || $result->user_id !== Auth::id()) {
            Log::warning('Service result access denied - unauthorized', [
                'result_hash' => $resultId,
                'result_user_id' => $result->user_id,
                'current_user_id' => Auth::id(),
                'ip' => request()->ip()
            ]);
            
            abort(401, 'شما مجاز به مشاهده این نتیجه نیستید.');
        }

        // Check if result is expired
        if ($result->isExpired()) {
            Log::info('Service result access denied - expired', [
                'result_hash' => $resultId,
                'processed_at' => $result->processed_at,
                'user_id' => Auth::id()
            ]);
            
            abort(410, 'این نتیجه منقضی شده است.');
        }

        $service = $result->service;

        // Set SEO for result page
        $this->setSeo([
            'title' => 'نتیجه ' . $service->title,
            'description' => 'نتیجه استعلام ' . $service->title,
            'keywords' => ['نتیجه', 'استعلام', $service->title, 'پیشخوانک'],
            'breadcrumbs' => [
                ['name' => 'خانه', 'url' => route('app.page.home')],
                ['name' => 'خدمات', 'url' => route('services.category', $service->category->slug)],
                ['name' => $service->title, 'url' => route('services.show', $service->slug)],
                ['name' => 'نتیجه']
            ],
            'type' => 'article'
        ]);

        // Try to get the appropriate service controller
        $serviceController = ServiceControllerFactory::getController($service);

        if ($serviceController) {
            // Call the service controller's show method
            return $serviceController->show($resultId, $service);
        }

        // Fallback to default result view
        return view('front.services.result', [
            'service' => $service,
            'result' => $result->getFormattedResult(),
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Display services in a specific category.
     *
     * @param  ServiceCategory $category
     * @return \Illuminate\View\View
     */
    public function showCategory(ServiceCategory $category)
    {
        // Set SEO for category page
        $this->setCategorySeo($category);

        $services = Service::where('service_category_id', $category->id)
                            ->where('status', 'active')
                            ->orderBy('created_at', 'desc')
                            ->paginate(12); // 12 services per page

        return view('front.services.taxonomy', compact('category', 'services'));
    }

    /**
     * Handle credit score form submission.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submitCreditScore(Request $request)
    {
        // Validate the request
        $request->validate([
            'mobile' => 'required|string|regex:/^09\d{9}$/',
            'customer_type' => 'required|in:personal,corporate',
            'national_code' => 'required_if:customer_type,personal|string|size:10',
            'company_id' => 'required_if:customer_type,corporate|string|size:11',
        ], [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.regex' => 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد.',
            'customer_type.required' => 'نوع مشتری الزامی است.',
            'customer_type.in' => 'نوع مشتری باید حقیقی یا حقوقی باشد.',
            'national_code.required_if' => 'کد ملی برای مشتری حقیقی الزامی است.',
            'national_code.size' => 'کد ملی باید 10 رقم باشد.',
            'company_id.required_if' => 'شناسه ملی شرکت برای مشتری حقوقی الزامی است.',
            'company_id.size' => 'شناسه ملی شرکت باید 11 رقم باشد.',
        ]);

        // Here you would typically call your credit score service
        // For now, let's return a mock response
        $mockData = [
            'credit_score' => rand(300, 850),
            'status' => 'فعال',
            'last_update' => now()->format('Y/m/d'),
        ];

        // Return back with success message and data
        return back()->with([
            'success' => true,
            'message' => 'استعلام رتبه اعتباری با موفقیت انجام شد.',
            'credit_score_data' => $mockData,
        ]);
    }

    /**
     * Check if the service is IBAN-related
     *
     * @param Service $service
     * @return bool
     */
    private function isIbanRelatedService(Service $service): bool
    {
        $bankRelatedSlugs = [
            'card-iban',
            'card-account',
            'iban-account',
            'account-iban',
            'account-to-iban',
            'iban-check',
            'sheba-account',
            'iban-validator',
            'iban-generator',
            'bank-inquiry',
            'loan-inquiry',
            'cheque-inquiry',
            'cheque-color-inquiry',
            'third-party-insurance-history'
        ];

        // Get the parent service slug if this is a sub-service
        $serviceSlug = $service->parent_id && $service->parent ? $service->parent->slug : $service->slug;

        return in_array($serviceSlug, $bankRelatedSlugs) || 
               str_contains(strtolower($service->title), 'شبا') ||
               str_contains(strtolower($service->title), 'iban') ||
               str_contains(strtolower($service->title), 'کارت') ||
               str_contains(strtolower($service->title), 'بانک') ||
               str_contains(strtolower($service->title), 'وام') ||
               str_contains(strtolower($service->title), 'چک') ||
               str_contains(strtolower($service->title), 'بیمه');
    }
} 