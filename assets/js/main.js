/**
 * Playlist Manager - Optimized JavaScript
 * Main application functionality and utilities
 */

(function() {
    'use strict';

    // ===================================
    // CONFIGURATION
    // ===================================
    
    const CONFIG = {
        API_ENDPOINTS: {
            LOGIN: '/script/login.php',
            SIGNUP: '/script/signup.php',
            LOGOUT: '/script/logout.php',
            UPDATE_SETTINGS: '/script/update.php',
            STATISTICS: '/script/statistics.php'
        },
        ANIMATION_DURATION: 250,
        DEBOUNCE_DELAY: 300,
        RETRY_ATTEMPTS: 3,
        RETRY_DELAY: 1000
    };

    // ===================================
    // UTILITY FUNCTIONS
    // ===================================

    const Utils = {
        /**
         * Debounce function to limit API calls
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Throttle function for performance optimization
         */
        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        /**
         * Format time in seconds to MM:SS
         */
        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },

        /**
         * Format date for display
         */
        formatDate(date) {
            return new Intl.DateTimeFormat('default', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        },

        /**
         * Generate random number between min and max
         */
        random(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },

        /**
         * Check if element is in viewport
         */
        isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        },

        /**
         * Smooth scroll to element
         */
        scrollTo(element, offset = 0) {
            const targetPosition = element.offsetTop - offset;
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        },

        /**
         * Copy text to clipboard
         */
        async copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch (err) {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                return true;
            }
        },

        /**
         * Show notification
         */
        showNotification(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} animate-fade-in`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, duration);
        },

        /**
         * Validate email format
         */
        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        /**
         * Validate password strength
         */
        getPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            return {
                score: strength,
                label: strength < 2 ? 'Weak' : strength < 4 ? 'Medium' : 'Strong',
                color: strength < 2 ? '#dc2626' : strength < 4 ? '#d97706' : '#059669'
            };
        }
    };

    // ===================================
    // API HANDLER
    // ===================================

    const API = {
        /**
         * Make API request with retry logic
         */
        async request(url, options = {}, retries = CONFIG.RETRY_ATTEMPTS) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            };

            const finalOptions = { ...defaultOptions, ...options };

            try {
                const response = await fetch(url, finalOptions);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return await response.json();
                }
                
                return await response.text();
            } catch (error) {
                if (retries > 0) {
                    await new Promise(resolve => setTimeout(resolve, CONFIG.RETRY_DELAY));
                    return this.request(url, options, retries - 1);
                }
                throw error;
            }
        },

        /**
         * Login user
         */
        async login(credentials) {
            return this.request(CONFIG.API_ENDPOINTS.LOGIN, {
                method: 'POST',
                body: JSON.stringify(credentials)
            });
        },

        /**
         * Register user
         */
        async signup(userData) {
            return this.request(CONFIG.API_ENDPOINTS.SIGNUP, {
                method: 'POST',
                body: JSON.stringify(userData)
            });
        },

        /**
         * Update user settings
         */
        async updateSettings(settings) {
            return this.request(CONFIG.API_ENDPOINTS.UPDATE_SETTINGS, {
                method: 'POST',
                body: JSON.stringify(settings)
            });
        },

        /**
         * Get user statistics
         */
        async getStatistics() {
            return this.request(CONFIG.API_ENDPOINTS.STATISTICS);
        }
    };

    // ===================================
    // UI COMPONENTS
    // ===================================

    const UI = {
        /**
         * Initialize sidebar functionality
         */
        initSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('sidebar-toggle');
            
            if (!sidebar || !mainContent || !toggleBtn) return;

            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });

            // Restore sidebar state
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }

            // Mobile sidebar handling
            if (window.innerWidth <= 1024) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        },

        /**
         * Initialize form validation
         */
        initFormValidation() {
            const forms = document.querySelectorAll('form[data-validate]');
            
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, select, textarea');
                
                inputs.forEach(input => {
                    // Real-time validation
                    input.addEventListener('blur', () => this.validateField(input));
                    input.addEventListener('input', Utils.debounce(() => this.validateField(input), 300));
                });

                // Form submission validation
                form.addEventListener('submit', (e) => {
                    if (!this.validateForm(form)) {
                        e.preventDefault();
                    }
                });
            });
        },

        /**
         * Validate individual field
         */
        validateField(field) {
            const value = field.value.trim();
            const type = field.type;
            const required = field.hasAttribute('required');
            const minLength = field.getAttribute('minlength');
            const maxLength = field.getAttribute('maxlength');
            const pattern = field.getAttribute('pattern');

            // Remove existing error
            this.removeFieldError(field);

            // Required validation
            if (required && !value) {
                this.showFieldError(field, 'This field is required');
                return false;
            }

            // Length validation
            if (minLength && value.length < parseInt(minLength)) {
                this.showFieldError(field, `Minimum ${minLength} characters required`);
                return false;
            }

            if (maxLength && value.length > parseInt(maxLength)) {
                this.showFieldError(field, `Maximum ${maxLength} characters allowed`);
                return false;
            }

            // Type-specific validation
            if (value) {
                switch (type) {
                    case 'email':
                        if (!Utils.isValidEmail(value)) {
                            this.showFieldError(field, 'Please enter a valid email address');
                            return false;
                        }
                        break;
                    case 'password':
                        const strength = Utils.getPasswordStrength(value);
                        this.updatePasswordStrength(field, strength);
                        break;
                }

                // Pattern validation
                if (pattern && !new RegExp(pattern).test(value)) {
                    this.showFieldError(field, field.getAttribute('data-error') || 'Invalid format');
                    return false;
                }
            }

            return true;
        },

        /**
         * Validate entire form
         */
        validateForm(form) {
            const fields = form.querySelectorAll('input, select, textarea');
            let isValid = true;

            fields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            return isValid;
        },

        /**
         * Show field error
         */
        showFieldError(field, message) {
            this.removeFieldError(field);
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'form-error';
            errorDiv.textContent = message;
            
            field.parentNode.appendChild(errorDiv);
            field.classList.add('error');
        },

        /**
         * Remove field error
         */
        removeFieldError(field) {
            const errorDiv = field.parentNode.querySelector('.form-error');
            if (errorDiv) {
                errorDiv.remove();
            }
            field.classList.remove('error');
        },

        /**
         * Update password strength indicator
         */
        updatePasswordStrength(field, strength) {
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            
            if (strengthBar && strengthText) {
                strengthBar.style.width = `${(strength.score / 5) * 100}%`;
                strengthBar.style.backgroundColor = strength.color;
                strengthText.innerHTML = `Password strength: <span class="font-medium">${strength.label}</span>`;
            }
        },

        /**
         * Initialize password toggle
         */
        initPasswordToggle() {
            const passwordToggles = document.querySelectorAll('.password-toggle');
            
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const input = toggle.previousElementSibling;
                    const icon = toggle.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
        },

        /**
         * Initialize language switcher
         */
        initLanguageSwitcher() {
            const languageSwitcher = document.querySelector('.language-switcher');
            if (!languageSwitcher) return;

            const dropdown = languageSwitcher.querySelector('.dropdown');
            const toggle = languageSwitcher.querySelector('.toggle');

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', () => {
                dropdown.classList.remove('show');
            });

            // Language selection
            const languageOptions = languageSwitcher.querySelectorAll('.language-option');
            languageOptions.forEach(option => {
                option.addEventListener('click', (e) => {
                    e.preventDefault();
                    const lang = option.getAttribute('data-lang');
                    this.changeLanguage(lang);
                });
            });
        },

        /**
         * Change application language
         */
        changeLanguage(lang) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('lang', lang);
            window.location.href = currentUrl.toString();
        },

        /**
         * Initialize loading states
         */
        initLoadingStates() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', () => {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Loading...';
                    }
                });
            });
        },

        /**
         * Initialize tooltips
         */
        initTooltips() {
            const tooltipElements = document.querySelectorAll('[data-tooltip]');
            
            tooltipElements.forEach(element => {
                element.addEventListener('mouseenter', (e) => {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip';
                    tooltip.textContent = element.getAttribute('data-tooltip');
                    
                    document.body.appendChild(tooltip);
                    
                    const rect = element.getBoundingClientRect();
                    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
                });
                
                element.addEventListener('mouseleave', () => {
                    const tooltip = document.querySelector('.tooltip');
                    if (tooltip) tooltip.remove();
                });
            });
        },

        /**
         * Initialize lazy loading
         */
        initLazyLoading() {
            const lazyElements = document.querySelectorAll('[data-lazy]');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const src = element.getAttribute('data-lazy');
                        
                        if (element.tagName === 'IMG') {
                            element.src = src;
                        } else {
                            element.style.backgroundImage = `url(${src})`;
                        }
                        
                        element.removeAttribute('data-lazy');
                        observer.unobserve(element);
                    }
                });
            });
            
            lazyElements.forEach(element => observer.observe(element));
        }
    };

    // ===================================
    // MUSIC PLAYER CONTROLS
    // ===================================

    const MusicPlayer = {
        /**
         * Initialize music player functionality
         */
        init() {
            this.initSpotifyPlayer();
            this.initAppleMusicPlayer();
            this.initYouTubePlayer();
            this.initAmazonPlayer();
        },

        /**
         * Initialize Spotify player
         */
        initSpotifyPlayer() {
            const spotifyPlayer = document.getElementById('spotify-player');
            if (!spotifyPlayer) return;

            // Spotify iframe API integration
            window.onSpotifyIframeApiReady = (IFrameAPI) => {
                const element = document.getElementById('spotify-iframe');
                const options = {
                    uri: 'spotify:playlist:37i9dQZF1DXcBWIGoYBM5M'
                };
                const callback = (EmbedController) => {
                    EmbedController.addListener('ready', () => {
                        console.log('Spotify player ready');
                    });
                };
                IFrameAPI.createController(element, options, callback);
            };
        },

        /**
         * Initialize Apple Music player
         */
        initAppleMusicPlayer() {
            const applePlayer = document.getElementById('apple-player');
            if (!applePlayer) return;

            // Apple MusicKit integration
            if (window.MusicKit) {
                const music = window.MusicKit.getInstance();
                
                music.addEventListener('playbackStateDidChange', (event) => {
                    this.updatePlayerStatus('apple', event.state);
                });
            }
        },

        /**
         * Initialize YouTube player
         */
        initYouTubePlayer() {
            const youtubePlayer = document.getElementById('youtube-player');
            if (!youtubePlayer) return;

            // YouTube IFrame API integration
            if (window.YT && window.YT.Player) {
                new window.YT.Player('youtube-iframe', {
                    height: '360',
                    width: '640',
                    videoId: 'dQw4w9WgXcQ',
                    events: {
                        'onStateChange': (event) => {
                            this.updatePlayerStatus('youtube', event.data);
                        }
                    }
                });
            }
        },

        /**
         * Initialize Amazon player
         */
        initAmazonPlayer() {
            const amazonPlayer = document.getElementById('amazon-player');
            if (!amazonPlayer) return;

            // Amazon Music integration (limited due to API restrictions)
            console.log('Amazon Music player initialized (manual control mode)');
        },

        /**
         * Update player status
         */
        updatePlayerStatus(platform, status) {
            const statusElement = document.getElementById(`${platform}-status`);
            if (statusElement) {
                statusElement.textContent = this.getStatusText(platform, status);
                statusElement.className = `status-${this.getStatusClass(status)}`;
            }
        },

        /**
         * Get status text
         */
        getStatusText(platform, status) {
            const statusMap = {
                spotify: {
                    'ready': 'Ready',
                    'playing': 'Playing',
                    'paused': 'Paused',
                    'stopped': 'Stopped'
                },
                apple: {
                    'none': 'Stopped',
                    'loading': 'Loading',
                    'playing': 'Playing',
                    'paused': 'Paused',
                    'stopped': 'Stopped'
                },
                youtube: {
                    '-1': 'Unstarted',
                    '0': 'Ended',
                    '1': 'Playing',
                    '2': 'Paused',
                    '3': 'Buffering',
                    '5': 'Video cued'
                }
            };

            return statusMap[platform]?.[status] || 'Unknown';
        },

        /**
         * Get status class
         */
        getStatusClass(status) {
            if (status === 'playing' || status === 1) return 'playing';
            if (status === 'paused' || status === 2) return 'paused';
            if (status === 'loading' || status === 3) return 'loading';
            return 'stopped';
        }
    };

    // ===================================
    // STATISTICS & ANALYTICS
    // ===================================

    const Analytics = {
        /**
         * Initialize analytics
         */
        init() {
            this.initCharts();
            this.initRealTimeUpdates();
        },

        /**
         * Initialize charts
         */
        initCharts() {
            const chartElements = document.querySelectorAll('[data-chart]');
            
            chartElements.forEach(element => {
                const type = element.getAttribute('data-chart');
                const data = JSON.parse(element.getAttribute('data-chart-data') || '{}');
                
                this.createChart(element, type, data);
            });
        },

        /**
         * Create chart
         */
        createChart(element, type, data) {
            if (typeof Chart === 'undefined') return;

            const ctx = element.getContext('2d');
            new Chart(ctx, {
                type: type,
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },

        /**
         * Initialize real-time updates
         */
        initRealTimeUpdates() {
            setInterval(() => {
                this.updateStatistics();
            }, 30000); // Update every 30 seconds
        },

        /**
         * Update statistics
         */
        async updateStatistics() {
            try {
                const stats = await API.getStatistics();
                this.updateStatsDisplay(stats);
            } catch (error) {
                console.error('Failed to update statistics:', error);
            }
        },

        /**
         * Update statistics display
         */
        updateStatsDisplay(stats) {
            Object.keys(stats).forEach(key => {
                const element = document.getElementById(`stat-${key}`);
                if (element) {
                    element.textContent = stats[key];
                }
            });
        }
    };

    // ===================================
    // ERROR HANDLING
    // ===================================

    const ErrorHandler = {
        /**
         * Initialize error handling
         */
        init() {
            window.addEventListener('error', this.handleError.bind(this));
            window.addEventListener('unhandledrejection', this.handlePromiseRejection.bind(this));
        },

        /**
         * Handle JavaScript errors
         */
        handleError(event) {
            console.error('JavaScript Error:', event.error);
            this.logError('JavaScript Error', event.error.message, event.error.stack);
        },

        /**
         * Handle promise rejections
         */
        handlePromiseRejection(event) {
            console.error('Promise Rejection:', event.reason);
            this.logError('Promise Rejection', event.reason.message, event.reason.stack);
        },

        /**
         * Log error to server
         */
        async logError(type, message, stack) {
            try {
                await API.request('/script/log-error.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        type,
                        message,
                        stack,
                        url: window.location.href,
                        userAgent: navigator.userAgent,
                        timestamp: new Date().toISOString()
                    })
                });
            } catch (error) {
                console.error('Failed to log error:', error);
            }
        }
    };

    // ===================================
    // PERFORMANCE OPTIMIZATION
    // ===================================

    const Performance = {
        /**
         * Initialize performance optimizations
         */
        init() {
            this.initIntersectionObserver();
            this.initResizeObserver();
            this.initScrollOptimization();
        },

        /**
         * Initialize intersection observer for lazy loading
         */
        initIntersectionObserver() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '50px'
            });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        },

        /**
         * Initialize resize observer
         */
        initResizeObserver() {
            const resizeObserver = new ResizeObserver(Utils.debounce(() => {
                this.handleResize();
            }, 100));

            resizeObserver.observe(document.body);
        },

        /**
         * Handle window resize
         */
        handleResize() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (window.innerWidth <= 1024) {
                sidebar?.classList.add('collapsed');
                mainContent?.classList.add('expanded');
            }
        },

        /**
         * Initialize scroll optimization
         */
        initScrollOptimization() {
            let ticking = false;

            function updateScroll() {
                ticking = false;
                // Handle scroll-based animations here
            }

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(updateScroll);
                    ticking = true;
                }
            });
        }
    };

    // ===================================
    // INITIALIZATION
    // ===================================

    const App = {
        /**
         * Initialize the application
         */
        init() {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', this.start.bind(this));
            } else {
                this.start();
            }
        },

        /**
         * Start the application
         */
        start() {
            try {
                // Initialize all modules
                ErrorHandler.init();
                Performance.init();
                UI.initSidebar();
                UI.initFormValidation();
                UI.initPasswordToggle();
                UI.initLanguageSwitcher();
                UI.initLoadingStates();
                UI.initTooltips();
                UI.initLazyLoading();
                MusicPlayer.init();
                Analytics.init();

                // Add global event listeners
                this.addGlobalEventListeners();

                console.log('Playlist Manager initialized successfully');
            } catch (error) {
                console.error('Failed to initialize application:', error);
                ErrorHandler.handleError({ error });
            }
        },

        /**
         * Add global event listeners
         */
        addGlobalEventListeners() {
            // Handle keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + K for search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    this.focusSearch();
                }
                
                // Escape to close modals/dropdowns
                if (e.key === 'Escape') {
                    this.closeAllDropdowns();
                }
            });

            // Handle service worker updates
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    window.location.reload();
                });
            }
        },

        /**
         * Focus search input
         */
        focusSearch() {
            const searchInput = document.querySelector('input[type="search"], .search-input');
            if (searchInput) {
                searchInput.focus();
            }
        },

        /**
         * Close all dropdowns
         */
        closeAllDropdowns() {
            document.querySelectorAll('.dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    };

    // ===================================
    // EXPOSE TO GLOBAL SCOPE
    // ===================================

    window.PlaylistManager = {
        Utils,
        API,
        UI,
        MusicPlayer,
        Analytics,
        ErrorHandler,
        Performance,
        CONFIG
    };

    // ===================================
    // START APPLICATION
    // ===================================

    App.init();

})();

