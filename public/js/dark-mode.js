// 🌙 Dark Mode Toggle Script for SEMOP

(function() {
    'use strict';
    
    // Get elements
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    const icon = darkModeToggle?.querySelector('i');
    
    // Check if dark mode toggle exists
    if (!darkModeToggle) {
        console.warn('Dark mode toggle button not found');
        return;
    }
    
    // Check for saved dark mode preference
    const isDarkMode = localStorage.getItem('darkMode') === 'enabled';
    
    // Apply saved preference on page load
    if (isDarkMode) {
        enableDarkMode();
    }
    
    // Toggle dark mode on button click
    darkModeToggle.addEventListener('click', function() {
        const currentMode = localStorage.getItem('darkMode');
        
        if (currentMode === 'enabled') {
            disableDarkMode();
        } else {
            enableDarkMode();
        }
    });
    
    // Enable dark mode
    function enableDarkMode() {
        body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
        
        // Change icon to sun
        if (icon) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
        
        // Update button title
        darkModeToggle.setAttribute('title', 'تبديل للوضع النهاري');
        
        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('darkModeChanged', { 
            detail: { enabled: true } 
        }));
    }
    
    // Disable dark mode
    function disableDarkMode() {
        body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
        
        // Change icon to moon
        if (icon) {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
        
        // Update button title
        darkModeToggle.setAttribute('title', 'تبديل للوضع الليلي');
        
        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('darkModeChanged', { 
            detail: { enabled: false } 
        }));
    }
    
    // Listen for system dark mode changes
    if (window.matchMedia) {
        const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        darkModeMediaQuery.addEventListener('change', (e) => {
            // Only apply if user hasn't set a preference
            if (!localStorage.getItem('darkMode')) {
                if (e.matches) {
                    enableDarkMode();
                } else {
                    disableDarkMode();
                }
            }
        });
    }
    
    // Add smooth transition on first load
    setTimeout(() => {
        body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
    }, 100);
    
})();

// Export functions for external use
window.SEMOP = window.SEMOP || {};
window.SEMOP.darkMode = {
    isEnabled: function() {
        return localStorage.getItem('darkMode') === 'enabled';
    },
    enable: function() {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
    },
    disable: function() {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
    },
    toggle: function() {
        const toggle = document.getElementById('darkModeToggle');
        if (toggle) {
            toggle.click();
        }
    }
};

console.log('🌙 Dark Mode initialized successfully');
