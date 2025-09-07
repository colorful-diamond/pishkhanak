<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class SMS
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * SMS constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add SMS related methods here once I have the API documentation.
} 