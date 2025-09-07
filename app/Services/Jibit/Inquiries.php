<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;

class Inquiries
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Inquiries constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    // I will add Inquiries related methods here once I have the API documentation.
} 