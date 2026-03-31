@auth
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="user-role" content="{{ auth()->user()->role }}">
@if(auth()->user()->isOperator() && auth()->user()->operator)
<meta name="operator-id" content="{{ auth()->user()->operator->id }}">
@endif

<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:11000;"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (!window.Echo) return;

    const userId = document.querySelector('meta[name="user-id"]')?.content;
    const userRole = document.querySelector('meta[name="user-role"]')?.content;

    if (userRole === 'operator') {
        // Listen for new bookings on the operator's private channel
        const operatorId = document.querySelector('meta[name="operator-id"]')?.content;
        if (operatorId) {
            window.Echo.private('operator.' + operatorId)
                .listen('NewBookingEvent', (e) => {
                    showToast('New Booking!', e.reference + ': ' + e.pickup + ' &rarr; ' + e.destination + ' - &pound;' + e.price, 'success');
                    // Play notification sound
                    new Audio('/sounds/notification.mp3').play().catch(function() {});
                });
        }
    }

    if (userRole === 'passenger') {
        window.Echo.private('passenger.' + userId)
            .listen('BookingStatusChanged', (e) => {
                showToast('Booking Update', e.reference + ' is now ' + e.status, 'info');
            });
    }

    function showToast(title, message, type) {
        type = type || 'info';
        var colors = { success: 'bg-success', info: 'bg-primary', warning: 'bg-warning', danger: 'bg-danger' };
        var container = document.getElementById('toast-container');
        var toast = document.createElement('div');
        toast.className = 'toast show';
        toast.setAttribute('role', 'alert');
        toast.innerHTML =
            '<div class="toast-header ' + (colors[type] || 'bg-primary') + ' text-white">' +
                '<i class="bi bi-bell me-2"></i>' +
                '<strong class="me-auto">' + title + '</strong>' +
                '<small>Just now</small>' +
                '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>' +
            '</div>' +
            '<div class="toast-body">' + message + '</div>';
        container.appendChild(toast);
        setTimeout(function() { toast.remove(); }, 8000);
    }
});
</script>
@endpush
@endauth
