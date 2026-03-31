@extends('layouts.app')
@section('title', 'Delete My Account')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <a href="{{ route('passenger.profile') }}" class="text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Back to Profile
            </a>

            <div class="card border-danger mt-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Delete My Account</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This action is irreversible. Once your account is deleted, it cannot be recovered.
                    </div>

                    <h6 class="fw-semibold mb-3">What will happen when you delete your account:</h6>
                    <ul class="mb-4">
                        <li>Your account will be <strong>permanently deactivated</strong></li>
                        <li>Your personal data (name, email, phone) will be <strong>anonymised</strong> in compliance with GDPR</li>
                        <li>Your <strong>booking history</strong> will be retained in anonymised form for legal and regulatory compliance</li>
                        <li>You will be <strong>logged out immediately</strong></li>
                        <li>You will <strong>no longer be able to log in</strong> with your current credentials</li>
                        <li>Any <strong>active bookings</strong> should be cancelled before deleting your account</li>
                    </ul>

                    <hr>

                    <form action="{{ route('passenger.delete-account.confirm') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                Confirm your password to proceed
                            </label>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your current password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('passenger.profile') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you absolutely sure you want to delete your account? This cannot be undone.')">
                                <i class="bi bi-trash me-1"></i>Delete My Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
