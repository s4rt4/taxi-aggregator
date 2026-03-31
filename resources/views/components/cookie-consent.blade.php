<div id="cookie-consent" class="fixed-bottom bg-dark text-white p-3" style="z-index:9999; display:none;" x-data="cookieConsent()" x-show="!accepted" x-transition>
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
        <p class="mb-0 small">
            We use cookies to improve your experience. By continuing to use this site, you agree to our
            <a href="{{ route('cookie-policy') }}" class="text-warning">Cookie Policy</a>.
        </p>
        <div class="d-flex gap-2">
            <button @click="acceptAll()" class="btn btn-warning btn-sm">Accept All</button>
            <button @click="acceptEssential()" class="btn btn-outline-light btn-sm">Essential Only</button>
        </div>
    </div>
</div>
