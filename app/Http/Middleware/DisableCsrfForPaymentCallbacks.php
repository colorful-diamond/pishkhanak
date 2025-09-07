<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;

class DisableCsrfForPaymentCallbacks extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [];

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        // Check if this is a payment callback
        if ($this->isPaymentCallback($request)) {
            return true;
        }

        return parent::inExceptArray($request);
    }
    
    /**
     * Check if the request is a payment callback
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isPaymentCallback(Request $request): bool
    {
        return preg_match('/^payment\/callback\/[^\/]+\/[^\/]+$/', $request->path()) === 1;
    }
} 