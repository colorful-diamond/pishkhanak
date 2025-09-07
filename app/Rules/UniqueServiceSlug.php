<?php

namespace App\Rules;

use App\Models\Service;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Facades\Log;

class UniqueServiceSlug implements Rule, DataAwareRule
{
    protected $ignoreId;
    protected $data;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        // Start building the query
        $query = Service::where('slug', $value);
        
        // If editing an existing record, exclude it from the check
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }
        
        // Get the parent_id from multiple sources
        $parentId = $this->getParentId();
        
        // Log for debugging (can be removed in production)
        Log::debug('UniqueServiceSlug validation', [
            'slug' => $value,
            'ignoreId' => $this->ignoreId,
            'parentId' => $parentId,
            'formData' => $this->data
        ]);
        
        // Check uniqueness based on parent_id and slug combination
        $existingService = $query->where('parent_id', $parentId)->first();
        
        if ($existingService) {
            Log::debug('Found existing service with same slug and parent_id', [
                'existing_service_id' => $existingService->id,
                'existing_service_slug' => $existingService->slug,
                'existing_service_parent_id' => $existingService->parent_id,
                'ignoreId' => $this->ignoreId
            ]);
        }
        
        return !$existingService;
    }

    /**
     * Get parent_id from various sources
     */
    protected function getParentId()
    {
        // 1. First try to get from form data
        if (isset($this->data['parent_id'])) {
            return $this->data['parent_id'] === '' ? null : $this->data['parent_id'];
        }
        
        // 2. Try to get from current request
        $requestParentId = request()->input('parent_id');
        if ($requestParentId !== null) {
            return $requestParentId === '' ? null : $requestParentId;
        }
        
        // 3. If we're editing an existing service and no parent_id is provided,
        // get the current parent_id from the database
        if ($this->ignoreId) {
            $currentService = Service::find($this->ignoreId);
            if ($currentService) {
                return $currentService->parent_id;
            }
        }
        
        // 4. Default to null (top-level service)
        return null;
    }

    public function message()
    {
        return 'نامک انتخاب شده قبلاً برای این سرویس والد استفاده شده است.';
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
} 