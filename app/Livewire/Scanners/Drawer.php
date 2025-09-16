<?php

namespace App\Livewire\Scanners;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Enums\LoanCondition;
use App\Traits\WithAlert;
use Livewire\Component;
use Carbon\Carbon;

class Drawer extends Component
{
    use WithAlert;

    public $isOpen = false;
    public $mode = 'checkout'; // 'checkout' or 'checkin'
    public $asset = null;
    public $assetLoan = null;

    // Checkout form fields
    public $borrowerName = '';
    public $checkoutDate = '';
    public $dueDate = '';
    public $conditionOut = '';
    public $checkoutNotes = '';

    // Checkin form fields
    public $checkinDate = '';
    public $conditionIn = '';
    public $checkinNotes = '';

    protected $listeners = [
        'openCheckoutDrawer' => 'openCheckout',
        'openCheckinDrawer' => 'openCheckin',
        'closeDrawer' => 'close',
    ];

    protected $rules = [
        'borrowerName' => 'required|string|max:255',
        'checkoutDate' => 'required|date',
        'dueDate' => 'required|date|after:checkoutDate',
        'conditionOut' => 'required|string',
        'checkoutNotes' => 'nullable|string|max:1000',
        'checkinDate' => 'required|date',
        'conditionIn' => 'required|string',
        'checkinNotes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'borrowerName.required' => 'Nama peminjam harus diisi.',
        'checkoutDate.required' => 'Tanggal checkout harus diisi.',
        'dueDate.required' => 'Tanggal jatuh tempo harus diisi.',
        'dueDate.after' => 'Tanggal jatuh tempo harus setelah tanggal checkout.',
        'conditionOut.required' => 'Kondisi keluar harus dipilih.',
        'checkinDate.required' => 'Tanggal checkin harus diisi.',
        'conditionIn.required' => 'Kondisi masuk harus dipilih.',
    ];

    public function mount()
    {
        $this->initializeDates();
    }

    public function openCheckout($assetId)
    {
        $this->asset = Asset::with(['category', 'location'])->find($assetId);
        
        if (!$this->asset) {
            $this->showErrorAlert('Asset tidak ditemukan.', 'Error');
            return;
        }

        if ($this->asset->status->value === 'checked_out') {
            $this->showWarningAlert('Asset sedang dipinjam.', 'Peringatan');
            return;
        }

        $this->mode = 'checkout';
        $this->resetForm();
        $this->initializeDates();
        $this->isOpen = true;
    }

    public function openCheckin($assetId)
    {
        $this->asset = Asset::with(['category', 'location'])->find($assetId);
        
        if (!$this->asset) {
            $this->showErrorAlert('Asset tidak ditemukan.', 'Error');
            return;
        }

        if ($this->asset->status->value !== 'checked_out') {
            $this->showWarningAlert('Asset tidak sedang dipinjam.', 'Peringatan');
            return;
        }

        // Get current loan
        $this->assetLoan = AssetLoan::where('asset_id', $this->asset->id)
            ->whereNull('returned_at')
            ->first();

        if (!$this->assetLoan) {
            $this->showErrorAlert('Data peminjaman tidak ditemukan.', 'Error');
            return;
        }

        $this->mode = 'checkin';
        $this->resetForm();
        $this->checkinDate = now()->format('Y-m-d\TH:i');
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
        $this->asset = null;
        $this->assetLoan = null;
    }

    public function checkout()
    {
        $this->validate([
            'borrowerName' => 'required|string|max:255',
            'checkoutDate' => 'required|date',
            'dueDate' => 'required|date|after:checkoutDate',
            'conditionOut' => 'required|string',
            'checkoutNotes' => 'nullable|string|max:1000',
        ]);

        try {
            // Create asset loan record
            AssetLoan::create([
                'asset_id' => $this->asset->id,
                'borrower_name' => $this->borrowerName,
                'borrowed_at' => Carbon::parse($this->checkoutDate),
                'due_at' => Carbon::parse($this->dueDate),
                'condition_out' => $this->conditionOut,
                'notes' => $this->checkoutNotes,
            ]);

            // Update asset status
            $this->asset->update([
                'status' => 'checked_out',
                'last_seen_at' => now(),
            ]);

            $this->showSuccessAlert(
                "Asset '{$this->asset->name}' berhasil di-checkout ke {$this->borrowerName}.",
                'Checkout Berhasil'
            );

            $this->dispatch('asset-checked-out');
            $this->close();

        } catch (\Exception $e) {
            $this->showErrorAlert(
                'Terjadi kesalahan saat melakukan checkout.',
                'Error'
            );
        }
    }

    public function checkin()
    {
        $this->validate([
            'checkinDate' => 'required|date',
            'conditionIn' => 'required|string',
            'checkinNotes' => 'nullable|string|max:1000',
        ]);

        try {
            // Update asset loan record
            $this->assetLoan->update([
                'returned_at' => Carbon::parse($this->checkinDate),
                'condition_in' => $this->conditionIn,
                'return_notes' => $this->checkinNotes,
            ]);

            // Update asset status
            $this->asset->update([
                'status' => 'available',
                'last_seen_at' => now(),
            ]);

            $this->showSuccessAlert(
                "Asset '{$this->asset->name}' berhasil di-checkin.",
                'Checkin Berhasil'
            );

            $this->dispatch('asset-checked-in');
            $this->close();

        } catch (\Exception $e) {
            $this->showErrorAlert(
                'Terjadi kesalahan saat melakukan checkin.',
                'Error'
            );
        }
    }

    private function resetForm()
    {
        $this->borrowerName = '';
        $this->checkoutDate = '';
        $this->dueDate = '';
        $this->conditionOut = '';
        $this->checkoutNotes = '';
        $this->checkinDate = '';
        $this->conditionIn = '';
        $this->checkinNotes = '';
        $this->resetErrorBag();
    }

    private function initializeDates()
    {
        $this->checkoutDate = now()->format('Y-m-d\TH:i');
        $this->dueDate = now()->addDays(7)->format('Y-m-d\TH:i');
        $this->checkinDate = now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('livewire.scanners.drawer', [
            'loanConditions' => LoanCondition::cases(),
        ]);
    }
}