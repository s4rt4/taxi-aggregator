<div class="text-center py-5">
    <i class="bi bi-journal-x display-3 text-muted"></i>
    <p class="text-muted mt-3">{{ $message ?? 'No bookings found.' }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-search me-1"></i> Search & Book
    </a>
</div>
