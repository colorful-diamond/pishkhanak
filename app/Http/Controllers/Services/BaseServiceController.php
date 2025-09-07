<?php

namespace App\Http\Controllers\Services;

use App\Models\Service;
use Illuminate\Http\Request;

interface BaseServiceController
{
    /**
     * Handle service submission
     */
    public function handle(Request $request, Service $service);

    /**
     * Process service and return data
     */
    public function process(array $serviceData, Service $service): array;

    /**
     * Show service result
     */
    public function show(string $resultId, Service $service);

    /**
     * Show progress page (optional - for background processing services)
     */
    public function showProgress(Request $request, Service $service, string $hash);

    /**
     * Handle OTP submission (optional - for services that require OTP)
     */
    public function handleOtpSubmission(Request $request, Service $service);

    /**
     * Show SMS verification page (optional - for SMS-based services)
     */
    public function showSmsVerification(Request $request, Service $service, string $hash);

    /**
     * Handle SMS verification submission (optional - for SMS-based services)
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash);

    /**
     * Show SMS result page (optional - for SMS-based services)
     */
    public function showSmsResult(Request $request, Service $service, string $id);
} 