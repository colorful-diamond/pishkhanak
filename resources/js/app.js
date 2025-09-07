document.addEventListener('DOMContentLoaded', () => {

    const servicesMenu = document.querySelector('.services-menu');
    const servicesDropdown = document.querySelector('.services-dropdown');
    const overlay = document.getElementById('overlay');

    // Only add event listeners if elements exist (not on login page)
    if (servicesMenu && servicesDropdown && overlay) {
        servicesMenu.addEventListener('click', (e) => {
          e.preventDefault();
          servicesMenu.classList.toggle('active');
          servicesDropdown.classList.toggle('active');
          overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
          servicesMenu.classList.remove('active');
          servicesDropdown.classList.remove('active');
          overlay.classList.remove('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
          if (!servicesMenu.contains(e.target) && !servicesDropdown.contains(e.target)) {
            servicesMenu.classList.remove('active');
            servicesDropdown.classList.remove('active');
            overlay.classList.remove('active');
          }
        });
    }

    // Mobile Header Menu - handled by enhanced-sidebars.js

    function closeAISearchBar() {
        if (aiSearchBarDesktop && aiSearchBarMobile && overlay && header) {
            aiSearchBarDesktop.classList.add('hidden');
            aiSearchBarMobile.classList.add('hidden');
            overlay.classList.add('hidden');
            header.classList.remove('z-60');
        }
    }

    function closeOverlayCloser() {
        document.querySelectorAll('.overlay-closer').forEach(closer => {
            closer.classList.remove('z-60');
            closer.classList.add('hidden');
        });
        document.querySelectorAll('.changed-z').forEach(closer => {
            closer.classList.remove('changed-z');
            closer.classList.remove('z-60');
        });
    }

    // Hamburger button handled by enhanced-sidebars.js

    if (overlay) {
        overlay.addEventListener('click', () => {
            closeAISearchBar();
            closeOverlayCloser()
        });
    }

    // AI Search functionality
    const aiSearchFloatBtn = document.getElementById('aiSearchFloatBtn');
    const aiSearchBarDesktop = document.getElementById('aiSearchBarDesktop');
    const aiSearchBarMobile = document.getElementById('aiSearchBarMobile');
    const aiSearchInputs = document.querySelectorAll('.aiSearchInput');
    const aiVoiceSearchBtns = document.querySelectorAll('.aiVoiceSearchBtn');
    const aiSearchSubmitBtns = document.querySelectorAll('.aiSearchSubmitBtn');
    const aiSearchTimers = document.querySelectorAll('.aiSearchTimer');
    const aiSearchTimerTexts = document.querySelectorAll('.aiSearchTimerText');
    const aiSearchTimerDots = document.querySelectorAll('.aiSearchTimerDot');

    // Only initialize AI search if elements exist
    if (aiSearchFloatBtn || aiSearchBarDesktop || aiSearchBarMobile) {
        let recognition;
        let isListening = false;
        let timerInterval;
        let activeSearchBar;

        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.lang = 'fa-IR';

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                if (activeSearchBar) {
                    const input = activeSearchBar.querySelector('.aiSearchInput');
                    if (input) {
                        input.value = transcript;
                    }
                }
                stopVoiceRecognition();
            };

            recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                stopVoiceRecognition();
            };

            recognition.onend = () => {
                stopVoiceRecognition();
            };
        }

        function startVoiceRecognition(searchBar) {
            if (recognition && !isListening) {
                isListening = true;
                activeSearchBar = searchBar;
                recognition.start();
                startTimer(searchBar);
            }
        }

        function stopVoiceRecognition() {
            if (recognition && isListening) {
                isListening = false;
                recognition.stop();
                stopTimer(activeSearchBar);
            }
        }

        function startTimer(searchBar) {
            if (!searchBar) return;
            
            const timer = searchBar.querySelector('.aiSearchTimer');
            const timerText = searchBar.querySelector('.aiSearchTimerText');
            const timerDot = searchBar.querySelector('.aiSearchTimerDot');
            
            if (timer && timerText && timerDot) {
                timer.classList.remove('hidden');
                timerDot.classList.add('animate-pulse');
                
                let seconds = 0;
                timerInterval = setInterval(() => {
                    seconds++;
                    timerText.textContent = `${seconds}s`;
                    
                    if (seconds >= 30) {
                        stopVoiceRecognition();
                    }
                }, 1000);
            }
        }

        function stopTimer(searchBar) {
            if (!searchBar) return;
            
            const timer = searchBar.querySelector('.aiSearchTimer');
            const timerDot = searchBar.querySelector('.aiSearchTimerDot');
            
            if (timer && timerDot) {
                timer.classList.add('hidden');
                timerDot.classList.remove('animate-pulse');
            }
            
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }

        function toggleAiSearchBar() {
            if (aiSearchBarDesktop && aiSearchBarMobile && overlay && header) {
                const isDesktopHidden = aiSearchBarDesktop.classList.contains('hidden');
                const isMobileHidden = aiSearchBarMobile.classList.contains('hidden');
                
                if (isDesktopHidden && isMobileHidden) {
                    // Show search bars
                    aiSearchBarDesktop.classList.remove('hidden');
                    aiSearchBarMobile.classList.remove('hidden');
                    overlay.classList.remove('hidden');
                    header.classList.add('z-60');
                    
                    // Focus on the appropriate input
                    const isMobile = window.innerWidth < 768;
                    const targetInput = isMobile ? 
                        aiSearchBarMobile.querySelector('.aiSearchInput') : 
                        aiSearchBarDesktop.querySelector('.aiSearchInput');
                    
                    if (targetInput) {
                        setTimeout(() => targetInput.focus(), 100);
                    }
                } else {
                    // Hide search bars
                    closeAISearchBar();
                }
            }
        }

        function submitAiSearch(searchBar) {
            if (!searchBar) return;
            
            const input = searchBar.querySelector('.aiSearchInput');
            if (!input) return;
            
            const query = input.value.trim();
            if (!query) return;
            
            // Show loading state
            const submitBtn = searchBar.querySelector('.aiSearchSubmitBtn');
            if (submitBtn) {
                submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                submitBtn.disabled = true;
            }
            
            // Here you would typically send the query to your AI service
            // For now, we'll just simulate a search
            setTimeout(() => {
                console.log('AI Search query:', query);
                
                // Reset button
                if (submitBtn) {
                    submitBtn.innerHTML = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>';
                    submitBtn.disabled = false;
                }
                
                // Clear input and close search
                input.value = '';
                closeAISearchBar();
                
                // You could redirect to search results or show results in a modal
                // window.location.href = `/search?q=${encodeURIComponent(query)}`;
            }, 1000);
        }

        // Event listeners
        if (aiSearchFloatBtn) {
            aiSearchFloatBtn.addEventListener('click', toggleAiSearchBar);
        }

        aiVoiceSearchBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const searchBar = btn.closest('.ai-search-bar');
                if (isListening) {
                    stopVoiceRecognition();
                } else {
                    startVoiceRecognition(searchBar);
                }
            });
        });

        aiSearchSubmitBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const searchBar = btn.closest('.ai-search-bar');
                submitAiSearch(searchBar);
            });
        });

        aiSearchInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const searchBar = input.closest('.ai-search-bar');
                    submitAiSearch(searchBar);
                }
            });
        });
    }

    function loadSearchData() {
        // Load search data if needed
        console.log('Search data loaded');
    }

    // Initialize search data
    loadSearchData();

    // Bank data functionality
    if (typeof window.bankData !== 'undefined') {
        console.log('Bank data loaded:', window.bankData.length, 'banks');
    }

    // Initialize other components
    console.log('App initialized successfully');
});