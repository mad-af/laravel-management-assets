@extends('layouts.dashboard')

@section('title', 'Components')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-base-content">Components</h1>
        <p class="text-base-content/70 mt-1">Various DaisyUI components showcase.</p>
    </div>

    <!-- Buttons -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Buttons</h2>
            <div class="flex flex-wrap gap-2">
                <button class="btn">Default</button>
                <button class="btn btn-primary">Primary</button>
                <button class="btn btn-secondary">Secondary</button>
                <button class="btn btn-accent">Accent</button>
                <button class="btn btn-info">Info</button>
                <button class="btn btn-success">Success</button>
                <button class="btn btn-warning">Warning</button>
                <button class="btn btn-error">Error</button>
            </div>
            <div class="divider">Button Sizes</div>
            <div class="flex flex-wrap gap-2 items-center">
                <button class="btn btn-xs">Extra Small</button>
                <button class="btn btn-sm">Small</button>
                <button class="btn">Normal</button>
                <button class="btn btn-lg">Large</button>
            </div>
            <div class="divider">Button Variants</div>
            <div class="flex flex-wrap gap-2">
                <button class="btn btn-outline">Outline</button>
                <button class="btn btn-ghost">Ghost</button>
                <button class="btn btn-link">Link</button>
                <button class="btn btn-active">Active</button>
                <button class="btn" disabled>Disabled</button>
            </div>
        </div>
    </div>

    <!-- Badges -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Badges</h2>
            <div class="flex flex-wrap gap-2">
                <div class="badge">Default</div>
                <div class="badge badge-primary">Primary</div>
                <div class="badge badge-secondary">Secondary</div>
                <div class="badge badge-accent">Accent</div>
                <div class="badge badge-info">Info</div>
                <div class="badge badge-success">Success</div>
                <div class="badge badge-warning">Warning</div>
                <div class="badge badge-error">Error</div>
            </div>
            <div class="divider">Badge Sizes</div>
            <div class="flex flex-wrap gap-2 items-center">
                <div class="badge badge-xs">XS</div>
                <div class="badge badge-sm">SM</div>
                <div class="badge">Normal</div>
                <div class="badge badge-lg">LG</div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Alerts</h2>
            <div class="space-y-4">
                <div class="alert">
                    <i data-lucide="info" class="stroke-current shrink-0 w-6 h-6"></i>
                    <span>New software update available.</span>
                </div>
                
                <div class="alert alert-info">
                    <i data-lucide="info" class="stroke-current shrink-0 w-6 h-6"></i>
                    <span>12 unread messages. Tap to see.</span>
                </div>
                
                <div class="alert alert-success">
                    <i data-lucide="check-circle" class="stroke-current shrink-0 h-6 w-6"></i>
                    <span>Your purchase has been confirmed!</span>
                </div>
                
                <div class="alert alert-warning">
                    <i data-lucide="alert-triangle" class="stroke-current shrink-0 h-6 w-6"></i>
                    <span>Warning: Invalid email address!</span>
                </div>
                
                <div class="alert alert-error">
                    <i data-lucide="x-circle" class="stroke-current shrink-0 h-6 w-6"></i>
                    <span>Error! Task failed successfully.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bars -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Progress Bars</h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Default Progress</span>
                        <span>70%</span>
                    </div>
                    <progress class="progress w-full" value="70" max="100"></progress>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Primary Progress</span>
                        <span>85%</span>
                    </div>
                    <progress class="progress progress-primary w-full" value="85" max="100"></progress>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Success Progress</span>
                        <span>100%</span>
                    </div>
                    <progress class="progress progress-success w-full" value="100" max="100"></progress>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Warning Progress</span>
                        <span>45%</span>
                    </div>
                    <progress class="progress progress-warning w-full" value="45" max="100"></progress>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Error Progress</span>
                        <span>25%</span>
                    </div>
                    <progress class="progress progress-error w-full" value="25" max="100"></progress>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading States -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Loading States</h2>
            <div class="flex flex-wrap gap-4 items-center">
                <span class="loading loading-spinner"></span>
                <span class="loading loading-dots"></span>
                <span class="loading loading-ring"></span>
                <span class="loading loading-ball"></span>
                <span class="loading loading-bars"></span>
                <span class="loading loading-infinity"></span>
            </div>
            <div class="divider">Loading Sizes</div>
            <div class="flex flex-wrap gap-4 items-center">
                <span class="loading loading-spinner loading-xs"></span>
                <span class="loading loading-spinner loading-sm"></span>
                <span class="loading loading-spinner loading-md"></span>
                <span class="loading loading-spinner loading-lg"></span>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Stats</h2>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i data-lucide="heart" class="inline-block w-8 h-8 stroke-current"></i>
                    </div>
                    <div class="stat-title">Total Likes</div>
                    <div class="stat-value text-primary">25.6K</div>
                    <div class="stat-desc">21% more than last month</div>
                </div>
                
                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <i data-lucide="zap" class="inline-block w-8 h-8 stroke-current"></i>
                    </div>
                    <div class="stat-title">Page Views</div>
                    <div class="stat-value text-secondary">2.6M</div>
                    <div class="stat-desc">21% more than last month</div>
                </div>
                
                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <div class="avatar online">
                            <div class="w-16 rounded-full">
                                <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                            </div>
                        </div>
                    </div>
                    <div class="stat-value">86%</div>
                    <div class="stat-title">Tasks done</div>
                    <div class="stat-desc text-secondary">31 tasks remaining</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tooltips -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Tooltips</h2>
            <div class="flex flex-wrap gap-4">
                <div class="tooltip" data-tip="hello">
                    <button class="btn">Hover me</button>
                </div>
                <div class="tooltip tooltip-open" data-tip="hello">
                    <button class="btn">Force open</button>
                </div>
                <div class="tooltip tooltip-top" data-tip="top">
                    <button class="btn">Top</button>
                </div>
                <div class="tooltip tooltip-bottom" data-tip="bottom">
                    <button class="btn">Bottom</button>
                </div>
                <div class="tooltip tooltip-left" data-tip="left">
                    <button class="btn">Left</button>
                </div>
                <div class="tooltip tooltip-right" data-tip="right">
                    <button class="btn">Right</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection