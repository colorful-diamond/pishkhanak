// Enhanced Sidebars JavaScript - FIXED VERSION
// Handles backdrop overlays, smooth animations, and improved UX for all sidebars

console.log('🚀 Enhanced sidebars script loading...');

// Global state to track open sidebars
let currentOpenSidebar = null;

// Global functions for sidebar control
window.openMobileSideMenu = function() {
    console.log('📱 Opening mobile side menu...');
    
    const mobileSideMenu = document.getElementById('mobileSideMenu');
    const mobileSideMenuBackdrop = document.getElementById('mobileSideMenuBackdrop');
    
    if (mobileSideMenu && mobileSideMenuBackdrop) {
        mobileSideMenu.classList.remove('translate-x-full');
        mobileSideMenuBackdrop.classList.remove('opacity-0', 'invisible');
        document.body.style.overflow = 'hidden';
        currentOpenSidebar = 'mobileSideMenu';
        console.log('✅ Mobile side menu opened');
    } else {
        console.error('❌ Mobile side menu elements not found');
    }
};

window.closeMobileSideMenu = function() {
    console.log('📱 Closing mobile side menu...');
    const mobileSideMenu = document.getElementById('mobileSideMenu');
    const mobileSideMenuBackdrop = document.getElementById('mobileSideMenuBackdrop');
    
    if (mobileSideMenu && mobileSideMenuBackdrop) {
        mobileSideMenu.classList.add('translate-x-full');
        mobileSideMenuBackdrop.classList.add('opacity-0', 'invisible');
        document.body.style.overflow = '';
        currentOpenSidebar = null;
        console.log('✅ Mobile side menu closed');
    } else {
        console.error('❌ Mobile side menu elements not found');
    }
};

// Helper function to check if mobile search is open
window.isMobileSearchOpen = function() {
    const mobileSearchContainer = document.getElementById('mobileSearchContainer');
    return mobileSearchContainer && !mobileSearchContainer.classList.contains('hidden');
};

// Mobile search functions
window.openMobileSearch = function() {
    console.log('🔍 Opening mobile search...');
    const mobileSearchContainer = document.getElementById('mobileSearchContainer');
    if (mobileSearchContainer) {
        // Close any open sidebars first
        window.closeMobileSideMenu();
        mobileSearchContainer.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        console.log('✅ Mobile search opened');
    }
};

window.closeMobileSearch = function() {
    console.log('🔍 Closing mobile search...');
    const mobileSearchContainer = document.getElementById('mobileSearchContainer');
    if (mobileSearchContainer) {
        mobileSearchContainer.classList.add('hidden');
        // Only reset body overflow if no sidebars are open
        if (!currentOpenSidebar) {
            document.body.style.overflow = '';
        }
        console.log('✅ Mobile search closed');
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded, initializing sidebar events...');
    
    // Wait a moment for all elements to be rendered
    setTimeout(function() {
        initializeSidebarEvents();
    }, 500);
});

function initializeSidebarEvents() {
    console.log('🎯 Setting up event listeners...');
    
    // === HAMBURGER BUTTON ===
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    if (hamburgerBtn) {
        console.log('✅ Hamburger button found');
        hamburgerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🍔 Hamburger clicked!');
            window.openMobileSideMenu();
        });
    } else {
        console.error('❌ Hamburger button not found');
    }
    
    // === CLOSE BUTTONS ===
    const closeMobileMenuBtns = document.querySelectorAll('.closeMobileMenu');
    console.log(`🔍 Found ${closeMobileMenuBtns.length} close buttons`);
    
    closeMobileMenuBtns.forEach((btn, index) => {
        console.log(`➕ Adding listener to close button ${index + 1}:`, btn);
        
        // Remove any existing listeners first
        btn.removeEventListener('click', handleCloseClick);
        
        // Add the new listener
        btn.addEventListener('click', handleCloseClick, true);
        
        // Also add direct onclick for backup
        btn.onclick = function(e) {
            console.log(`🔴 Direct onclick for button ${index + 1}`);
            handleCloseClick(e);
        };
    });
    
    // === BACKDROP CLICKS ===
    const mobileSideMenuBackdrop = document.getElementById('mobileSideMenuBackdrop');
    if (mobileSideMenuBackdrop) {
        console.log('✅ Right sidebar backdrop found');
        mobileSideMenuBackdrop.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎭 Right backdrop clicked!');
            window.closeMobileSideMenu();
        });
    }
    
    // === LOGIN MODAL BUTTONS ===
    const openLoginModalBtns = document.querySelectorAll('.openLoginModal');
    console.log(`🔍 Found ${openLoginModalBtns.length} login modal buttons`);
    
    openLoginModalBtns.forEach((btn, index) => {
        btn.addEventListener('click', function(e) {
            console.log(`🔐 Login modal button ${index + 1} clicked - closing sidebars`);
            window.closeMobileSideMenu();
        });
    });
    
    // === SEARCH BUTTONS ===
    const searchButtons = document.querySelectorAll('button[aria-label="Search"]');
    console.log(`🔍 Found ${searchButtons.length} search buttons`);
    
    searchButtons.forEach((btn, index) => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log(`🔍 Search button ${index + 1} clicked`);
            window.openMobileSearch();
        });
    });
    
    // === SEARCH CLOSE BUTTON ===
    const closeMobileSearchBtn = document.getElementById('closeMobileSearch');
    if (closeMobileSearchBtn) {
        console.log('✅ Search close button found');
        closeMobileSearchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🔍 Search close button clicked');
            window.closeMobileSearch();
        });
    }
    
    console.log('🎉 All event listeners initialized!');
}

// Handle close button clicks
function handleCloseClick(e) {
    console.log('🔴 Close button clicked!', e.target);
    e.preventDefault();
    e.stopPropagation();
    
    // Close the right sidebar
    window.closeMobileSideMenu();
}

// Scroll to services function
window.scrollToServices = function() {
    const servicesSection = document.querySelector('[data-category-slug]');
    if (servicesSection) {
        servicesSection.scrollIntoView({ behavior: 'smooth' });
    }
}; 