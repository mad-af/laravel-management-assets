<aside class="w-80 min-h-full bg-base-100 text-base-content">
    <div class="p-4">
        <h2 class="mb-8 text-2xl font-bold text-primary">Dashboard</h2>
        
        <ul class="p-0 w-full menu">
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('dashboard.tables') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard.tables') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <i data-lucide="table" class="w-5 h-5"></i>
                    <span class="font-medium">Tables</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('dashboard.forms') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard.forms') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span class="font-medium">Forms</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('dashboard.components') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard.components') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <i data-lucide="puzzle" class="w-5 h-5"></i>
                    <span class="font-medium">Components</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('dashboard.settings') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard.settings') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span class="font-medium">Settings</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('users.index') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span class="font-medium">User Management</span>
                </a>
            </li>
        </ul>
        
        <div class="divider"></div>
        
        <div class="p-3">
            <div class="flex gap-3 items-center">
                <x-avatar initials="U" size="md" placeholder="true" />
                <div>
                    <p class="font-medium">User Name</p>
                    <p class="text-sm opacity-70">Administrator</p>
                </div>
            </div>
        </div>
    </div>
</aside>