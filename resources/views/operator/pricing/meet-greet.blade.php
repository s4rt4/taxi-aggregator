@extends('layouts.operator')
@section('title', 'Meet & Greet Charges')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Meet & Greet Charges</h1>
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
        Meet & Greet is a premium service where your driver meets the passenger inside the airport terminal or
        at the station entrance, holding a name board. This is popular with business travellers and customers
        arriving at unfamiliar airports or stations.
    </p>
    <p class="mb-0 text-muted small">
        Set your additional charge for providing a Meet & Greet service at each location below.
        This charge is added on top of the journey fare. Leave blank or set to &pound;0.00 if you do not
        wish to offer Meet & Greet at a particular location.
    </p>
</div>

{{-- Instruction --}}
<div class="bg-light rounded border p-3 mb-4">
    <p class="mb-0 fw-medium">
        <i class="bi bi-info-circle text-primary"></i>
        Set your Meet & Greet charges per location. Charges are applied per booking when the customer selects
        the Meet & Greet option.
    </p>
</div>

{{-- Location Charges Form --}}
<form action="#" method="POST">
    @csrf
    @method('PUT')

    <div class="bg-white rounded border p-4 mb-4">
        <h5 class="fw-bold mb-3">Airport & Station Meet & Greet Charges</h5>

        @php
            $locations = [
                ['name' => 'Aberdeen Airport - Dyce, Terminal Building ABZ5AA', 'code' => 'ABD', 'default' => '5.00'],
                ['name' => 'Belfast City Airport BT3 9JH', 'code' => 'BFS', 'default' => '5.00'],
                ['name' => 'Belfast International Airport BT29 4AB', 'code' => 'BFS_INT', 'default' => '5.00'],
                ['name' => 'Birmingham International Airport B26 3QJ', 'code' => 'BHX', 'default' => '5.00'],
                ['name' => 'Blackpool Airport FY4 2QY', 'code' => 'BLK', 'default' => '3.00'],
                ['name' => 'Bournemouth Airport BH23 6SE', 'code' => 'BOH', 'default' => '3.00'],
                ['name' => 'Bristol Airport BS48 3DY', 'code' => 'BRS', 'default' => '5.00'],
                ['name' => 'Cardiff Airport CF62 3BD', 'code' => 'CWL', 'default' => '5.00'],
                ['name' => 'City of Derry Airport BT47 3GY', 'code' => 'LDY', 'default' => '3.00'],
                ['name' => 'Coventry Airport CV8 3AZ', 'code' => 'CVT', 'default' => '3.00'],
                ['name' => 'Doncaster Sheffield Airport DN9 3RH', 'code' => 'DSA', 'default' => '3.00'],
                ['name' => 'Dundee Airport DD2 1UH', 'code' => 'DND', 'default' => '3.00'],
                ['name' => 'Durham Tees Valley Airport DL2 1LU', 'code' => 'MME', 'default' => '3.00'],
                ['name' => 'East Midlands Airport DE74 2SA', 'code' => 'EMA', 'default' => '5.00'],
                ['name' => 'Edinburgh Airport EH12 9DN', 'code' => 'EDI', 'default' => '5.00'],
                ['name' => 'Exeter Airport EX5 2BD', 'code' => 'EXT', 'default' => '3.00'],
                ['name' => 'Glasgow Airport PA3 2SW', 'code' => 'GLA', 'default' => '5.00'],
                ['name' => 'Glasgow Prestwick Airport KA9 2PL', 'code' => 'PIK', 'default' => '5.00'],
                ['name' => 'Guernsey Airport GY1 1LJ', 'code' => 'GCI', 'default' => '3.00'],
                ['name' => 'Humberside Airport DN39 6YH', 'code' => 'HUY', 'default' => '3.00'],
                ['name' => 'Inverness Airport IV2 7JB', 'code' => 'INV', 'default' => '3.00'],
                ['name' => 'Isle of Man Airport IM9 2AS', 'code' => 'IOM', 'default' => '3.00'],
                ['name' => 'Jersey Airport JE1 1BY', 'code' => 'JER', 'default' => '3.00'],
                ['name' => 'Leeds Bradford Airport LS19 7TU', 'code' => 'LBA', 'default' => '5.00'],
                ['name' => 'Liverpool John Lennon Airport L24 1YD', 'code' => 'LPL', 'default' => '5.00'],
                ['name' => 'London City Airport E16 2PX', 'code' => 'LCY', 'default' => '5.00'],
                ['name' => 'London Gatwick Airport RH6 0NP', 'code' => 'LGW', 'default' => '8.00'],
                ['name' => 'London Heathrow Airport TW6 1EW', 'code' => 'LHR', 'default' => '10.00'],
                ['name' => 'London Luton Airport LU2 9LY', 'code' => 'LTN', 'default' => '5.00'],
                ['name' => 'London Southend Airport SS2 6YF', 'code' => 'SEN', 'default' => '5.00'],
                ['name' => 'London Stansted Airport CM24 1RW', 'code' => 'STN', 'default' => '5.00'],
                ['name' => 'Manchester Airport M90 1QX', 'code' => 'MAN', 'default' => '5.00'],
                ['name' => 'Newcastle Airport NE13 8BZ', 'code' => 'NCL', 'default' => '5.00'],
                ['name' => 'Newquay Cornwall Airport TR8 4RQ', 'code' => 'NQY', 'default' => '3.00'],
                ['name' => 'Norwich Airport NR6 6JA', 'code' => 'NWI', 'default' => '3.00'],
                ['name' => 'Nottingham East Midlands Airport DE74 2SA', 'code' => 'EMA2', 'default' => '5.00'],
                ['name' => 'Robin Hood Doncaster Sheffield Airport DN9 3RH', 'code' => 'DSA2', 'default' => '3.00'],
                ['name' => 'Southampton Airport SO18 2NL', 'code' => 'SOU', 'default' => '5.00'],
                ['name' => 'Birmingham New Street Station B2 4QA', 'code' => 'BHM_STN', 'default' => '3.00'],
                ['name' => 'Bristol Temple Meads Station BS1 6QF', 'code' => 'BRI_STN', 'default' => '3.00'],
                ['name' => 'Edinburgh Waverley Station EH1 1BB', 'code' => 'EDI_STN', 'default' => '3.00'],
                ['name' => 'Glasgow Central Station G1 3SL', 'code' => 'GLC_STN', 'default' => '3.00'],
                ['name' => 'Kings Cross Station N1 9AP', 'code' => 'KGX', 'default' => '5.00'],
                ['name' => 'Leeds Station LS1 4DY', 'code' => 'LDS_STN', 'default' => '3.00'],
                ['name' => 'Liverpool Lime Street Station L1 1JD', 'code' => 'LIV_STN', 'default' => '3.00'],
                ['name' => 'London Euston Station NW1 2RT', 'code' => 'EUS', 'default' => '5.00'],
                ['name' => 'London Liverpool Street Station EC2M 7QH', 'code' => 'LST', 'default' => '5.00'],
                ['name' => 'London Paddington Station W2 1HQ', 'code' => 'PAD', 'default' => '5.00'],
                ['name' => 'London St Pancras International N1C 4QP', 'code' => 'STP', 'default' => '5.00'],
                ['name' => 'London Victoria Station SW1V 1JU', 'code' => 'VIC', 'default' => '5.00'],
                ['name' => 'London Waterloo Station SE1 8SW', 'code' => 'WAT', 'default' => '5.00'],
                ['name' => 'Manchester Piccadilly Station M60 7RA', 'code' => 'MAN_STN', 'default' => '3.00'],
                ['name' => 'Newcastle Central Station NE1 5DL', 'code' => 'NCL_STN', 'default' => '3.00'],
                ['name' => 'Reading Station RG1 1LZ', 'code' => 'RDG_STN', 'default' => '3.00'],
                ['name' => 'Sheffield Station S1 2BP', 'code' => 'SHF_STN', 'default' => '3.00'],
                ['name' => 'Southampton Central Station SO15 1GY', 'code' => 'SOT_STN', 'default' => '3.00'],
                ['name' => 'York Station YO24 1AB', 'code' => 'YRK_STN', 'default' => '3.00'],
                ['name' => 'Dover Port CT17 9BU', 'code' => 'DVR_PORT', 'default' => '5.00'],
                ['name' => 'Folkestone Eurotunnel CT18 8XX', 'code' => 'FLK_TUN', 'default' => '5.00'],
                ['name' => 'Harwich International Port CO12 4SR', 'code' => 'HRW_PORT', 'default' => '3.00'],
                ['name' => 'Hull Ferry Terminal HU9 1PQ', 'code' => 'HUL_PORT', 'default' => '3.00'],
                ['name' => 'Newhaven Ferry Port BN9 0DF', 'code' => 'NHV_PORT', 'default' => '3.00'],
                ['name' => 'Portsmouth Ferry Port PO2 8RU', 'code' => 'POR_PORT', 'default' => '3.00'],
                ['name' => 'Southampton Cruise Terminal SO14 2AQ', 'code' => 'SOT_CRU', 'default' => '5.00'],
            ];
        @endphp

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-start" style="min-width: 450px;">Location</th>
                        <th style="min-width: 80px;">Code</th>
                        <th class="text-center" style="min-width: 140px;">Meet & Greet Charge</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locations as $location)
                    <tr>
                        <td class="text-start">
                            {{ $location['name'] }}
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $location['code'] }}</span>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mx-auto" style="max-width: 120px;">
                                <span class="input-group-text">&pound;</span>
                                <input type="number"
                                       class="form-control form-control-sm text-center"
                                       name="meet_greet[{{ $location['code'] }}]"
                                       value="{{ old('meet_greet.' . $location['code'], $location['default']) }}"
                                       step="0.01"
                                       min="0">
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
</form>
@endsection
