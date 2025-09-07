<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Traits\HasResourcePermissions;

abstract class BaseResource extends Resource
{
    use HasResourcePermissions;

    protected static function getPermissionPrefix(): string
    {
        // By default, use the model name as prefix
        // For example: User -> users, Service -> services
        $modelClass = static::getModel();
        $modelName = class_basename($modelClass);
        
        // Convert CamelCase to kebab-case
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $modelName));
    }

    public static function canViewAny(): bool
    {
        $prefix = static::getPermissionPrefix();
        return auth()->user()?->can($prefix . '.view') ?? false;
    }

    public static function canCreate(): bool
    {
        $prefix = static::getPermissionPrefix();
        return auth()->user()?->can($prefix . '.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        $prefix = static::getPermissionPrefix();
        return auth()->user()?->can($prefix . '.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        $prefix = static::getPermissionPrefix();
        return auth()->user()?->can($prefix . '.delete') ?? false;
    }

    public static function canView($record): bool
    {
        $prefix = static::getPermissionPrefix();
        return auth()->user()?->can($prefix . '.view') ?? false;
    }

    // Special method to override permission prefix for resources that don't follow naming convention
    protected static function getCustomPermissions(): array
    {
        return [];
    }
}