@extends('layouts.dashboard')

@section('title', 'Asset Transfer Details')

@section('content')

    <livewire:dashboard-content-header 
        title='Transfer Details' 
        description='{{ $assetTransfer->transfer_no }}' 
        showBackButton />

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- Left Column - Detail Info & Items Table -->
        <div class="space-y-4 lg:col-span-2">
            <!-- Detail Information -->
            <livewire:asset-transfers.detail-info :transferData="$transferData" />
            
            <!-- Items Table -->
            <livewire:asset-transfers.items-table :itemsData="$itemsData" />
        </div>
        
        <!-- Right Column - Quick Actions & Timeline -->
        <div class="space-y-4 lg:col-span-1">
            <!-- Quick Actions -->
            <livewire:asset-transfers.quick-actions :quickActionsData="$quickActionsData" />
            
            <!-- Timeline -->
            <livewire:asset-transfers.timeline :timelineData="$timelineData" />
        </div>
    </div>

    <!-- Edit Drawer -->
    <livewire:asset-transfers.drawer />
   
@endsection