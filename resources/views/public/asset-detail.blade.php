@extends('layouts.public')

@section('title', $asset->name . ' - Asset Detail')

@section('header-title', 'Asset Information')
@section('header-description', 'Informasi detail asset perusahaan')

@section('content')
    <div class="space-y-6">
        <!-- Asset Stats Card -->
        <livewire:assets.stats-card :asset="$asset" :isHorizontal="true" />
        
        <!-- Basic Information Card -->
        <livewire:assets.basic-info :asset="$asset" />

        <!-- History Monitor Card -->
        <livewire:assets.history-monitor :asset="$asset" />

        <!-- Activity Log Card -->
        <livewire:assets.activity-log :asset="$asset" />
    </div>

@endsection