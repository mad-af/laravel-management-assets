@extends('layouts.dashboard')

@section('title', 'Asset Maintenance')

@section('content')

    <!-- Dashboard Content Header -->
    <livewire:dashboard-content-header title="Perawatan Aset" description="Kelola dan pantau aktivitas perawatan aset"
        button-text="Tambah Perawatan" button-icon="o-plus" button-action="openMaintenanceDrawer" :additional-buttons="[
                    // [
                    //     'text' => 'Tukar Tampilan',
                    //     'icon' => 'o-view-columns',
                    //     'class' => 'btn-sm',
                    //     'action' => 'toggleMaintenanceView()'
                    // ],
                    [
                        'text' => 'Unduh Data Maintenace',
                        'icon' => 'o-document-arrow-down',
                        'class' => ' btn-sm',
                        'action' => 'downloadAssetMaintenance'
                    ]
                ]" />

    {{-- Toggle tampilan: Tabel vs Kanban --}}
    {{-- <div x-data="{ maintenanceView: 'table' }"
        x-on:toggleMaintenanceView.window="maintenanceView = maintenanceView === 'table' ? 'kanban' : 'table'"> --}}
        {{-- Tabel Maintenances --}}
        {{-- <div x-show="maintenanceView === 'table'" x-cloak>
            <livewire:maintenances.table />
        </div> --}}

        {{-- Tampilan Kanban --}}
        {{-- <div x-show="maintenanceView === 'kanban'" x-cloak> --}}
            <livewire:maintenances.kanban-board />
            {{--
        </div> --}}
        {{-- </div> --}}



    <livewire:maintenances.drawer />

    <!-- Insurance Claim Modal (Page-level) -->
    <div 
        x-data="{ showInsuranceClaimModal: false }"
        x-on:open-insurance-claim-modal.window="showInsuranceClaimModal = true"
    >
        <div 
            x-show="showInsuranceClaimModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50"
                 @click="showInsuranceClaimModal = false; Livewire.dispatch('dismiss-insurance-claim-prompt')"></div>

            <!-- Modal -->
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md mx-auto bg-base-100 rounded-lg shadow-xl"
                >
                    <div class="p-6">
                        <div class="flex gap-3 items-start">
                            <div class="flex-shrink-0 mt-0.5 w-10 h-10 rounded-full bg-info/20 flex items-center justify-center">
                                <x-icon name="o-information-circle" class="w-6 h-6 text-info" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold">Buka Form Klaim Asuransi?</h4>
                                <p class="mt-1 text-sm text-base-content/80">
                                    Klaim asuransi untuk maintenance ini telah dibuat. Ingin mengisi detail klaim sekarang?
                                </p>
                                <div class="flex gap-2 mt-4 justify-end">
                                    <x-button 
                                        label="Nanti Saja" 
                                        class="btn-ghost btn-sm" 
                                        @click="showInsuranceClaimModal = false; Livewire.dispatch('dismiss-insurance-claim-prompt')"
                                    />
                                    <x-button 
                                        label="Ya, Isi Sekarang" 
                                        class="btn-info btn-sm" 
                                        @click="showInsuranceClaimModal = false; Livewire.dispatch('confirm-open-claim')"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection