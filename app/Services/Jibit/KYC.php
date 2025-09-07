<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class KYC
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * KYC constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add KYC related methods here once I have the API documentation.
} 