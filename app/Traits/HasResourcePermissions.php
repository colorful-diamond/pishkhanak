<?php

namespace App\Traits;

trait HasResourcePermissions
{
    protected static function getResourcePermissionPrefix(): string
    {
        // Extract permission prefix from resource class name
        $resourceClass = class_basename(static::class);
        
        // Remove 'Resource' suffix and convert to kebab-case
        $name = str_replace('Resource', '', $resourceClass);
        
        // Convert CamelCase to kebab-case
        $kebabCase = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $name));
        
        // Handle special cases
        $permissionMap = [
            'service-category' => 'service-categories',
            'auto-response-context' => 'auto-response-contexts',
            'footer-section' => 'footer-sections',
            'footer-link' => 'footer-links',
            'footer-content' => 'footer-contents',
            'site-link' => 'site-links',
            'wallet-transaction' => 'wallet-transactions',
            'gateway-transaction' => 'gateway-transactions',
            'payment-gateway' => 'payment-gateways',
            'payment-source' => 'payment-sources',
            'ticket-status' => 'ticket-statuses',
            'ticket-priority' => 'ticket-priorities',
            'ticket-category' => 'ticket-categories',
            'ticket-template' => 'ticket-templates',
            'contact-message' => 'contact-messages',
            'ai-setting' => 'ai-settings',
            'ai-content' => 'ai-content',
            'auto-response' => 'auto-responses',
            'token-refresh-log' => 'tokens',
        ];

        return $permissionMap[$kebabCase] ?? $kebabCase . 's';
    }

    public static function canViewAny(): bool
    {
        $permission = static::getResourcePermissionPrefix() . '.view';
        return auth()->user()?->can($permission) ?? false;
    }

    public static function canCreate(): bool
    {
        $permission = static::getResourcePermissionPrefix() . '.create';
        return auth()->user()?->can($permission) ?? false;
    }

    public static function canEdit($record): bool
    {
        $permission = static::getResourcePermissionPrefix() . '.edit';
        return auth()->user()?->can($permission) ?? false;
    }

    public static function canDelete($record): bool
    {
        $permission = static::getResourcePermissionPrefix() . '.delete';
        return auth()->user()?->can($permission) ?? false;
    }

    public static function canView($record): bool
    {
        $permission = static::getResourcePermissionPrefix() . '.view';
        return auth()->user()?->can($permission) ?? false;
    }
}