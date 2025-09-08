<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AccessPanelProvider::class,
    App\Providers\Filament\MasterPanelProvider::class,
    // App\Providers\TelescopeServiceProvider::class, // Disabled due to missing tables
    App\Providers\ViewComposerServiceProvider::class,
    App\Providers\AutoResponseServiceProvider::class,
];
