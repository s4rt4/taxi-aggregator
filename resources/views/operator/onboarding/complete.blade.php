@extends('operator.onboarding.layout')
@section('step', 'Complete')

@php $step = 5; @endphp

@section('content')
    <div class="text-center py-3">
        <div class="mb-3">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        </div>

        <h4 class="fw-bold mb-2">Your account is set up!</h4>
        <p class="text-muted mb-4">
            Your account is pending admin approval. You will receive an email once approved.
        </p>

        <div class="text-start mx-auto" style="max-width:400px;">
            <h6 class="fw-bold mb-3">Setup Checklist</h6>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span class="small">Company details provided</span>
                </li>
                <li class="mb-2">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span class="small">Address & location set</span>
                </li>
                <li class="mb-2">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span class="small">Licence & fleet info added</span>
                </li>
                <li class="mb-2">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span class="small">Basic pricing configured</span>
                </li>
                <li class="mb-2">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span class="small">Availability range set</span>
                </li>
            </ul>
        </div>

        <hr class="my-4">

        <div class="d-grid gap-2 mx-auto" style="max-width:300px;">
            <a href="{{ route('operator.dashboard') }}" class="btn btn-success btn-lg">
                <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
            </a>
        </div>
    </div>
@endsection
