@extends('layouts.guest')
@section('title', 'Corporate Account Setup')

@section('content')
<div class="text-center mb-3">
    <h5 class="fw-bold mb-1">Corporate Account Setup</h5>
    <p class="text-muted small mb-3">Step {{ $step }} of {{ $totalSteps }}</p>

    <div class="progress mb-4" style="height: 6px;">
        <div class="progress-bar bg-primary" role="progressbar"
             style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
    </div>
</div>

@yield('onboarding-content')
@endsection
