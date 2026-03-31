@extends('layouts.operator')
@section('title', 'Postcode Area Prices')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Postcode Area Prices (PAPs)</h1>
    <div>
        <span class="text-muted small">Help</span>
    </div>
</div>

{{-- Fleet type tabs --}}
<div class="fleet-tabs mb-3">
    <span class="fleet-tab active">Petrol, Diesel & Hybrid</span>
</div>

{{-- Sub tabs --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="#">Standard</a>
    </li>
</ul>

{{-- Description --}}
<div class="bg-white rounded border p-4 mb-4">
    <p class="mb-2">
        Postcode Area Prices (PAPs) let you set fixed prices between UK postcode areas.
        These are useful for setting competitive rates across broad geographic regions.
    </p>
    <ul class="text-muted small mb-0">
        <li class="mb-1">
            <strong>Priority:</strong> Location Prices (LPs) take priority over PAPs. If both exist for a route, the LP will be used.
            PAPs take priority over Per Mile Prices (PMPs).
        </li>
        <li class="mb-1">
            <strong>How to set:</strong> Find the origin postcode area on the left column, the destination postcode area on the top row,
            and enter your price in the intersecting cell.
        </li>
        <li class="mb-1">
            <strong>Auto-generation:</strong> If you do not set a PAP for a specific route, the system will automatically calculate
            a price using your Per Mile Prices (PMP) settings based on the distance between the postcode areas.
        </li>
        <li class="mb-0">
            <strong>Reverse routes:</strong> Prices apply in one direction only. To set a return price, you must also enter the reverse route separately.
        </li>
    </ul>
</div>

{{-- Enter price instruction --}}
<div class="bg-light rounded border p-3 mb-4">
    <p class="mb-0 fw-medium">
        <i class="bi bi-info-circle text-primary"></i>
        Enter your price for a <strong>4-passenger saloon</strong>. Prices for other fleet types will be scaled automatically based on your fleet type multipliers.
    </p>
</div>

{{-- Filter Controls --}}
<div class="bg-white rounded border p-4 mb-4">
    <form action="#" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-medium">Edit a postcode area</label>
            <select class="form-select form-select-sm" name="postcode_area">
                <option value="">-- Select postcode area --</option>
                @php
                    $allPostcodes = [
                        'AB','AL','B','BA','BB','BD','BH','BL','BN','BR','BS','BT',
                        'CA','CB','CF','CH','CM','CO','CR','CT','CV','CW',
                        'DA','DD','DE','DG','DH','DL','DN','DT','DY',
                        'E','EC','EH','EN','EX',
                        'FK','FY',
                        'G','GL','GU',
                        'HA','HD','HG','HP','HR','HS','HU','HX',
                        'IG','IP','IV',
                        'KA','KT','KW','KY',
                        'L','LA','LD','LE','LL','LN','LS','LU',
                        'M','ME','MK','ML',
                        'N','NE','NG','NN','NP','NR','NW',
                        'OL','OX',
                        'PA','PE','PH','PL','PO','PR',
                        'RG','RH','RM',
                        'S','SA','SE','SG','SK','SL','SM','SN','SO','SP','SR','SS','ST','SW','SY',
                        'TA','TD','TF','TN','TQ','TR','TS','TW',
                        'UB',
                        'W','WA','WC','WD','WF','WN','WR','WS','WV',
                        'YO',
                        'ZE'
                    ];
                @endphp
                @foreach($allPostcodes as $pc)
                    <option value="{{ $pc }}" {{ request('postcode_area') === $pc ? 'selected' : '' }}>{{ $pc }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label small fw-medium">Show prices for</label>
            <select class="form-select form-select-sm" name="fleet_type">
                <option value="1_4" selected>4 seater</option>
                <option value="5_6">5-6 seater</option>
                <option value="7">7 seater</option>
                <option value="8">8 seater</option>
                <option value="9">9 seater</option>
                <option value="10_14">10-14 seater</option>
                <option value="15_16">15-16 seater</option>
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                Go
            </button>
        </div>
    </form>
</div>

{{-- Postcode Areas Section --}}
<div class="bg-white rounded border p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Postcode areas</h5>
    </div>

    {{-- Alphabet Filter --}}
    <div class="d-flex flex-wrap gap-1 mb-4">
        @foreach(range('A', 'Z') as $letter)
        <a href="?letter={{ $letter }}"
           class="btn btn-sm {{ request('letter', 'A') === $letter ? 'btn-primary' : 'btn-outline-secondary' }}"
           style="min-width: 36px;">
            {{ $letter }}
        </a>
        @endforeach
    </div>

    {{-- Postcode Grid Table --}}
    @php
        // Grid postcodes for the currently selected letter (default: A)
        $currentLetter = request('letter', 'A');
        $rowPostcodes = array_filter($allPostcodes, function($pc) use ($currentLetter) {
            return str_starts_with($pc, $currentLetter);
        });

        // Destination postcodes (header row) - showing a subset for the current page
        $destPostcodes = array_slice($allPostcodes, 0, 15);
    @endphp

    <div class="table-responsive" style="max-height: 600px; overflow: auto;">
        <table class="table table-bordered table-sm align-middle text-center" style="font-size: 0.8rem;">
            <thead class="table-light sticky-top">
                <tr>
                    <th class="text-start bg-light" style="min-width: 70px; position: sticky; left: 0; z-index: 3;">
                        From / To
                    </th>
                    @foreach($destPostcodes as $dest)
                    <th style="min-width: 70px;" class="bg-light">{{ $dest }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rowPostcodes as $origin)
                <tr>
                    <td class="text-start fw-bold bg-light" style="position: sticky; left: 0; z-index: 1;">
                        {{ $origin }}
                    </td>
                    @foreach($destPostcodes as $dest)
                    <td>
                        @if($origin !== $dest)
                        <div class="input-group input-group-sm" style="min-width: 65px;">
                            <span class="input-group-text px-1" style="font-size: 0.7rem;">&pound;</span>
                            <input type="number"
                                   class="form-control form-control-sm text-center px-1"
                                   name="pap[{{ $origin }}][{{ $dest }}]"
                                   value="{{ old("pap.{$origin}.{$dest}") }}"
                                   step="0.01"
                                   min="0"
                                   placeholder="-"
                                   style="font-size: 0.75rem;">
                        </div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach

                @if(empty($rowPostcodes))
                <tr>
                    <td colspan="{{ count($destPostcodes) + 1 }}" class="text-center text-muted py-4">
                        No postcode areas found for letter "{{ $currentLetter }}".
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <nav class="mt-3" aria-label="Postcode area pagination">
        <ul class="pagination pagination-sm justify-content-center mb-0">
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item active"><a class="page-link" href="?page=1">1</a></li>
            <li class="page-item"><a class="page-link" href="?page=2">2</a></li>
            <li class="page-item"><a class="page-link" href="?page=3">3</a></li>
            <li class="page-item"><a class="page-link" href="?page=4">4</a></li>
            <li class="page-item"><a class="page-link" href="?page=5">5</a></li>
            <li class="page-item"><a class="page-link" href="?page=6">6</a></li>
            <li class="page-item"><a class="page-link" href="?page=7">7</a></li>
            <li class="page-item">
                <a class="page-link" href="?page=2" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

{{-- Action Buttons --}}
<div class="d-flex gap-3 mb-4">
    <button type="submit" class="btn btn-success text-uppercase fw-bold px-4">
        Save Changes
    </button>
    <button type="button" class="btn btn-secondary text-uppercase fw-bold px-4">
        Cancel and Discard Changes
    </button>
</div>
@endsection
