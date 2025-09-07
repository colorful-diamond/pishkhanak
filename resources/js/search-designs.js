// Search Designs JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Common search functionality for all designs
    const searchInputs = document.querySelectorAll('input[type="search"]');
    const sampleSuggestions = [
        'استعلام خلافی خودرو',
        'محاسبه شبا',
        'استعلام مالیاتی',
        'استعلام وضعیت چک',
        'استعلام جواز کسب',
        'استعلام سهام عدالت',
        'استعلام بیمه',
        'محاسبه قسط وام'
    ];

    searchInputs.forEach(input => {
        const designNumber = input.id.replace('search', '');
        const suggestionsContainer = document.querySelector(`.search-suggestions-${designNumber}`);
        
        if (suggestionsContainer) {
            // Show suggestions on focus
            input.addEventListener('focus', () => {
                suggestionsContainer.classList.remove('hidden');
                
                // Add typing animation
                input.classList.add('animate-pulse');
                setTimeout(() => {
                    input.classList.remove('animate-pulse');
                }, 300);
            });

            // Hide suggestions on blur (with delay for clicks)
            input.addEventListener('blur', () => {
                setTimeout(() => {
                    suggestionsContainer.classList.add('hidden');
                }, 200);
            });

            // Filter suggestions on input
            input.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const suggestions = suggestionsContainer.querySelectorAll('.search-suggestion');
                
                suggestions.forEach(suggestion => {
                    const text = suggestion.textContent.toLowerCase();
                    if (text.includes(query) || query === '') {
                        suggestion.style.display = 'block';
                    } else {
                        suggestion.style.display = 'none';
                    }
                });

                // Show suggestions if there's input
                if (query.length > 0) {
                    suggestionsContainer.classList.remove('hidden');
                }
            });

            // Handle suggestion clicks
            suggestionsContainer.addEventListener('click', (e) => {
                const suggestion = e.target.closest('.search-suggestion');
                if (suggestion) {
                    const text = suggestion.textContent.trim();
                    input.value = text;
                    suggestionsContainer.classList.add('hidden');
                    
                    // Add visual feedback
                    suggestion.classList.add('bg-yellow-100');
                    setTimeout(() => {
                        suggestion.classList.remove('bg-yellow-100');
                    }, 300);
                    
                    // Simulate search
                    console.log(`Searching for: ${text}`);
                    performSearch(text);
                }
            });
        }
    });

    // Special functionality for specific designs

    // Design 5: Tab functionality
    const searchTabs = document.querySelectorAll('.search-tab');
    searchTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            searchTabs.forEach(t => {
                t.classList.remove('active', 'bg-sky-100');
                t.classList.add('hover:bg-sky-100');
            });
            tab.classList.add('active', 'bg-sky-100');
            tab.classList.remove('hover:bg-sky-100');
            
            // Update placeholder based on selected tab
            const searchInput = document.getElementById('search5');
            const tabText = tab.textContent;
            searchInput.placeholder = `جستجو در بخش "${tabText}"...`;
        });
    });

    // Design 6: Voice search simulation
    const voiceSearchBtn = document.querySelector('.voice-search-btn');
    if (voiceSearchBtn) {
        voiceSearchBtn.addEventListener('click', () => {
            const input = document.getElementById('search6');
            voiceSearchBtn.classList.add('animate-pulse');
            voiceSearchBtn.innerHTML = `
                <svg class="w-4 h-4 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            `;
            
            setTimeout(() => {
                input.value = 'استعلام خلافی خودرو';
                voiceSearchBtn.classList.remove('animate-pulse');
                voiceSearchBtn.innerHTML = `
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                `;
                
                // Show suggestions
                const suggestionsContainer = document.querySelector('.search-suggestions-6');
                if (suggestionsContainer) {
                    suggestionsContainer.classList.remove('hidden');
                }
                
                showNotification('جستجوی صوتی شبیه‌سازی شد!', 'success');
            }, 2000);
        });
    }

    // Design 8: Quick actions
    const quickActions = document.querySelectorAll('.quick-action');
    quickActions.forEach(action => {
        action.addEventListener('click', () => {
            const actionText = action.querySelector('div:last-child').textContent;
            const searchInput = document.getElementById('search8');
            
            const actionMap = {
                'خلافی': 'استعلام خلافی خودرو',
                'شبا': 'محاسبه شبا',
                'مالیات': 'استعلام مالیاتی',
                'چک': 'استعلام وضعیت چک'
            };
            
            // Add click animation
            action.classList.add('scale-95');
            setTimeout(() => {
                action.classList.remove('scale-95');
            }, 150);
            
            searchInput.value = actionMap[actionText] || actionText;
            
            // Show suggestions
            const suggestionsContainer = document.querySelector('.search-suggestions-8');
            if (suggestionsContainer) {
                suggestionsContainer.classList.remove('hidden');
            }
            
            performSearch(actionMap[actionText] || actionText);
        });
    });

    // Design 10: Quick tags
    const quickTags = document.querySelectorAll('.quick-tag');
    quickTags.forEach(tag => {
        tag.addEventListener('click', () => {
            const tagText = tag.textContent;
            const searchInput = document.getElementById('search10');
            
            const tagMap = {
                'خلافی': 'استعلام خلافی خودرو',
                'شبا': 'محاسبه شبا',
                'مالیات': 'استعلام مالیاتی',
                'چک': 'استعلام وضعیت چک'
            };
            
            // Add selection animation
            quickTags.forEach(t => t.classList.remove('ring-2', 'ring-yellow-400'));
            tag.classList.add('ring-2', 'ring-yellow-400');
            
            searchInput.value = tagMap[tagText] || tagText;
            
            // Show suggestions
            const suggestionsContainer = document.querySelector('.search-suggestions-10');
            if (suggestionsContainer) {
                suggestionsContainer.classList.remove('hidden');
            }
            
            performSearch(tagMap[tagText] || tagText);
        });
    });

    // Auto-typing effect for demonstration
    function typeWriter(element, text, speed = 100) {
        let i = 0;
        element.value = '';
        
        function type() {
            if (i < text.length) {
                element.value += text.charAt(i);
                i++;
                setTimeout(type, speed);
                
                // Trigger input event for suggestions
                element.dispatchEvent(new Event('input'));
            }
        }
        
        type();
    }

    // Demonstrate auto-typing on page load
    setTimeout(() => {
        const firstInput = document.getElementById('search1');
        if (firstInput) {
            typeWriter(firstInput, 'استعلام خلافی خودرو', 150);
        }
    }, 1000);

    // Add more auto-typing demos for other designs
    setTimeout(() => {
        const search4 = document.getElementById('search4');
        if (search4) {
            typeWriter(search4, 'محاسبه شبا', 120);
        }
    }, 3000);

    setTimeout(() => {
        const search9 = document.getElementById('search9');
        if (search9) {
            typeWriter(search9, 'استعلام مالیاتی', 130);
        }
    }, 5000);

    // Utility functions
    function performSearch(query) {
        console.log(`Performing search for: ${query}`);
        showNotification(`جستجو برای: ${query}`, 'info');
        
        // Here you would typically make an API call
        // For demo purposes, we'll just show a notification
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        // Set notification style based on type
        switch (type) {
            case 'success':
                notification.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-200');
                break;
            case 'error':
                notification.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-200');
                break;
            case 'warning':
                notification.classList.add('bg-yellow-100', 'text-yellow-800', 'border', 'border-yellow-200');
                break;
            default:
                notification.classList.add('bg-sky-100', 'text-sky-800', 'border', 'border-sky-200');
        }
        
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="ml-2">${message}</span>
                <button class="close-notification text-current hover:text-opacity-70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            removeNotification(notification);
        }, 3000);
        
        // Handle close button
        notification.querySelector('.close-notification').addEventListener('click', () => {
            removeNotification(notification);
        });
    }

    function removeNotification(notification) {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    // Add keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Hide all suggestions
            document.querySelectorAll('[class*="search-suggestions-"]').forEach(container => {
                container.classList.add('hidden');
            });
        }
    });

    // Add responsive behavior
    function handleResize() {
        const isMobile = window.innerWidth < 768;
        
        // Adjust search containers for mobile
        document.querySelectorAll('[class*="search-suggestions-"]').forEach(container => {
            if (isMobile) {
                container.classList.add('left-0', 'right-0');
                container.style.width = '100%';
            } else {
                container.classList.remove('left-0', 'right-0');
                container.style.width = '';
            }
        });
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial call
});

// Export for use in other files if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        typeWriter,
        performSearch,
        showNotification
    };
} 