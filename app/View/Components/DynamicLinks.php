<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\FooterManagerService;

class DynamicLinks extends Component
{
    public string $location;
    public string $cssClass;

    /**
     * Create a new component instance.
     */
    public function __construct(string $location, string $cssClass = '')
    {
        $this->location = $location;
        $this->cssClass = $cssClass;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $links = FooterManagerService::getSiteLinks($this->location);
        
        return view('components.dynamic-links', [
            'links' => $links,
            'location' => $this->location,
            'cssClass' => $this->cssClass,
        ]);
    }
} 