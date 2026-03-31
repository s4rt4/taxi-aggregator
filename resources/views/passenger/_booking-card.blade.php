<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <div class="row align-items-center">
            {{-- Reference & Date --}}
            <div class="col-md-3">
                <span class="font-monospace fw-bold text-primary">{{ $booking->reference }}</span>
                <div class="small text-muted mt-1">
                    <i class="bi bi-calendar me-1"></i>{{ $booking->pickup_datetime->format('D, j M Y') }}
                </div>
                <div class="small text-muted">
                    <i class="bi bi-clock me-1"></i>{{ $booking->pickup_datetime->format('H:i') }}
                </div>
            </div>

            {{-- Route --}}
            <div class="col-md-4 my-2 my-md-0">
                <div class="small">
                    <i class="bi bi-geo-alt text-success me-1"></i>{{ Str::limit($booking->pickup_address, 35) }}
                </div>
                <div class="small mt-1">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ Str::limit($booking->destination_address, 35) }}
                </div>
                @if($booking->operator)
                    <div class="small text-muted mt-1">
                        <i class="bi bi-building me-1"></i>{{ $booking->operator->operator_name }}
                    </div>
                @endif
            </div>

            {{-- Status --}}
            <div class="col-md-2 text-center my-2 my-md-0">
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'accepted' => 'info',
                        'en_route' => 'primary',
                        'arrived' => 'primary',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'no_show' => 'dark',
                    ];
                    $color = $statusColors[$booking->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $color }}">
                    {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                </span>
            </div>

            {{-- Price & Actions --}}
            <div class="col-md-3 text-md-end">
                <div class="fw-bold fs-5">&pound;{{ number_format($booking->total_price, 2) }}</div>
                <div class="mt-2">
                    <a href="{{ route('passenger.booking-detail', $booking) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                    @if($booking->status === 'completed' && !$booking->review)
                        <a href="{{ route('passenger.booking-detail', $booking) }}#review" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-star me-1"></i>Review
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
