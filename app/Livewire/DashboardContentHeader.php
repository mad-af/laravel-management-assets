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

    // Back button properties
    public $showBackButton = false;

    public $backButtonUrl;

    public $backButtonIcon = 'o-arrow-left';

    public $backButtonClass = 'btn-ghost btn-sm';

    // Additional buttons properties (workaround for Livewire v3 slot issues)
    public $additionalButtons = [];

    public function mount(
        $title,
        $description = null,
        $buttonText = null,
        $buttonIcon = 'o-plus',
        $buttonClass = 'btn-primary btn-sm',
        $buttonAction = null,
        $additionalButtons = [],
        $showBackButton = false,
        $backButtonUrl = null,
        $backButtonIcon = 'o-arrow-left',
        $backButtonClass = 'btn-ghost btn-sm'
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->buttonText = $buttonText;
        $this->buttonIcon = $buttonIcon;
        $this->buttonClass = $buttonClass;
        $this->buttonAction = $buttonAction;
        $this->additionalButtons = $additionalButtons;
        $this->showBackButton = $showBackButton;
        $this->backButtonUrl = $backButtonUrl;
        $this->backButtonIcon = $backButtonIcon;
        $this->backButtonClass = $backButtonClass;
    }

    public function render()
    {
        return view('livewire.dashboard-content-header');
    }

    public function executeButtonAction()
    {
        switch ($this->buttonAction) {
            case 'openUserDrawer':
            case 'openAssetTransferDrawer':
            case 'openCategoryDrawer':
            case 'openCompanyDrawer':
            case 'openAssetDrawer':
            case 'openBranchDrawer':
            case 'openEmployeeDrawer':
            case 'openAssetLoanDrawer':
            case 'openMaintenanceDrawer':
            case 'openVehicleTaxDrawer':
                $this->dispatch('open-drawer');
                break;
            case 'openVehicleOdometerDrawer':
                $this->dispatch('open-odometer-drawer');
                break;
            default:
                $this->dispatch($this->buttonAction);
                break;
        }
    }

    public function executeAdditionalButtonAction($action)
    {
        switch ($action) {
            case 'openVehicleProfileDrawer':
                $this->dispatch('open-profile-drawer');
                break;
            case 'openVehicleTaxTypeDrawer':
                $this->dispatch('open-tax-type-drawer');
                break;
            case 'printQrBarcode':
                $this->dispatch('print-qr-barcode');
                break;
            case 'downloadAsset':
                $this->dispatch('download-asset');
                break;
            case 'openBatchDrawer':
                $this->dispatch('open-batch-drawer');
                break;
            case 'downloadAssetMaintenance':
                $this->dispatch('download-asset-maintenance');
                break;
            default:
                $this->dispatch($action);
                break;
        }
    }
}
