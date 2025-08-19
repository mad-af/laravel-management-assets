import './bootstrap';

// Initialize Lucide icons after DOM is loaded
function initializeLucideIcons() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        console.log('Lucide icons initialized successfully');
    } else {
        console.error('Lucide library not loaded from CDN');
        // Retry after a short delay
        setTimeout(initializeLucideIcons, 100);
    }
}

// Initialize icons when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLucideIcons);
} else {
    initializeLucideIcons();
}

// Also initialize after a short delay to ensure all elements are rendered
setTimeout(initializeLucideIcons, 500);

// Expose function globally for manual re-initialization if needed
window.initLucide = initializeLucideIcons;