<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class MobileCharge
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * MobileCharge constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add MobileCharge related methods here once I have the API documentation.
} 