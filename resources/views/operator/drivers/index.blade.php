@extends('layouts.operator')
@section('title', 'Drivers')

@push('styles')
<style>
    .drivers-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #666;
        font-weight: 600;
        letter-spacing: 0.3px;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }
    .drivers-table td {
        font-size: 0.9rem;
        vertical-align: middle;
        color: #333;
    }
    .drivers-table .driver-name {
        font-weight: 700;
        color: #333;
    }
    .dbs-check {
        font-size: 1.1rem;
    }
    .dbs-check.passed {
        color: #198754;
    }
    .dbs-check.failed {
        color: #dc3545;
    }
    .action-link {
        font-size: 0.85rem;
        text-decoration: none;
        font-weight: 500;
    }
    .action-link.edit {
        color: #0d9488;
    }
    .action-link.edit:hover {
        color: #0a7a6e;
        text-decoration: underline;
    }
    .action-link.delete {
        color: #dc3545;
    }
    .action-link.delete:hover {
        color: #b02a37;
        text-decoration: underline;
    }
    .pagination-custom .page-link {
        color: #333;
        border: 1px solid #dee2e6;
        font-size: 0.85rem;
        padding: 0.35rem 0.7rem;
    }
    .pagination-custom .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Drivers</h1>
    <div>
        <a href="#" class="text-decoration-none small"><i class="bi bi-plus-circle"></i> Add Driver</a>
        <a href="#" class="text-decoration-none small ms-3"><i class="bi bi-question-circle"></i> Help</a>
    </div>
</div>

{{-- Drivers Table --}}
<div class="bg-white rounded border">
    <div class="table-responsive">
        <table class="table table-hover mb-0 drivers-table">
            <thead>
                <tr>
                    <th class="ps-3">Driver Name</th>
                    <th>Licence Number</th>
                    <th>Mobile Number</th>
                    <th>Vehicle</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Reg No</th>
                    <th class="text-center">DBS</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers ?? [] as $driver)
                <tr>
                    <td class="ps-3">
                        <span class="driver-name">{{ $driver->full_name ?? $driver->first_name . ' ' . $driver->last_name }}</span>
                    </td>
                    <td>{{ $driver->licence_number ?? 'N/A' }}</td>
                    <td>{{ $driver->mobile_number ?? 'N/A' }}</td>
                    <td>{{ $driver->vehicle_type ?? 'N/A' }}</td>
                    <td>{{ $driver->vehicle_make ?? 'N/A' }}</td>
                    <td>{{ $driver->vehicle_model ?? 'N/A' }}</td>
                    <td>{{ $driver->registration_number ?? 'N/A' }}</td>
                    <td class="text-center">
                        @if($driver->dbs_verified ?? false)
                            <i class="bi bi-check-circle-fill dbs-check passed"></i>
                        @else
                            <i class="bi bi-x-circle-fill dbs-check failed"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('operator.drivers.edit', $driver->id ?? 0) }}" class="action-link edit">Edit</a>
                        <span class="text-muted mx-1">|</span>
                        <form method="POST" action="{{ route('operator.drivers.destroy', $driver->id ?? 0) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this driver?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 action-link delete" style="border:none;background:none;">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-people fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">No drivers added yet. Click <strong>"+ Add Driver"</strong> to add your first driver.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if(isset($drivers) && $drivers instanceof \Illuminate\Pagination\LengthAwarePaginator && $drivers->lastPage() > 1)
<nav class="d-flex justify-content-center mt-4" aria-label="Drivers pagination">
    <ul class="pagination pagination-custom mb-0">
        {{-- Previous --}}
        <li class="page-item {{ $drivers->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $drivers->previousPageUrl() }}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        @for($i = 1; $i <= $drivers->lastPage(); $i++)
        <li class="page-item {{ $drivers->currentPage() === $i ? 'active' : '' }}">
            <a class="page-link" href="{{ $drivers->url($i) }}">{{ $i }}</a>
        </li>
        @endfor

        {{-- Next --}}
        <li class="page-item {{ $drivers->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $drivers->nextPageUrl() }}" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
@endif
@endsection
