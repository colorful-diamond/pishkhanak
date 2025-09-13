<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PopularServicesPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static string $view = 'filament.pages.popular-services';
    
    protected static ?string $navigationLabel = 'محبوب‌ترین سرویس‌ها';
    
    protected static ?string $title = 'محبوب‌ترین سرویس‌ها';
    
    protected static ?string $navigationGroup = 'تحلیل و گزارش';
    
    protected static ?int $navigationSort = 5;

    protected ?string $heading = 'محبوب‌ترین سرویس‌ها و آمار استفاده';

    public $period = '30'; // Default to last 30 days
    public $category = 'all'; // Default to all categories
    
    public function mount(): void
    {
        // Initialize default values
        $this->period = request('period', '30');
        $this->category = request('category', 'all');
    }

    public function getPopularServices()
    {
        $query = Service::query()
            ->select([
                'services.id',
                'services.title',
                'services.slug',
                'services.price',
                'services.category_id',
                'services.status',
                'services.is_maintenance',
                DB::raw('(
                    SELECT COUNT(*) 
                    FROM service_requests 
                    WHERE service_requests.service_id = services.id
                    ' . ($this->period !== 'all' ? 'AND service_requests.created_at >= NOW() - INTERVAL \'' . (int)$this->period . ' DAY\'' : '') . '
                ) as request_count'),
                DB::raw('(
                    SELECT COUNT(*) 
                    FROM service_results 
                    WHERE service_results.service_id = services.id
                    ' . ($this->period !== 'all' ? 'AND service_results.created_at >= NOW() - INTERVAL \'' . (int)$this->period . ' DAY\'' : '') . '
                ) as result_count'),
                DB::raw('(
                    SELECT COUNT(*) 
                    FROM service_requests 
                    WHERE service_requests.service_id = services.id
                    AND service_requests.status = \'processed\'
                    ' . ($this->period !== 'all' ? 'AND service_requests.created_at >= NOW() - INTERVAL \'' . (int)$this->period . ' DAY\'' : '') . '
                ) as successful_requests'),
                DB::raw('(
                    SELECT COUNT(*) 
                    FROM service_results 
                    WHERE service_results.service_id = services.id
                    AND service_results.status = \'success\'
                    ' . ($this->period !== 'all' ? 'AND service_results.created_at >= NOW() - INTERVAL \'' . (int)$this->period . ' DAY\'' : '') . '
                ) as successful_results'),
                DB::raw('(
                    SELECT COALESCE(SUM(gateway_transactions.amount), 0) 
                    FROM gateway_transactions 
                    WHERE gateway_transactions.status = \'completed\'
                    AND gateway_transactions.metadata->>\'service_id\' IS NOT NULL
                    AND (gateway_transactions.metadata->>\'service_id\')::text::int = services.id
                    ' . ($this->period !== 'all' ? 'AND gateway_transactions.created_at >= NOW() - INTERVAL \'' . (int)$this->period . ' DAY\'' : '') . '
                ) as gateway_revenue'),
                DB::raw('(
                    SELECT COALESCE(SUM(ABS(transactions.amount)), 0) 
                    FROM transactions 
                    WHERE transactions.confirmed = true
                    AND transactions.type = \'withdraw\'
                    AND transactions.meta->>\'service_id\' IS NOT NULL
                    AND (transactions.meta->>\'service_id\')::text::int = services.id
                    ' . ($this->period !== 'all' ? 'AND transactions.created_at >= NOW() - INTERVAL \'' . (int)$this->period . ' DAY\'' : '') . '
                ) as wallet_revenue')
            ])
            ->with(['category']);

        // Apply category filter
        if ($this->category !== 'all') {
            $query->where('category_id', $this->category);
        }

        // Add total usage count (combining requests and results)
        $query->addSelect(DB::raw('(
            (SELECT COUNT(*) FROM service_requests WHERE service_requests.service_id = services.id' . 
            ($this->period !== 'all' ? ' AND service_requests.created_at >= NOW() - INTERVAL \'' . (int)$this->period . ' DAY\'' : '') . ') +
            (SELECT COUNT(*) FROM service_results WHERE service_results.service_id = services.id' . 
            ($this->period !== 'all' ? ' AND service_results.created_at >= NOW() - INTERVAL \'' . (int)$this->period . ' DAY\'' : '') . ')
        ) as total_usage'));

        // Order by total usage
        $services = $query->orderByDesc('total_usage')
            ->limit(20)
            ->get();

        // Calculate additional metrics
        return $services->map(function ($service) {
            $totalRevenue = ($service->gateway_revenue ?? 0) + ($service->wallet_revenue ?? 0);
            $totalSuccess = $service->successful_requests + $service->successful_results;
            $totalAttempts = $service->request_count + $service->result_count;
            
            $service->total_revenue = $totalRevenue;
            $service->success_rate = $totalAttempts > 0 ? round(($totalSuccess / $totalAttempts) * 100, 1) : 0;
            $service->average_revenue = $totalAttempts > 0 ? round($totalRevenue / $totalAttempts) : 0;
            $service->total_comments = 0; // Set to 0 since we don't have comments relationship
            
            // Determine primary tracking method
            if ($service->request_count > 0 && $service->result_count == 0) {
                $service->tracking_method = 'service_requests';
            } elseif ($service->result_count > 0 && $service->request_count == 0) {
                $service->tracking_method = 'service_results';
            } else {
                $service->tracking_method = 'both';
            }
            
            return $service;
        });
    }

    public function getServiceStats()
    {
        // Get overall statistics
        $totalServices = Service::where('status', 'active')->count();
        
        $dateFilter = $this->period !== 'all' 
            ? Carbon::now()->subDays((int)$this->period) 
            : null;
        
        // Total usage from both tables
        $totalRequests = DB::table('service_requests')
            ->when($dateFilter, function ($query) use ($dateFilter) {
                return $query->where('created_at', '>=', $dateFilter);
            })
            ->count();
            
        $totalResults = DB::table('service_results')
            ->when($dateFilter, function ($query) use ($dateFilter) {
                return $query->where('created_at', '>=', $dateFilter);
            })
            ->count();
        
        $totalUsage = $totalRequests + $totalResults;
        
        // Revenue calculations
        $gatewayRevenue = DB::table('gateway_transactions')
            ->where('status', 'completed')
            ->whereRaw("metadata->>'service_id' IS NOT NULL")
            ->when($dateFilter, function ($query) use ($dateFilter) {
                return $query->where('created_at', '>=', $dateFilter);
            })
            ->sum('amount');
            
        $walletRevenue = DB::table('transactions')
            ->where('confirmed', true)
            ->where('type', 'withdraw')
            ->whereRaw("meta->>'service_id' IS NOT NULL")
            ->when($dateFilter, function ($query) use ($dateFilter) {
                return $query->where('created_at', '>=', $dateFilter);
            })
            ->sum(DB::raw('ABS(amount)'));
        
        $totalRevenue = $gatewayRevenue + $walletRevenue;
        
        // Services with activity
        $servicesWithActivity = Service::query()
            ->where(function ($query) use ($dateFilter) {
                $query->whereHas('requests', function ($q) use ($dateFilter) {
                    if ($dateFilter) {
                        $q->where('created_at', '>=', $dateFilter);
                    }
                })
                ->orWhereHas('results', function ($q) use ($dateFilter) {
                    if ($dateFilter) {
                        $q->where('created_at', '>=', $dateFilter);
                    }
                });
            })
            ->count();
        
        return [
            'total_services' => $totalServices,
            'services_with_activity' => $servicesWithActivity,
            'total_usage' => $totalUsage,
            'total_requests' => $totalRequests,
            'total_results' => $totalResults,
            'total_revenue' => $totalRevenue,
            'gateway_revenue' => $gatewayRevenue,
            'wallet_revenue' => $walletRevenue,
            'average_per_service' => $servicesWithActivity > 0 ? round($totalUsage / $servicesWithActivity) : 0,
        ];
    }

    public function getCategories()
    {
        return \App\Models\Category::whereHas('services')->get();
    }

    public function setPeriod($period)
    {
        $this->period = $period;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }
}