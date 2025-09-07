<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class Bourse
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Bourse constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add Bourse related methods here once I have the API documentation.
} 