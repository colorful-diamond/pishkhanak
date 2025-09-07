<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;

class TokenHealthController extends Controller
{
    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     * TokenHealthController constructor.
     */
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Get token health status for all providers.
     *
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        $healthStatus = $this->tokenService->getTokenHealthStatus();
        
        // Calculate overall health
        $totalProviders = count($healthStatus);
        $healthyProviders = 0;
        
        foreach ($healthStatus as $status) {
            if ($status['active'] && !$status['access_token_expired'] && !$status['refresh_token_expired']) {
                $healthyProviders++;
            }
        }
        
        $overallHealth = $healthyProviders === $totalProviders ? 'healthy' : 
                        ($healthyProviders > 0 ? 'partial' : 'unhealthy');
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'overall_health' => $overallHealth,
                'healthy_providers' => $healthyProviders,
                'total_providers' => $totalProviders,
                'providers' => $healthStatus,
                'checked_at' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Refresh tokens that need refresh.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            $results = $this->tokenService->refreshAllTokensNeedingRefresh();
            $deactivatedCount = $this->tokenService->deactivateExpiredTokens();
            
            $successCount = array_sum($results);
            $totalCount = count($results);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Token refresh completed',
                'data' => [
                    'refresh_results' => $results,
                    'successful_refreshes' => $successCount,
                    'total_providers' => $totalCount,
                    'deactivated_tokens' => $deactivatedCount,
                    'refreshed_at' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token refresh failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tokens that need refresh.
     *
     * @return JsonResponse
     */
    public function needsRefresh(): JsonResponse
    {
        $tokens = $this->tokenService->getTokensNeedingRefresh();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'tokens_needing_refresh' => $tokens,
                'count' => count($tokens),
                'checked_at' => now()->toISOString()
            ]
        ]);
    }
} 