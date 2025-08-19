@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-base-content">Dashboard</h1>
            <p class="text-base-content/70 mt-1">Welcome back! Here's what's happening.</p>
        </div>
        <div class="flex gap-2">
            <button class="btn btn-primary btn-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Item
            </button>
            <button class="btn btn-outline btn-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stats bg-base-100 shadow">
            <div class="stat">
                <div class="stat-title">Total Users</div>
                <div class="stat-value text-primary">1,234</div>
                <div class="stat-desc text-success">+12% from last month</div>
            </div>
        </div>
        
        <div class="stats bg-base-100 shadow">
            <div class="stat">
                <div class="stat-title">Revenue</div>
                <div class="stat-value text-primary">$45,678</div>
                <div class="stat-desc text-success">+8% from last month</div>
            </div>
        </div>
        
        <div class="stats bg-base-100 shadow">
            <div class="stat">
                <div class="stat-title">Orders</div>
                <div class="stat-value text-primary">892</div>
                <div class="stat-desc text-error">-3% from last month</div>
            </div>
        </div>
        
        <div class="stats bg-base-100 shadow">
            <div class="stat">
                <div class="stat-title">Products</div>
                <div class="stat-value text-primary">156</div>
                <div class="stat-desc text-success">+5% from last month</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-lg font-semibold mb-4">Recent Activity</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                    <span class="text-xs">JD</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold">John Doe</div>
                                                <div class="text-sm opacity-50">john@example.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Created new order</td>
                                    <td>2 minutes ago</td>
                                    <td><span class="badge badge-success badge-sm">Success</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                    <span class="text-xs">JS</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold">Jane Smith</div>
                                                <div class="text-sm opacity-50">jane@example.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Updated profile</td>
                                    <td>5 minutes ago</td>
                                    <td><span class="badge badge-info badge-sm">Info</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                    <span class="text-xs">MB</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold">Mike Brown</div>
                                                <div class="text-sm opacity-50">mike@example.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Deleted item</td>
                                    <td>10 minutes ago</td>
                                    <td><span class="badge badge-warning badge-sm">Warning</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Info -->
        <div class="space-y-6">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-lg font-semibold mb-4">System Status</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span>Server Status</span>
                            <div class="badge badge-success">Online</div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Database</span>
                            <div class="badge badge-success">Connected</div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Cache</span>
                            <div class="badge badge-warning">Clearing</div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Storage</span>
                            <div class="badge badge-info">75% Used</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-lg font-semibold mb-4">Recent Users</h2>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                    <span class="text-xs">JD</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm">John Doe</p>
                                <p class="text-xs opacity-70">2 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                    <span class="text-xs">JS</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm">Jane Smith</p>
                                <p class="text-xs opacity-70">5 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                    <span class="text-xs">MB</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm">Mike Brown</p>
                                <p class="text-xs opacity-70">10 minutes ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection