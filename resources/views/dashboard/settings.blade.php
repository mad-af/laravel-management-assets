@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-base-content">Settings</h1>
        <p class="text-base-content/70 mt-1">Manage your account settings and preferences.</p>
    </div>

    <!-- Profile Settings -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Profile Settings</h2>
            <form class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="avatar">
                        <div class="w-24 rounded-full">
                            <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" alt="Profile" />
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm">Change Avatar</button>
                        <button type="button" class="btn btn-ghost btn-sm">Remove</button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">First Name</span>
                        </label>
                        <input type="text" value="John" class="input input-bordered" />
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Last Name</span>
                        </label>
                        <input type="text" value="Doe" class="input input-bordered" />
                    </div>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" value="john.doe@example.com" class="input input-bordered" />
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone</span>
                    </label>
                    <input type="tel" value="+1 (555) 123-4567" class="input input-bordered" />
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Bio</span>
                    </label>
                    <textarea class="textarea textarea-bordered" placeholder="Tell us about yourself...">Software developer passionate about creating amazing user experiences.</textarea>
                </div>
                
                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Settings -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Account Settings</h2>
            <div class="space-y-4">
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Email Notifications</span>
                        <input type="checkbox" class="toggle toggle-primary" checked />
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">SMS Notifications</span>
                        <input type="checkbox" class="toggle toggle-primary" />
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Push Notifications</span>
                        <input type="checkbox" class="toggle toggle-primary" checked />
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Marketing Emails</span>
                        <input type="checkbox" class="toggle toggle-primary" />
                    </label>
                </div>
                
                <div class="divider">Privacy</div>
                
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Profile Visibility</span>
                        <input type="checkbox" class="toggle toggle-primary" checked />
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Show Online Status</span>
                        <input type="checkbox" class="toggle toggle-primary" checked />
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Allow Search Engines</span>
                        <input type="checkbox" class="toggle toggle-primary" />
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Security Settings</h2>
            <div class="space-y-4">
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Last login: January 15, 2024 at 2:30 PM</span>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Current Password</span>
                    </label>
                    <input type="password" placeholder="Enter current password" class="input input-bordered" />
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">New Password</span>
                    </label>
                    <input type="password" placeholder="Enter new password" class="input input-bordered" />
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Confirm New Password</span>
                    </label>
                    <input type="password" placeholder="Confirm new password" class="input input-bordered" />
                </div>
                
                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
                
                <div class="divider">Two-Factor Authentication</div>
                
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Enable Two-Factor Authentication</span>
                        <input type="checkbox" class="toggle toggle-primary" />
                    </label>
                    <label class="label">
                        <span class="label-text-alt">Add an extra layer of security to your account</span>
                    </label>
                </div>
                
                <div class="form-control mt-4">
                    <button type="button" class="btn btn-outline">Setup Authenticator App</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Settings -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4">Theme Settings</h2>
            <div class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Choose Theme</span>
                    </label>
                    <select class="select select-bordered">
                        <option>Light</option>
                        <option>Dark</option>
                        <option>Cupcake</option>
                        <option>Bumblebee</option>
                        <option>Emerald</option>
                        <option>Corporate</option>
                        <option>Synthwave</option>
                        <option>Retro</option>
                        <option>Cyberpunk</option>
                        <option>Valentine</option>
                        <option>Halloween</option>
                        <option>Garden</option>
                        <option>Forest</option>
                        <option>Aqua</option>
                        <option>Lofi</option>
                        <option>Pastel</option>
                        <option>Fantasy</option>
                        <option>Wireframe</option>
                        <option>Black</option>
                        <option>Luxury</option>
                        <option>Dracula</option>
                        <option>CMYK</option>
                        <option>Autumn</option>
                        <option>Business</option>
                        <option>Acid</option>
                        <option>Lemonade</option>
                        <option>Night</option>
                        <option>Coffee</option>
                        <option>Winter</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Auto Dark Mode</span>
                        <input type="checkbox" class="toggle toggle-primary" />
                    </label>
                    <label class="label">
                        <span class="label-text-alt">Automatically switch to dark mode at sunset</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card bg-base-100 shadow-xl border-error">
        <div class="card-body">
            <h2 class="card-title text-lg font-semibold mb-4 text-error">Danger Zone</h2>
            <div class="space-y-4">
                <div class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" /></svg>
                    <span>These actions cannot be undone. Please be careful.</span>
                </div>
                
                <div class="flex flex-col gap-2">
                    <button type="button" class="btn btn-outline btn-warning">Export Account Data</button>
                    <button type="button" class="btn btn-outline btn-error">Deactivate Account</button>
                    <button type="button" class="btn btn-error">Delete Account</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection