<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AiService;
use Illuminate\Support\Facades\Redis;
use App\Models\Post;
use App\Services\ImageAiService;
use App\Services\MidjourneyService;
class TestController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
       dd($this->aiService->generateTitles("test" , "test Blog"));
    }
}