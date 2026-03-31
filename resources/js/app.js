import './bootstrap';

// Bootstrap JS
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Alpine.js
import Alpine from 'alpinejs';

// Cookie Consent component
Alpine.data('cookieConsent', () => ({
    accepted: localStorage.getItem('cookie_consent') !== null,

    acceptAll() {
        localStorage.setItem('cookie_consent', 'all');
        this.accepted = true;
        this.loadAnalytics();
    },

    acceptEssential() {
        localStorage.setItem('cookie_consent', 'essential');
        this.accepted = true;
    },

    loadAnalytics() {
        // Load Google Analytics or other analytics scripts here
        // when the user accepts all cookies
    },

    init() {
        if (localStorage.getItem('cookie_consent') === 'all') {
            this.loadAnalytics();
        }
    }
}));

window.Alpine = Alpine;
Alpine.start();
