<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class Finance
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Finance constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add Finance related methods here once I have the API documentation.
} 