<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class Bill
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Bill constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add Bill related methods here once I have the API documentation.
} 