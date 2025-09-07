document.addEventListener('DOMContentLoaded', () => {
    // Search functionality
    const mainSearch = document.getElementById('MainSearch');
    const mainSearchFrom = document.getElementById('MainSearchFrom');
    const searchResultsContainer = document.getElementById('searchResultsContainer');
    const initialSearchContent = document.getElementById('initialSearchContent');
    const searchResults = document.getElementById('searchResults');
    const recentSearches = document.getElementById('recentSearches');
    const popularSearches = document.getElementById('popularSearches');
    const clearSearchHistory = document.getElementById('clearSearchHistory');

    let searchTimeout;
    
    if (mainSearch) {
        
        mainSearch.addEventListener('focus', () => {
            console.log("mainSearch");
            overlay.classList.remove('hidden');
            MainSearchFrom.classList.add(...['changed-z', 'z-60']);
            searchResultsContainer.classList.remove('hidden');
            loadInitialSearchContent();

            // Scroll to search bar on mobile
            if (window.innerWidth <= 768) {
                const searchContainerTop = MainSearchFrom.getBoundingClientRect().top + window.scrollY;
                const targetScrollPosition = searchContainerTop - (window.innerHeight * 0.1); // 10vh from top
                window.scrollTo({
                    top: targetScrollPosition,
                    behavior: 'smooth'
                });

                // Adjust searchResultsContainer height for mobile
                adjustSearchResultsContainerHeight();
            }
        });

        mainSearch.addEventListener('input', (e) => {

            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length > 2) {
                searchTimeout = setTimeout(() => performSearch(query), 300);
            } else {
                initialSearchContent.classList.remove('hidden');
            }
        });
    }

    function addSearchChip(container, text) {
        const chip = document.createElement('div');
        chip.className = 'px-3 py-1 bg-gray-100 rounded-full text-sm text-gray-700 cursor-pointer hover:bg-gray-200';
        chip.textContent = text;
        chip.addEventListener('click', () => {
            mainSearch.value = text;
            performSearch(text);
        });
        container.appendChild(chip);
    }

    function loadInitialSearchContent() {
        // Load recent searches from localStorage
        const recentSearchItems = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        recentSearches.innerHTML = '';
        recentSearchItems.forEach(item => addSearchChip(recentSearches, item));

        // Load popular searches (you might want to fetch this from the server)
        const popularSearchItems = ['استعلام خلافی خورو', 'استعلام پلاک‌های ف��ال', 'محاسبه شبا'];
        popularSearches.innerHTML = '';
        popularSearchItems.forEach(item => addSearchChip(popularSearches, item));

    }

    function showSearchResults(results) {
        searchResults.innerHTML = '';

        if (results.length === 0) {
            searchResults.innerHTML = `
                <div class="flex flex-col items-center justify-center p-8">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-600 text-lg font-semibold">نتیجه‌ای یافت نشد!</p>
                </div>
            `;
        } else {
            // Create and append result elements
            results.forEach(result => {
                const resultElement = document.createElement('div');
                resultElement.className = 'p-4 border-b last:border-b-0 hover:bg-gray-50';
                resultElement.innerHTML = `
                    <h3 class="text-lg font-semibold">${result.title}</h3>
                    <p class="text-gray-600">${result.description}</p>
                `;
                searchResults.appendChild(resultElement);
            });
        }

        initialSearchContent.classList.add('hidden');
        searchResults.classList.remove('hidden');
    }

    function performSearch(query) {
        // Add to recent searches
        let recentSearchItems = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        recentSearchItems = [query, ...recentSearchItems.filter(item => item !== query)].slice(0, 5);
        localStorage.setItem('recentSearches', JSON.stringify(recentSearchItems));

        // Fetch search results from the server
        fetch(`/api/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                showSearchResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                showSearchResults([]);
            });
    }
    if (clearSearchHistory) {
        clearSearchHistory.addEventListener('click', () => {
            localStorage.removeItem('recentSearches');
            loadInitialSearchContent();
        });
    }

    // Initialize search content
    loadInitialSearchContent();

    // Slider functionality (placeholder)
    function initializeSlider() {
        // Add your slider initialization code here
        console.log('Slider initialized');
    }

    // Tabs functionality (placeholder)
    function initializeTabs() {
        // Add your tabs initialization code here
        console.log('Tabs initialized');
    }

    // Modal functionality (placeholder)
    function initializeModals() {
        // Add your modal initialization code here
        console.log('Modals initialized');
    }

    // Form validation (placeholder)
    function initializeFormValidation() {
        // Add your form validation code here
        console.log('Form validation initialized');
    }

    // Initialize all components
    initializeSlider();
    initializeTabs();
    initializeModals();
    initializeFormValidation();

    // Responsive menu toggle (placeholder)
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Scroll to top button (placeholder)
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');

    if (scrollToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 100) {
                scrollToTopBtn.classList.remove('hidden');
            } else {
                scrollToTopBtn.classList.add('hidden');
            }
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Add any other home-specific JavaScript functionality here

    // Function to adjust searchResultsContainer height
    function adjustSearchResultsContainerHeight() {
        if (window.innerWidth <= 768 && !searchResultsContainer.classList.contains('hidden')) {
            const availableHeight = window.innerHeight - (window.innerHeight * 0.1) - MainSearchFrom.offsetHeight;
            const minHeight = Math.min(availableHeight, window.innerHeight * 0.4); // Max 50vh
            searchResultsContainer.style.minHeight = `${minHeight}px`;
            searchResultsContainer.style.overflowY = 'auto';
        } else {
            searchResultsContainer.style.maxHeight = ''; // Reset for larger screens
            searchResultsContainer.style.overflowY = '';
        }
    }

    // Add a resize event listener to adjust the searchResultsContainer height on window resize
    window.addEventListener('resize', adjustSearchResultsContainerHeight);
});