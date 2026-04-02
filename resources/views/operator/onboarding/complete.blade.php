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

        <div class="text-start mx-auto" style="max-width:450px;">
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

            <div class="alert alert-danger small mt-3">
                <i class="bi bi-shield-exclamation me-1"></i>
                <strong>To complete your approval, please upload the following documents in My Account &gt; Licence & Fleet:</strong>
                <ol class="mb-0 mt-2">
                    <li><strong>Private Hire Operator Licence</strong> (PDF or photo of your current PHO licence)</li>
                    <li><strong>Public Liability Insurance Certificate</strong> (minimum &pound;5,000,000 cover)</li>
                </ol>
                <div class="mt-2">
                    Applications without valid documents will be <strong>rejected</strong>.
                    All drivers must also have valid PHV driver licences and enhanced DBS checks.
                </div>
            </div>

            <div class="alert alert-light border small mt-2">
                <strong>Approval Timeline:</strong> Our compliance team reviews applications within <strong>1-2 business days</strong>.
                You will receive an email notification once your account is approved or if additional information is needed.
            </div>
        </div>

        <hr class="my-4">

        <div class="d-grid gap-2 mx-auto" style="max-width:300px;">
            <a href="{{ route('operator.account.index') }}" class="btn btn-danger btn-lg mb-2">
                <i class="bi bi-upload me-2"></i> Upload Documents Now
            </a>
            <a href="{{ route('operator.dashboard') }}" class="btn btn-outline-success">
                <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
            </a>
        </div>
    </div>
@endsection
