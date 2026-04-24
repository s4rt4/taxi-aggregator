@extends('layouts.admin')
@section('title', 'Pending Corporates')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4">Pending Corporate Approvals</h3>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($corporates->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                    <p>No corporates pending approval.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Business Type</th>
                                <th>Billing Email</th>
                                <th>Users</th>
                                <th>Registered</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($corporates as $c)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $c->company_name }}</div>
                                        @if($c->registration_number)
                                            <small class="text-muted">Reg: {{ $c->registration_number }}</small>
                                        @endif
                                    </td>
                                    <td>{{ str_replace('_', ' ', ucfirst($c->business_type)) }}</td>
                                    <td>{{ $c->billing_email }}</td>
                                    <td>{{ $c->corporate_users_count }}</td>
                                    <td>{{ $c->created_at?->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.corporates.show', $c) }}" class="btn btn-sm btn-primary">Review</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
