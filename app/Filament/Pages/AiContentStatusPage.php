<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AiContentStatusPage extends Page
{

    protected static ?string $navigationLabel = 'PERSIAN_TEXT_93a7e79b';
    
    protected static ?string $navigationGroup = 'PERSIAN_TEXT_2a4a3388';
    
    protected static ?int $navigationSort = 2;
    
    protected static string $view = 'filament.pages.ai-content-status';
    
    protected static ?string $title = 'PERSIAN_TEXT_87acb8a4';
    
    protected static ?string $slug = 'ai-content-status';
    
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Disabled - content generation is complete
    }
}