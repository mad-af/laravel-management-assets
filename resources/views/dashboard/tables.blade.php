@extends('layouts.dashboard')

@section('title', 'Tables')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-base-content">Tables</h1>
        <p class="text-base-content/70 mt-1">Different table variations using DaisyUI components.</p>
    </div>

    <!-- Advanced Data Table -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Advanced Data Table</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>
                                <label>
                                    <input type="checkbox" class="checkbox" />
                                </label>
                            </th>
                            <th>Name</th>
                            <th>Job</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <label>
                                    <input type="checkbox" class="checkbox" />
                                </label>
                            </th>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12">
                                            <img src="https://img.daisyui.com/images/profile/demo/2@94.webp" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">Hart Hagerty</div>
                                        <div class="text-sm opacity-50">United States</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                Zemlak, Daniel and Leannon
                                <br/>
                                <span class="badge badge-ghost badge-sm">Desktop Support Technician</span>
                            </td>
                            <td>Purple</td>
                            <td>Canada</td>
                            <td>12/16/2020</td>
                            <td><span class="badge badge-success badge-sm">Active</span></td>
                            <td>
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button" class="btn btn-ghost btn-xs">⋮</div>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li><a>Edit</a></li>
                                        <li><a>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>
                                    <input type="checkbox" class="checkbox" />
                                </label>
                            </th>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12">
                                            <img src="https://img.daisyui.com/images/profile/demo/3@94.webp" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">Brice Swyre</div>
                                        <div class="text-sm opacity-50">China</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                Carroll Group
                                <br/>
                                <span class="badge badge-ghost badge-sm">Tax Accountant</span>
                            </td>
                            <td>Red</td>
                            <td>China</td>
                            <td>6/8/2020</td>
                            <td><span class="badge badge-warning badge-sm">Pending</span></td>
                            <td>
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button" class="btn btn-ghost btn-xs">⋮</div>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li><a>Edit</a></li>
                                        <li><a>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>
                                    <input type="checkbox" class="checkbox" />
                                </label>
                            </th>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12">
                                            <img src="https://img.daisyui.com/images/profile/demo/4@94.webp" alt="Avatar" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">Marjy Ferencz</div>
                                        <div class="text-sm opacity-50">Russia</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                Rowe-Schoen
                                <br/>
                                <span class="badge badge-ghost badge-sm">Office Assistant I</span>
                            </td>
                            <td>Crimson</td>
                            <td>Russia</td>
                            <td>3/25/2021</td>
                            <td><span class="badge badge-error badge-sm">Inactive</span></td>
                            <td>
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button" class="btn btn-ghost btn-xs">⋮</div>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li><a>Edit</a></li>
                                        <li><a>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Simple Table -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Simple Table</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Laptop Pro</td>
                            <td>Electronics</td>
                            <td>$1,299</td>
                            <td>25</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Wireless Mouse</td>
                            <td>Accessories</td>
                            <td>$29</td>
                            <td>150</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Mechanical Keyboard</td>
                            <td>Accessories</td>
                            <td>$89</td>
                            <td>75</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Monitor 4K</td>
                            <td>Electronics</td>
                            <td>$399</td>
                            <td>12</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>USB-C Hub</td>
                            <td>Accessories</td>
                            <td>$49</td>
                            <td>88</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Compact Table -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Compact Table</h2>
            <div class="overflow-x-auto">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-001</td>
                            <td>John Smith</td>
                            <td>2024-01-15</td>
                            <td>$125.50</td>
                            <td><span class="badge badge-success badge-xs">Completed</span></td>
                            <td><span class="badge badge-info badge-xs">Paid</span></td>
                            <td>
                                <button class="btn btn-ghost btn-xs">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-002</td>
                            <td>Jane Doe</td>
                            <td>2024-01-14</td>
                            <td>$89.99</td>
                            <td><span class="badge badge-warning badge-xs">Processing</span></td>
                            <td><span class="badge badge-success badge-xs">Paid</span></td>
                            <td>
                                <button class="btn btn-ghost btn-xs">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-003</td>
                            <td>Bob Johnson</td>
                            <td>2024-01-13</td>
                            <td>$299.00</td>
                            <td><span class="badge badge-error badge-xs">Cancelled</span></td>
                            <td><span class="badge badge-error badge-xs">Refunded</span></td>
                            <td>
                                <button class="btn btn-ghost btn-xs">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-004</td>
                            <td>Alice Brown</td>
                            <td>2024-01-12</td>
                            <td>$45.75</td>
                            <td><span class="badge badge-info badge-xs">Shipped</span></td>
                            <td><span class="badge badge-success badge-xs">Paid</span></td>
                            <td>
                                <button class="btn btn-ghost btn-xs">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-005</td>
                            <td>Charlie Wilson</td>
                            <td>2024-01-11</td>
                            <td>$199.99</td>
                            <td><span class="badge badge-success badge-xs">Delivered</span></td>
                            <td><span class="badge badge-success badge-xs">Paid</span></td>
                            <td>
                                <button class="btn btn-ghost btn-xs">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection