import '../../bootstrap.js';

// Dark mode enhancements for Filament
document.addEventListener('DOMContentLoaded', function() {
    // Ensure dark mode class is properly applied
    function updateDarkModeClasses() {
        const isDark = document.documentElement.classList.contains('dark');
        const body = document.body;
        const filamentBody = document.querySelector('.fi-body');
        
        if (isDark) {
            body.classList.add('dark');
            if (filamentBody) filamentBody.classList.add('dark');
        } else {
            body.classList.remove('dark');
            if (filamentBody) filamentBody.classList.remove('dark');
        }
    }
    
    // Initial check
    updateDarkModeClasses();
    
    // Watch for dark mode changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                updateDarkModeClasses();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    // Enhanced icon visibility
    function ensureIconVisibility() {
        const icons = document.querySelectorAll('svg, .fi-icon, .heroicon');
        icons.forEach(icon => {
            if (icon.style.color === '' || icon.style.color === 'transparent') {
                icon.style.color = 'inherit';
            }
            icon.style.fill = 'currentColor';
        });
    }
    
    // Initial icon check
    ensureIconVisibility();
    
    // Re-check icons on content changes
    const contentObserver = new MutationObserver(function() {
        ensureIconVisibility();
    });
    
    contentObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Force refresh of Filament components when dark mode changes
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-toggle="dark-mode"]')) {
            setTimeout(() => {
                updateDarkModeClasses();
                ensureIconVisibility();
                
                // Refresh any problematic components
                const componentsToRefresh = document.querySelectorAll('.fi-ta, .fi-modal, .fi-dropdown');
                componentsToRefresh.forEach(component => {
                    component.style.display = 'none';
                    component.offsetHeight; // Trigger reflow
                    component.style.display = '';
                });
            }, 100);
        }
    });
});