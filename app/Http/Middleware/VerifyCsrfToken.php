<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        "pay/res",
        "broadcasting/auth",
        "payment/callback/*",
        "payment/callback/*/*",
        "payment/webhook/*",
        "guest/payment/callback/*",
        "guest/payment/callback/*/*",
    ];
}
