import './bootstrap';

// Initialize Lucide icons after DOM and CDN are loaded
function initializeLucideIcons() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        console.log('Lucide icons initialized successfully');
    } else {
        console.log('Waiting for Lucide library to load...');
        // Retry after a short delay
        setTimeout(initializeLucideIcons, 200);
    }
}

// Wait for window load to ensure all external scripts are loaded
window.addEventListener('load', function() {
    // Give a small delay to ensure Lucide CDN is fully loaded
    setTimeout(initializeLucideIcons, 100);
});

// Also try to initialize when DOM is ready (fallback)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeLucideIcons, 300);
    });
} else {
    setTimeout(initializeLucideIcons, 300);
}

// Expose function globally for manual re-initialization if needed
window.initLucide = initializeLucideIcons;

// Simple Theme Management
function changeTheme(theme) {
    // Save to localStorage
    localStorage.setItem('theme', theme);
    
    // Apply theme
    document.documentElement.setAttribute('data-theme', theme);
    
    // Update active state
    document.querySelectorAll('.theme-option').forEach(option => {
        option.classList.remove('active');
    });
    
    // Find and mark active theme
    const activeOption = document.querySelector(`[onclick="changeTheme('${theme}')"]`);
    if (activeOption) {
        activeOption.classList.add('active');
    }
}

// Initialize theme on page load
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Update active state after DOM is ready
    setTimeout(() => {
        const activeOption = document.querySelector(`[onclick="changeTheme('${savedTheme}')"]`);
        if (activeOption) {
            activeOption.classList.add('active');
        }
    }, 100);
}

// Initialize theme
initTheme();

// Make changeTheme globally available
window.changeTheme = changeTheme;