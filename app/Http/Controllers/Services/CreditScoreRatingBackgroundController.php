<?php

namespace App\Http\Controllers\Services;

use App\Rules\IranianMobile;
use App\Rules\IranianNationalCode;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class CreditScoreRatingBackgroundController extends LocalApiController
{
    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        $this->serviceSlug = 'credit-score-rating';
        $this->estimatedDuration = 300; // 5 minutes estimated time
        
        $this->requiredFields = [
            'mobile',
            'national_code'
        ];

        $this->validationRules = [
            'mobile' => ['required', 'string', new IranianMobile()],
            'national_code' => ['required', 'string', new IranianNationalCode()],
        ];

        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'national_code.required' => 'کد ملی الزامی است.',
        ];
    }
} 