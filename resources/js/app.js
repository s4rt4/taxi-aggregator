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

// Laravel Echo for real-time WebSocket via Reverb
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

if (document.querySelector('meta[name="user-id"]')) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}
