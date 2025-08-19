<aside class="min-h-full w-80 bg-base-100 text-base-content">
    <div class="p-4">
        <h2 class="text-2xl font-bold text-primary mb-8">Dashboard</h2>
        
        <ul class="menu p-0 w-full">
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('dashboard.tables') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard.tables') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="font-medium">Tables</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('dashboard.forms') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard.forms') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium">Forms</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('dashboard.components') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard.components') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="font-medium">Components</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('dashboard.settings') }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard.settings') ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium">Settings</span>
                </a>
            </li>
        </ul>
        
        <div class="divider"></div>
        
        <div class="p-3">
            <div class="flex items-center gap-3">
                <div class="avatar placeholder">
                    <div class="bg-neutral text-neutral-content rounded-full w-10">
                        <span class="text-sm">U</span>
                    </div>
                </div>
                <div>
                    <p class="font-medium">User Name</p>
                    <p class="text-sm opacity-70">Administrator</p>
                </div>
            </div>
        </div>
    </div>
</aside>