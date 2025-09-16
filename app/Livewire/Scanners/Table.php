<?php

namespace App\Livewire\Scanners;

use App\Models\Asset;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination, WithAlert;

    public $search = '';
    public $scanHistory = [];

    protected $queryString = ['search'];

    protected $listeners = [
        'scan-added' => 'addScanToHistory',
        'refresh-history' => '$refresh',
    ];

    public function mount()
    {
        $this->loadScanHistory();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addScanToHistory($scanData)
    {
        // Add new scan to the beginning of history
        array_unshift($this->scanHistory, $scanData);
        
        // Limit to 50 recent scans
        $this->scanHistory = array_slice($this->scanHistory, 0, 50);
        
        // Store in session
        session(['recent_scans' => $this->scanHistory]);
    }

    public function clearHistory()
    {
        $this->scanHistory = [];
        session()->forget('recent_scans');
        
        $this->showSuccessAlert(
            'Riwayat scan berhasil dihapus.',
            'Riwayat Dihapus'
        );
    }

    public function viewAsset($assetId)
    {
        return redirect()->route('admin.assets.show', $assetId);
    }

    public function rescanCode($code)
    {
        $this->dispatch('codeScanned', $code)->to('scanners.scanner-interface');
    }

    private function loadScanHistory()
    {
        $this->scanHistory = session('recent_scans', []);
    }

    public function render()
    {
        // Filter scan history based on search
        $filteredHistory = collect($this->scanHistory);
        
        if ($this->search) {
            $filteredHistory = $filteredHistory->filter(function ($scan) {
                return str_contains(strtolower($scan['code']), strtolower($this->search)) ||
                       (isset($scan['asset']) && $scan['asset'] && 
                        str_contains(strtolower($scan['asset']['name']), strtolower($this->search)));
            });
        }

        // Paginate the results
        $currentPage = $this->getPage();
        $perPage = 10;
        $total = $filteredHistory->count();
        $items = $filteredHistory->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return view('livewire.scanners.table', [
            'scanHistory' => $items,
            'total' => $total,
            'hasHistory' => !empty($this->scanHistory)
        ]);
    }
}