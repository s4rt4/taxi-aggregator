@php
    $user = Auth::user();
    $layout = match($user?->role) {
        'admin' => 'layouts.admin',
        'operator' => 'layouts.operator',
        default => 'layouts.app',
    };
@endphp

@extends($layout)
@section('title', $title ?? 'Page')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route($user->dashboardRoute()) }}">Dashboard</a></li>
    <li class="breadcrumb-item active">{{ $title ?? 'Page' }}</li>
</ol>
@endsection

@section('content')
@if(!in_array($user?->role, ['admin', 'operator']))
<div class="container py-4">
@endif

<div class="page-header">
    <h1>{{ $title ?? 'Page' }}</h1>
</div>

<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-cone-striped text-warning" style="font-size: 3rem;"></i>
        <h5 class="mt-3 fw-semibold">Coming Soon</h5>
        <p class="text-muted">This page is under development.</p>
    </div>
</div>

@if(!in_array($user?->role, ['admin', 'operator']))
</div>
@endif
@endsection
