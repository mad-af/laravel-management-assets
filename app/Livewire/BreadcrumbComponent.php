<?php

namespace App\Livewire;

use Livewire\Component;

class BreadcrumbComponent extends Component
{
    public $pageTitle;
    public $pageDescription;
    public $backRoute;
    public $showBreadcrumbs = true;

    public function mount($pageTitle = null, $pageDescription = null, $backRoute = null, $showBreadcrumbs = true)
    {
        $this->pageTitle = $pageTitle;
        $this->pageDescription = $pageDescription;
        $this->backRoute = $backRoute;
        $this->showBreadcrumbs = $showBreadcrumbs;
    }

    public function getBreadcrumbs()
    {
        $segments = request()->segments();
        $breadcrumbs = [];
        
        // Add Home breadcrumb
        $breadcrumbs[] = ['name' => 'Home', 'url' => route('dashboard'), 'active' => false];
        
        $currentPath = '';
        foreach ($segments as $index => $segment) {
            $currentPath .= '/' . $segment;
            $name = ucfirst($segment);
            
            // Skip adding "Dashboard" to breadcrumbs since Home already points to dashboard
            if (strtolower($segment) !== 'dashboard') {
                $isLast = ($index === count($segments) - 1);
                $breadcrumbs[] = [
                    'name' => $name,
                    'url' => $isLast ? null : url($currentPath),
                    'active' => $isLast
                ];
            }
        }
        
        return $breadcrumbs;
    }

    public function render()
    {
        return view('livewire.breadcrumb-component', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}
