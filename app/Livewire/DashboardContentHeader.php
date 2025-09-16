<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardContentHeader extends Component
{
    public $title;
    public $description;
    public $buttonText;
    public $buttonIcon = 'o-plus';
    public $buttonClass = 'btn-primary btn-sm';
    public $buttonAction;
    
    // Additional buttons properties (workaround for Livewire v3 slot issues)
    public $additionalButtons = [];


    public function mount(
        $title,
        $description = null,
        $buttonText = null,
        $buttonIcon = 'o-plus',
        $buttonClass = 'btn-primary btn-sm',
        $buttonAction = null,
        $additionalButtons = []
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->buttonText = $buttonText;
        $this->buttonIcon = $buttonIcon;
        $this->buttonClass = $buttonClass;
        $this->buttonAction = $buttonAction;
        $this->additionalButtons = $additionalButtons;
    }

    public function render()
    {
        return view('livewire.dashboard-content-header');
    }

    public function executeButtonAction()
    {
        if ($this->buttonAction) {
            // Handle specific drawer actions
            if (in_array($this->buttonAction, ['openCategoryDrawer', 'openUserDrawer', 'openLocationDrawer', 'openCompanyDrawer', 'openAssetDrawer', 'openAssetLoanDrawer'])) {
                $this->dispatch('openDrawer');
            } else {
                // For other actions, dispatch as-is
                $this->dispatch($this->buttonAction);
            }
        }
    }
}