import "./bootstrap";
import "./scanners";
import "./qrbarcode";

// Simple Theme Management
function changeTheme(theme) {
    // Save to localStorage
    localStorage.setItem("theme", theme);

    // Apply theme
    document.documentElement.setAttribute("data-theme", theme);

    // Update active state using the centralized function
    updateThemeActiveState(theme);
}

// Initialize theme on page load
function initTheme() {
    const savedTheme = localStorage.getItem("theme") || "light";
    document.documentElement.setAttribute("data-theme", savedTheme);

    // Update active state when DOM is ready
    updateThemeActiveState(savedTheme);
}

// Update theme active state
function updateThemeActiveState(theme) {
    // Remove active class from all theme options
    document.querySelectorAll(".theme-option").forEach((option) => {
        option.classList.remove("active");
    });

    // Find and mark active theme
    const activeOption = document.querySelector(
        `[onclick="changeTheme('${theme}')"]`
    );
    if (activeOption) {
        activeOption.classList.add("active");
    }
}

// Enhanced theme initialization with observers
function enhancedInitTheme() {
    // Apply theme immediately
    initTheme();

    // Watch for DOM changes and reapply theme state
    const observer = new MutationObserver(() => {
        const savedTheme = localStorage.getItem("theme") || "light";
        // Ensure theme is still applied
        if (
            document.documentElement.getAttribute("data-theme") !== savedTheme
        ) {
            document.documentElement.setAttribute("data-theme", savedTheme);
        }
        // Update active states if theme options are present
        if (document.querySelector('[onclick*="changeTheme"]')) {
            updateThemeActiveState(savedTheme);
        }
    });

    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

// Initialize theme with enhanced features
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", enhancedInitTheme);
} else {
    enhancedInitTheme();
}

// Make functions globally available
window.changeTheme = changeTheme;

window.addEventListener("reload-page", () => {
    // Refresh hanya ke main route tanpa parameter dan query parameter
    window.location.href = window.location.pathname;
});
