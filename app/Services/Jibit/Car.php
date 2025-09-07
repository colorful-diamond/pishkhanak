<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class Car
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Car constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add Car related methods here once I have the API documentation.
} 