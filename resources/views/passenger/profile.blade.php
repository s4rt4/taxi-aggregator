@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4">My Profile</h3>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('passenger.update-profile') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="+44 7xxx xxx xxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <a href="{{ route('passenger.bookings') }}" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Back to Bookings
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Account Info --}}
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h6 class="fw-semibold mb-0">Account Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Account Type</small>
                            <span class="fw-medium">{{ ucfirst($user->role) }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Member Since</small>
                            <span class="fw-medium">{{ $user->created_at->format('j M Y') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Email Verified</small>
                            @if($user->email_verified_at)
                                <span class="text-success"><i class="bi bi-check-circle me-1"></i>Verified</span>
                            @else
                                <span class="text-warning"><i class="bi bi-exclamation-circle me-1"></i>Not verified</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
