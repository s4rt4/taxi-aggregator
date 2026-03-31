@extends('layouts.operator')
@section('title', 'Trip Issues & Ratings')

@push('styles')
<style>
    .issues-tabs .nav-link {
        color: #555;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        padding: 0.6rem 1rem;
        background: none;
    }
    .issues-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: none;
    }
    .issues-tabs .nav-link:hover {
        color: #0d6efd;
    }
    .issues-table th {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #666;
        font-weight: 700;
        letter-spacing: 0.3px;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
        padding: 0.6rem 0.5rem;
    }
    .issues-table td {
        font-size: 0.85rem;
        vertical-align: middle;
        color: #333;
        padding: 0.6rem 0.5rem;
    }
    .issues-table .week-range {
        font-weight: 600;
        white-space: nowrap;
    }
    .rating-bar-container {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .rating-bar-label {
        width: 80px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #555;
        flex-shrink: 0;
    }
    .rating-bar-track {
        flex: 1;
        height: 24px;
        background: #f0f0f0;
        border-radius: 4px;
        overflow: hidden;
        margin: 0 10px;
    }
    .rating-bar-fill {
        height: 100%;
        background: #7b2d3b;
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    .rating-bar-value {
        font-size: 0.85rem;
        font-weight: 700;
        color: #333;
        width: 35px;
        text-align: right;
        flex-shrink: 0;
    }
    .review-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    .review-stars .bi-star-fill {
        color: #ffc107;
        font-size: 0.9rem;
    }
    .review-stars .bi-star {
        color: #dee2e6;
        font-size: 0.9rem;
    }
    .show-more-link {
        font-size: 0.85rem;
        color: #0d6efd;
        text-decoration: none;
        font-weight: 500;
    }
    .show-more-link:hover {
        text-decoration: underline;
    }
    .trends-chart-placeholder {
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border: 1px dashed #dee2e6;
        border-radius: 6px;
        color: #999;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Trip Issues &amp; Ratings</h1>
    <div>
        <a href="#" class="text-decoration-none small"><i class="bi bi-question-circle"></i> Help</a>
    </div>
</div>

{{-- Sub-tab Navigation --}}
<ul class="nav issues-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="trip-issues-tab" data-bs-toggle="tab" data-bs-target="#trip-issues" type="button" role="tab" aria-controls="trip-issues" aria-selected="true">Trip Issues</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="latest-ratings-tab" data-bs-toggle="tab" data-bs-target="#latest-ratings" type="button" role="tab" aria-controls="latest-ratings" aria-selected="false">Latest Ratings</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="ratings-trends-tab" data-bs-toggle="tab" data-bs-target="#ratings-trends" type="button" role="tab" aria-controls="ratings-trends" aria-selected="false">Ratings Trends</button>
    </li>
</ul>

{{-- Tab Content --}}
<div class="tab-content">

    {{-- Trip Issues Tab --}}
    <div class="tab-pane fade show active" id="trip-issues" role="tabpanel" aria-labelledby="trip-issues-tab">
        <div class="bg-white rounded border p-4 mb-4">
            <p class="text-muted small mb-3">
                Below is a summary of trip issues for your account. Fines may be applied for rejected trips, driver no-shows,
                and late pickups. Repeated issues may result in account suspension. Please review and take corrective action
                where necessary. If you have any queries, contact
                <a href="mailto:support@taxiaggregator.co.uk">support@taxiaggregator.co.uk</a>.
            </p>

            <div class="table-responsive">
                <table class="table table-hover mb-0 issues-table">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th class="text-center">No. of Pickups</th>
                            <th class="text-center">No. of Rejected Trips</th>
                            <th class="text-center"># Rejected Trips</th>
                            <th class="text-center">No. of Driver No Shows</th>
                            <th class="text-center"># Lost from No Shows</th>
                            <th class="text-center">No. of Late Trips</th>
                            <th class="text-center">No. of Failed Airport Meet &amp; Greets</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tripIssues ?? [] as $issue)
                        <tr>
                            <td class="week-range">{{ $issue->week_start ?? '' }} - {{ $issue->week_end ?? '' }}</td>
                            <td class="text-center">{{ $issue->total_pickups ?? 0 }}</td>
                            <td class="text-center">{{ $issue->rejected_trips ?? 0 }}</td>
                            <td class="text-center">&pound;{{ number_format($issue->rejected_trips_cost ?? 0, 2) }}</td>
                            <td class="text-center">{{ $issue->no_shows ?? 0 }}</td>
                            <td class="text-center">&pound;{{ number_format($issue->no_shows_cost ?? 0, 2) }}</td>
                            <td class="text-center">{{ $issue->late_trips ?? 0 }}</td>
                            <td class="text-center">{{ $issue->failed_meet_greets ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No trip issues recorded for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($tripIssues) && count($tripIssues) > 0)
            <div class="text-center mt-3">
                <a href="#" class="show-more-link">Show more</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Latest Ratings Tab --}}
    <div class="tab-pane fade" id="latest-ratings" role="tabpanel" aria-labelledby="latest-ratings-tab">
        <div class="bg-white rounded border p-4 mb-4">
            {{-- Date Range Filter --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Pick up date from</label>
                    <input type="date" class="form-control form-control-sm" name="ratings_date_from" value="{{ request('ratings_date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Pick up date to</label>
                    <input type="date" class="form-control form-control-sm" name="ratings_date_to" value="{{ request('ratings_date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </div>

            {{-- Total Trips --}}
            <div class="mb-4">
                <span class="fw-bold text-muted small">Total number of trips: {{ $totalRatedTrips ?? 0 }}</span>
            </div>

            {{-- Horizontal Bar Chart --}}
            <div class="mb-4">
                @php
                    $ratingCategories = [
                        'Timing' => $ratingSummary['timing'] ?? 0,
                        'Fare' => $ratingSummary['fare'] ?? 0,
                        'Driver' => $ratingSummary['driver'] ?? 0,
                        'Vehicle' => $ratingSummary['vehicle'] ?? 0,
                        'Route' => $ratingSummary['route'] ?? 0,
                    ];
                    $maxRating = 5;
                @endphp

                @foreach($ratingCategories as $category => $rating)
                <div class="rating-bar-container">
                    <span class="rating-bar-label">{{ $category }}</span>
                    <div class="rating-bar-track">
                        <div class="rating-bar-fill" style="width: {{ $maxRating > 0 ? ($rating / $maxRating) * 100 : 0 }}%"></div>
                    </div>
                    <span class="rating-bar-value">{{ number_format($rating, 1) }}</span>
                </div>
                @endforeach
            </div>

            <hr>

            {{-- Individual Reviews --}}
            <h6 class="fw-bold mb-3">Individual Reviews</h6>
            @forelse($latestRatings ?? [] as $rating)
            <div class="review-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="review-stars mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($rating->overall_rating ?? 0))
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                            <span class="ms-2 small fw-bold">{{ number_format($rating->overall_rating ?? 0, 1) }}</span>
                        </div>
                        @if($rating->comment ?? false)
                        <p class="text-muted small mb-0 mt-1">{{ $rating->comment }}</p>
                        @endif
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">{{ $rating->created_at ?? '' }}</div>
                        <div class="small text-muted">{{ $rating->passenger_name ?? 'Anonymous' }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="bi bi-star fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-0">No ratings received yet.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Ratings Trends Tab --}}
    <div class="tab-pane fade" id="ratings-trends" role="tabpanel" aria-labelledby="ratings-trends-tab">
        <div class="bg-white rounded border p-4 mb-4">
            {{-- Date Range & Dropdown Filter --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Pick up date from</label>
                    <input type="date" class="form-control form-control-sm" name="trends_date_from" value="{{ request('trends_date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Pick up date to</label>
                    <input type="date" class="form-control form-control-sm" name="trends_date_to" value="{{ request('trends_date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Category</label>
                    <select class="form-select form-select-sm" name="trends_category">
                        <option value="overall">Ratings</option>
                        <option value="timing">Timing</option>
                        <option value="fare">Fare</option>
                        <option value="driver">Driver</option>
                        <option value="vehicle">Vehicle</option>
                        <option value="route">Route</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </div>

            {{-- Total Trips --}}
            <div class="mb-4">
                <span class="fw-bold text-muted small">Total number of trips: {{ $totalTrendsTrips ?? 0 }}</span>
            </div>

            {{-- Star Rating Visualization / Scatter Timeline --}}
            <div class="trends-chart-placeholder" id="ratings-trends-chart">
                <div class="text-center">
                    <i class="bi bi-graph-up fs-1 d-block mb-2"></i>
                    <span class="small">Ratings trend chart will render here</span>
                    <div class="small text-muted mt-1">Requires JavaScript charting library (e.g., Chart.js)</div>
                </div>
            </div>

            {{-- Rating points data for chart rendering --}}
            @if(isset($ratingsTrends) && count($ratingsTrends) > 0)
            <div class="mt-4">
                <h6 class="fw-bold small text-muted">Rating Data Points</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th class="small">Date</th>
                                <th class="small text-center">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ratingsTrends as $point)
                            <tr>
                                <td class="small">{{ $point->date ?? '' }}</td>
                                <td class="text-center">
                                    <span class="review-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= ($point->rating ?? 0))
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Placeholder for Chart.js or similar library integration
    // Ratings trends chart will be initialized here when charting library is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Initialize ratings trends chart
        // const ctx = document.getElementById('ratings-trends-chart');
        // if (ctx && typeof Chart !== 'undefined') {
        //     new Chart(ctx, { type: 'scatter', ... });
        // }
    });
</script>
@endpush
