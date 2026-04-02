<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup Your Account - Step @yield('step') - {{ config('app.name') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { background: #f5f6fa; }
        .onboarding-card { border: none; border-radius: 12px; }
        .step-indicator { display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 0.5rem; }
        .step-dot { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center;
                     justify-content: center; font-size: 0.75rem; font-weight: 700; border: 2px solid #dee2e6;
                     color: #6c757d; background: #fff; }
        .step-dot.active { background: #198754; border-color: #198754; color: #fff; }
        .step-dot.completed { background: #198754; border-color: #198754; color: #fff; }
        .field-label { font-size: 0.8125rem; font-weight: 600; color: #495057; margin-bottom: 0.25rem; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container py-4" style="max-width:700px;">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-3">
                <i class="bi bi-taxi-front-fill text-primary me-2"></i>
                {{ \App\Helpers\Settings::get('company_name', config('app.name')) }} - Operator Setup
            </h4>

            {{-- Step indicators --}}
            <div class="step-indicator">
                @for ($i = 1; $i <= $totalSteps; $i++)
                    <div class="step-dot {{ $i < $step ? 'completed' : ($i === $step ? 'active' : '') }}">
                        @if ($i < $step)
                            <i class="bi bi-check-lg"></i>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                @endfor
            </div>

            {{-- Progress bar --}}
            <div class="progress mb-2" style="height:6px;">
                <div class="progress-bar bg-success" style="width:{{ ($step / $totalSteps) * 100 }}%"></div>
            </div>
            <span class="small text-muted">Step {{ $step }} of {{ $totalSteps }}</span>
        </div>

        <div class="card onboarding-card shadow-sm">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <div class="text-center mt-3">
            <small class="text-muted">Need help? Contact <a href="mailto:{{ \App\Helpers\Settings::get('contact_email', 'support@rushxo.com') }}">{{ \App\Helpers\Settings::get('contact_email', 'support@rushxo.com') }}</a></small>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
