<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class Promissory
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Promissory constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add Promissory related methods here once I have the API documentation.
} 