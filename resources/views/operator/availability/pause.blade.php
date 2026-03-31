@extends('layouts.operator')
@section('title', 'Pause Availability')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Pause Availability</h1>
    <div>
        <a href="#" class="text-decoration-none text-muted small">Help</a>
    </div>
</div>

{{-- Fleet type tabs --}}
<div class="fleet-tabs mb-3">
    <span class="fleet-tab active">Petrol, Diesel & Hybrid</span>
</div>

{{-- Sub-tabs --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="#">Standard</a>
    </li>
</ul>

<div class="row">
    {{-- Immediate Pause --}}
    <div class="col-lg-6 mb-4">
        <div class="bg-white rounded border p-4">
            <h5 class="fw-bold mb-3">Immediate Pause</h5>

            <div class="mb-3">
                <label class="form-label fw-semibold">Length of pause</label>
                <select class="form-select" name="immediate_pause_length">
                    <option value="30" selected>30 mins</option>
                    <option value="60">1 Hour</option>
                    <option value="120">2 Hours</option>
                    <option value="180">3 Hours</option>
                    <option value="240">4 Hours</option>
                    <option value="360">6 Hours</option>
                    <option value="480">8 Hours</option>
                    <option value="720">12 Hours</option>
                    <option value="1440">24 Hours</option>
                    <option value="2880">48 Hours</option>
                    <option value="4320">72 Hours</option>
                    <option value="10080">1 Week</option>
                    <option value="20160">2 Weeks</option>
                    <option value="43200">1 Month</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Apply to:</label>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="immediate_all_sizes" checked>
                        <label class="form-check-label fw-semibold" for="immediate_all_sizes">All car sizes</label>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="immediate_1_4" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="immediate_1_4">
                                <i class="bi bi-check2 me-1"></i> 1-4 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="immediate_5" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="immediate_5">
                                <i class="bi bi-check2 me-1"></i> 5 seater
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="immediate_5_6" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="immediate_5_6">
                                <i class="bi bi-check2 me-1"></i> 5-6 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="immediate_7" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="immediate_7">
                                <i class="bi bi-check2 me-1"></i> 7 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="immediate_8" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="immediate_8">
                                <i class="bi bi-check2 me-1"></i> 8 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="immediate_9" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="immediate_9">
                                <i class="bi bi-check2 me-1"></i> 9 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="immediate_10_14" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="immediate_10_14">
                                <i class="bi bi-check2 me-1"></i> 10-14 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="immediate_15_16" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="immediate_15_16">
                                <i class="bi bi-check2 me-1"></i> 15-16 Passengers
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary text-uppercase fw-bold mt-3">
                Confirm Immediate Pause
            </button>
        </div>
    </div>

    {{-- Pause in the Future --}}
    <div class="col-lg-6 mb-4">
        <div class="bg-white rounded border p-4">
            <h5 class="fw-bold mb-3">Pause in the future</h5>

            <div class="mb-3">
                <label class="form-label fw-semibold">Length of pause</label>
                <select class="form-select" name="future_pause_length">
                    <option value="30">30 mins</option>
                    <option value="60" selected>1 Hour</option>
                    <option value="120">2 Hours</option>
                    <option value="180">3 Hours</option>
                    <option value="240">4 Hours</option>
                    <option value="360">6 Hours</option>
                    <option value="480">8 Hours</option>
                    <option value="720">12 Hours</option>
                    <option value="1440">24 Hours</option>
                    <option value="2880">48 Hours</option>
                    <option value="4320">72 Hours</option>
                    <option value="10080">1 Week</option>
                    <option value="20160">2 Weeks</option>
                    <option value="43200">1 Month</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Starting on</label>
                <div class="row g-2">
                    <div class="col-7">
                        <input type="date" class="form-control" name="future_pause_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-5">
                        <input type="time" class="form-control" name="future_pause_time" value="12:00">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Apply to:</label>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="future_all_sizes" checked>
                        <label class="form-check-label fw-semibold" for="future_all_sizes">All car sizes</label>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="future_1_4" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="future_1_4">
                                <i class="bi bi-check2 me-1"></i> 1-4 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="future_5" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="future_5">
                                <i class="bi bi-check2 me-1"></i> 5 seater
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="future_5_6" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="future_5_6">
                                <i class="bi bi-check2 me-1"></i> 5-6 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="future_7" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="future_7">
                                <i class="bi bi-check2 me-1"></i> 7 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="future_8" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="future_8">
                                <i class="bi bi-check2 me-1"></i> 8 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="future_9" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="future_9">
                                <i class="bi bi-check2 me-1"></i> 9 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="future_10_14" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="future_10_14">
                                <i class="bi bi-check2 me-1"></i> 10-14 Passengers
                            </label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check btn-check-wrapper">
                            <input class="form-check-input d-none" type="checkbox" id="future_15_16" checked>
                            <label class="btn btn-outline-secondary btn-sm w-100 text-start" for="future_15_16">
                                <i class="bi bi-check2 me-1"></i> 15-16 Passengers
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary text-uppercase fw-bold mt-3">
                Confirm Future Pause
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle all checkboxes when "All car sizes" is checked/unchecked
        function setupAllSizesToggle(allCheckboxId, prefix) {
            const allCheckbox = document.getElementById(allCheckboxId);
            if (!allCheckbox) return;

            const sizeCheckboxes = document.querySelectorAll('[id^="' + prefix + '_"]:not(#' + allCheckboxId + ')');

            allCheckbox.addEventListener('change', function () {
                sizeCheckboxes.forEach(function (cb) {
                    cb.checked = allCheckbox.checked;
                    // Toggle active state on label
                    const label = document.querySelector('label[for="' + cb.id + '"]');
                    if (label) {
                        label.classList.toggle('active', cb.checked);
                    }
                });
            });

            sizeCheckboxes.forEach(function (cb) {
                cb.addEventListener('change', function () {
                    const allChecked = Array.from(sizeCheckboxes).every(function (c) { return c.checked; });
                    allCheckbox.checked = allChecked;
                });
            });
        }

        setupAllSizesToggle('immediate_all_sizes', 'immediate');
        setupAllSizesToggle('future_all_sizes', 'future');
    });
</script>
@endpush

@push('styles')
<style>
    .btn-check-wrapper .btn-outline-secondary {
        border-color: #dee2e6;
        color: #495057;
        font-size: 0.8rem;
    }
    .btn-check-wrapper .btn-outline-secondary:hover,
    .btn-check-wrapper input:checked + .btn-outline-secondary {
        background-color: #e8f5e9;
        border-color: #4caf50;
        color: #2e7d32;
    }
</style>
@endpush
