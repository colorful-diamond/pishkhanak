<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AiContentStatusPage extends Page
{

    protected static ?string $navigationLabel = 'وضعیت محتوای هوش مصنوعی';
    
    protected static ?string $navigationGroup = 'هوش مصنوعی';
    
    protected static ?int $navigationSort = 2;
    
    protected static string $view = 'filament.pages.ai-content-status';
    
    protected static ?string $title = 'وضعیت تولید محتوا';
    
    protected static ?string $slug = 'ai-content-status';
    
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Disabled - content generation is complete
    }
}