@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Notifications</h2>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge bg-primary fs-6">
                    {{ auth()->user()->unreadNotifications->count() }} unread
                </span>
            @else
                <span class="text-muted">All caught up!</span>
            @endif
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-check2-all me-1"></i> Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">You have no notifications yet.</p>
            </div>
        </div>
    @else
        <div class="list-group">
            @foreach($notifications as $notification)
                @php
                    $data = $notification->data;
                    $type = $data['type'] ?? 'general';
                    $isUnread = is_null($notification->read_at);

                    // Determine icon based on notification type
                    $icon = match($type) {
                        'booking_confirmed' => 'bi-check-circle-fill text-success',
                        'new_booking_received' => 'bi-journal-plus text-primary',
                        'booking_status_updated' => 'bi-arrow-repeat text-info',
                        'booking_cancelled' => 'bi-x-circle-fill text-danger',
                        'new_review_received' => 'bi-star-fill text-warning',
                        'payment_confirmed' => 'bi-credit-card-fill text-success',
                        'operator_approved' => 'bi-patch-check-fill text-success',
                        'operator_rejected' => 'bi-patch-exclamation-fill text-danger',
                        default => 'bi-bell-fill text-secondary',
                    };
                @endphp
                <div class="list-group-item {{ $isUnread ? 'list-group-item-light border-start border-primary border-3' : '' }}">
                    <div class="d-flex align-items-start">
                        <div class="me-3 mt-1">
                            <i class="bi {{ $icon }}" style="font-size: 1.4rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="mb-1 {{ $isUnread ? 'fw-semibold' : '' }}">
                                        {{ $data['message'] ?? 'You have a new notification.' }}
                                    </p>
                                    @if(isset($data['reference']))
                                        <small class="text-muted">
                                            Ref: {{ $data['reference'] }}
                                        </small>
                                    @endif
                                </div>
                                <div class="text-end ms-3 text-nowrap">
                                    <small class="text-muted" title="{{ $notification->created_at->format('d M Y H:i') }}">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                    @if($isUnread)
                                        <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link text-muted p-0 ms-2" title="Mark as read">
                                                <i class="bi bi-check2"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
