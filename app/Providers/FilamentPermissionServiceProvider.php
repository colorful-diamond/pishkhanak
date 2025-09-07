<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Resources\Resource;

class FilamentPermissionServiceProvider extends ServiceProvider
{
    private const RESOURCE_PERMISSIONS = [
        'UserResource' => 'users',
        'ServiceResource' => 'services', 
        'ServiceCategoryResource' => 'service-categories',
        'PostResource' => 'posts',
        'CategoryResource' => 'categories',
        'TagResource' => 'tags',
        'CommentResource' => 'comments',
        'PageResource' => 'pages',
        'ContactMessageResource' => 'contact-messages',
        'AiContentResource' => 'ai-content',
        'AiSettingResource' => 'ai-settings',
        'AutoResponseResource' => 'auto-responses',
        'AutoResponseContextResource' => 'auto-response-contexts',
        'TokenResource' => 'tokens',
        'TokenRefreshLogResource' => 'tokens',
        'SettingResource' => 'settings',
        'BankResource' => 'banks',
        'CurrencyResource' => 'currencies',
        'FooterSectionResource' => 'footer-sections',
        'FooterLinkResource' => 'footer-links',
        'FooterContentResource' => 'footer-contents',
        'SiteLinkResource' => 'site-links',
        'RedirectResource' => 'redirects',
        'WalletResource' => 'wallets',
        'WalletTransactionResource' => 'wallet-transactions',
        'GatewayTransactionResource' => 'gateway-transactions',
        'PaymentGatewayResource' => 'payment-gateways',
        'PaymentSourceResource' => 'payment-sources',
        'RoleResource' => 'roles',
        'TicketStatusResource' => 'ticket-statuses',
        'TicketPriorityResource' => 'ticket-priorities',
        'TicketCategoryResource' => 'ticket-categories',
        'TicketTemplateResource' => 'ticket-templates',
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureResourcePermissions();
    }

    private function configureResourcePermissions(): void
    {
        // Apply permissions to all registered resources
        foreach (Filament::getResources() as $resource) {
            $resourceClass = class_basename($resource);
            
            if (isset(self::RESOURCE_PERMISSIONS[$resourceClass])) {
                $permissionPrefix = self::RESOURCE_PERMISSIONS[$resourceClass];
                $this->applyPermissionsToResource($resource, $permissionPrefix);
            }
        }
    }

    private function applyPermissionsToResource(string $resource, string $permissionPrefix): void
    {
        // This is a conceptual approach - Filament doesn't allow runtime modification
        // Instead, we'll document the expected permissions for each resource
        
        // The actual permission implementation should be done in each Resource class
        // using the canViewAny(), canCreate(), canEdit(), canDelete() methods
    }

    public static function getResourcePermission(string $resourceClass, string $action): string
    {
        $resourceName = class_basename($resourceClass);
        $permissionPrefix = self::RESOURCE_PERMISSIONS[$resourceName] ?? 'unknown';
        
        return $permissionPrefix . '.' . $action;
    }
}