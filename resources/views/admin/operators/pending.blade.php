@extends('layouts.admin')
@section('title', 'Pending Operator Approvals')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.operators.index') }}">Operators</a></li>
    <li class="breadcrumb-item active">Pending Approval</li>
</ol>
@endsection

@section('content')
<div class="page-header">
    <h1>Pending Operator Approvals</h1>
    <p class="text-muted mb-0">Review and approve new operator applications.</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Operator</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Licence Authority</th>
                    <th>Fleet Size</th>
                    <th>Applied</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($operators as $operator)
                    <tr>
                        <td>
                            <div class="fw-semibold">
                                {{ $operator->operator_name }}
                                @switch($operator->business_type)
                                    @case('sole_trader') <span class="badge bg-info ms-1">Sole Trader</span> @break
                                    @case('limited_company') <span class="badge bg-primary ms-1">Ltd</span> @break
                                    @case('partnership') <span class="badge bg-warning ms-1">Partnership</span> @break
                                    @case('llp') <span class="badge bg-secondary ms-1">LLP</span> @break
                                @endswitch
                            </div>
                            @if($operator->legal_company_name && $operator->legal_company_name !== $operator->operator_name)
                                <small class="text-muted">{{ $operator->legal_company_name }}</small>
                            @endif
                        </td>
                        <td>{{ $operator->email }}</td>
                        <td>{{ $operator->city ?? '-' }}</td>
                        <td>{{ $operator->licence_authority ?? '-' }}</td>
                        <td>{{ $operator->fleet_size ?? '-' }}</td>
                        <td>{{ $operator->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.operators.show', $operator) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Review
                                </a>
                                <form method="POST" action="{{ route('admin.operators.approve', $operator) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this operator?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $operator->id }}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>

                            {{-- Reject Modal --}}
                            <div class="modal fade" id="rejectModal{{ $operator->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.operators.reject', $operator) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reject {{ $operator->operator_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Reason for Rejection</label>
                                                    <textarea name="rejection_reason" class="form-control" rows="4" required
                                                        placeholder="Provide a reason for rejecting this application..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject Operator</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-2 d-block mb-2"></i>
                            No pending operator approvals.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($operators->hasPages())
        <div class="card-footer">
            {{ $operators->links() }}
        </div>
    @endif
</div>
@endsection
