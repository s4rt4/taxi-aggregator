@extends('layouts.admin')
@section('title', 'Dispute ' . ($dispute->reference ?? 'DSP-' . $dispute->id))

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.disputes.index') }}">Disputes</a></li>
    <li class="breadcrumb-item active">{{ $dispute->reference ?? 'DSP-' . $dispute->id }}</li>
</ol>
@endsection

@section('content')
@php
    $disputeStatusColors = ['open' => 'danger', 'investigating' => 'warning', 'resolved' => 'success', 'closed' => 'secondary'];
@endphp

{{-- Dispute Header --}}
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="mb-0">{{ $dispute->reference ?? 'DSP-' . $dispute->id }}</h1>
            <span class="badge bg-{{ $disputeStatusColors[$dispute->status] ?? 'secondary' }} fs-6">
                {{ ucfirst($dispute->status) }}
            </span>
            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $dispute->type ?? '-')) }}</span>
        </div>
        <p class="text-muted mb-0">
            Raised by {{ $dispute->raisedBy->name ?? '-' }} ({{ ucfirst($dispute->raised_by_role ?? '') }})
            on {{ $dispute->created_at->format('d M Y H:i') }}
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        {{-- Description --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Description</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $dispute->description }}</p>
            </div>
        </div>

        {{-- Message Thread --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Message Thread</h6>
            </div>
            <div class="card-body">
                @if($dispute->messages->isEmpty())
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-chat-dots fs-2 d-block mb-2"></i>
                        No messages yet.
                    </div>
                @else
                    @foreach($dispute->messages as $message)
                        @php
                            $isAdmin = $message->user && $message->user->role === 'admin';
                        @endphp
                        <div class="d-flex mb-3 {{ $isAdmin ? 'justify-content-end' : '' }}">
                            <div class="p-3 rounded-3 {{ $isAdmin ? 'bg-primary text-white' : 'bg-light' }}" style="max-width:75%;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-semibold">
                                        {{ $message->user->name ?? 'System' }}
                                        @if($message->is_internal)
                                            <span class="badge bg-warning text-dark ms-1">Internal</span>
                                        @endif
                                    </small>
                                    <small class="{{ $isAdmin ? 'text-white-50' : 'text-muted' }} ms-2">
                                        {{ $message->created_at->format('d M H:i') }}
                                    </small>
                                </div>
                                <div>{{ $message->message }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Add Message Form --}}
                <hr>
                <form method="POST" action="{{ route('admin.disputes.add-message', $dispute) }}">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" class="form-control" rows="3" placeholder="Type a message..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" name="is_internal" value="1" class="form-check-input" id="internalCheck">
                            <label class="form-check-label" for="internalCheck">
                                Internal note (not visible to customer)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Resolution --}}
        @if($dispute->status !== 'resolved' && $dispute->status !== 'closed')
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0">Resolve Dispute</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Resolution Type</label>
                                <select name="resolution" class="form-select" required>
                                    <option value="">Select resolution...</option>
                                    <option value="refund">Full/Partial Refund</option>
                                    <option value="credit">Account Credit</option>
                                    <option value="no_action">No Action Required</option>
                                    <option value="warning">Warning to Operator</option>
                                    <option value="suspend">Suspend Operator</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Refund Amount (if applicable)</label>
                                <div class="input-group">
                                    <span class="input-group-text">&pound;</span>
                                    <input type="number" name="refund_amount" class="form-control" step="0.01" min="0"
                                        value="{{ $dispute->booking?->total_price ?? '' }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Resolution Notes</label>
                                <textarea name="resolution_notes" class="form-control" rows="3" required
                                    placeholder="Describe the resolution..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Resolve this dispute?')">
                                    <i class="bi bi-check-circle me-1"></i> Resolve Dispute
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0">Resolution</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:30%">Resolution</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $dispute->resolution ?? '-')) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Notes</td>
                            <td>{{ $dispute->resolution_notes ?? '-' }}</td>
                        </tr>
                        @if($dispute->refund_amount)
                            <tr>
                                <td class="text-muted">Refund Amount</td>
                                <td>&pound;{{ number_format($dispute->refund_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Resolved By</td>
                            <td>{{ $dispute->resolvedBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Resolved At</td>
                            <td>{{ $dispute->resolved_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-md-4">
        {{-- Booking Details --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Related Booking</h6>
            </div>
            <div class="card-body">
                @if($dispute->booking)
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted">Reference</td>
                            <td>
                                <a href="{{ route('admin.bookings.show', $dispute->booking) }}">
                                    {{ $dispute->booking->reference }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td>{{ $dispute->booking->pickup_datetime?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Passenger</td>
                            <td>{{ $dispute->booking->passenger_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Operator</td>
                            <td>
                                @if($dispute->booking->operator)
                                    <a href="{{ route('admin.operators.show', $dispute->booking->operator) }}">
                                        {{ $dispute->booking->operator->operator_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Price</td>
                            <td class="fw-semibold">&pound;{{ number_format($dispute->booking->total_price, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @php
                                    $sc = ['pending'=>'warning','confirmed'=>'info','completed'=>'success','cancelled'=>'danger'];
                                @endphp
                                <span class="badge bg-{{ $sc[$dispute->booking->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $dispute->booking->status)) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                @else
                    <span class="text-muted">No booking linked.</span>
                @endif
            </div>
        </div>

        {{-- Dispute Info --}}
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0">Dispute Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted">Type</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $dispute->type ?? '-')) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $disputeStatusColors[$dispute->status] ?? 'secondary' }}">
                                {{ ucfirst($dispute->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Raised By</td>
                        <td>{{ $dispute->raisedBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Role</td>
                        <td>{{ ucfirst($dispute->raised_by_role ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Created</td>
                        <td>{{ $dispute->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Messages</td>
                        <td>{{ $dispute->messages->count() }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
