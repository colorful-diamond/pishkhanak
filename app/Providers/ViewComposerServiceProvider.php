<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer(['view1', 'view2', 'view3'], function ($view) {
            if (auth()->check() && method_exists(auth()->user(), 'languages')) {
                $view->with('userLanguages', auth()->user()->languages);
            }
        });
    }
}