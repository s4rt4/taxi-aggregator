<?php

namespace Database\Seeders;

use App\Models\MeetGreetLocation;
use Illuminate\Database\Seeder;

class MeetGreetLocationSeeder extends Seeder
{
    /**
     * Seed all UK airports, major stations, and ports into meet_greet_locations.
     */
    public function run(): void
    {
        $locations = [
            // ── Airports ──────────────────────────────────────────────
            ['name' => 'Aberdeen Airport', 'type' => 'airport', 'code' => 'ABZ', 'lat' => 57.2019, 'lng' => -2.1978],
            ['name' => 'Belfast City Airport', 'type' => 'airport', 'code' => 'BHD', 'lat' => 54.6181, 'lng' => -5.8725],
            ['name' => 'Belfast International Airport', 'type' => 'airport', 'code' => 'BFS', 'lat' => 54.6575, 'lng' => -6.2158],
            ['name' => 'Birmingham Airport', 'type' => 'airport', 'code' => 'BHX', 'lat' => 52.4539, 'lng' => -1.7480],
            ['name' => 'Blackpool Airport', 'type' => 'airport', 'code' => 'BLK', 'lat' => 53.7717, 'lng' => -3.0286],
            ['name' => 'Bournemouth Airport', 'type' => 'airport', 'code' => 'BOH', 'lat' => 50.7800, 'lng' => -1.8425],
            ['name' => 'Bristol Airport', 'type' => 'airport', 'code' => 'BRS', 'lat' => 51.3827, 'lng' => -2.7191],
            ['name' => 'Cardiff Airport', 'type' => 'airport', 'code' => 'CWL', 'lat' => 51.3967, 'lng' => -3.3433],
            ['name' => 'Doncaster Sheffield Airport', 'type' => 'airport', 'code' => 'DSA', 'lat' => 53.4805, 'lng' => -1.0105],
            ['name' => 'East Midlands Airport', 'type' => 'airport', 'code' => 'EMA', 'lat' => 52.8311, 'lng' => -1.3281],
            ['name' => 'Edinburgh Airport', 'type' => 'airport', 'code' => 'EDI', 'lat' => 55.9500, 'lng' => -3.3725],
            ['name' => 'Exeter Airport', 'type' => 'airport', 'code' => 'EXT', 'lat' => 50.7344, 'lng' => -3.4139],
            ['name' => 'Glasgow Airport', 'type' => 'airport', 'code' => 'GLA', 'lat' => 55.8717, 'lng' => -4.4314],
            ['name' => 'Glasgow Prestwick Airport', 'type' => 'airport', 'code' => 'PIK', 'lat' => 55.5094, 'lng' => -4.5867],
            ['name' => 'Gatwick Airport North Terminal', 'type' => 'airport', 'code' => 'LGW', 'lat' => 51.1568, 'lng' => -0.1761],
            ['name' => 'Gatwick Airport South Terminal', 'type' => 'airport', 'code' => 'LGW', 'lat' => 51.1481, 'lng' => -0.1772],
            ['name' => 'Heathrow Airport Terminal 1', 'type' => 'airport', 'code' => 'LHR', 'lat' => 51.4700, 'lng' => -0.4543],
            ['name' => 'Heathrow Airport Terminal 2', 'type' => 'airport', 'code' => 'LHR', 'lat' => 51.4710, 'lng' => -0.4528],
            ['name' => 'Heathrow Airport Terminal 3', 'type' => 'airport', 'code' => 'LHR', 'lat' => 51.4722, 'lng' => -0.4535],
            ['name' => 'Heathrow Airport Terminal 4', 'type' => 'airport', 'code' => 'LHR', 'lat' => 51.4590, 'lng' => -0.4455],
            ['name' => 'Heathrow Airport Terminal 5', 'type' => 'airport', 'code' => 'LHR', 'lat' => 51.4723, 'lng' => -0.4889],
            ['name' => 'Humberside Airport', 'type' => 'airport', 'code' => 'HUY', 'lat' => 53.5744, 'lng' => -0.3509],
            ['name' => 'Inverness Airport', 'type' => 'airport', 'code' => 'INV', 'lat' => 57.5425, 'lng' => -4.0475],
            ['name' => 'Leeds Bradford Airport', 'type' => 'airport', 'code' => 'LBA', 'lat' => 53.8659, 'lng' => -1.6606],
            ['name' => 'Liverpool John Lennon Airport', 'type' => 'airport', 'code' => 'LPL', 'lat' => 53.3336, 'lng' => -2.8497],
            ['name' => 'London City Airport', 'type' => 'airport', 'code' => 'LCY', 'lat' => 51.5053, 'lng' => 0.0553],
            ['name' => 'London Luton Airport', 'type' => 'airport', 'code' => 'LTN', 'lat' => 51.8747, 'lng' => -0.3683],
            ['name' => 'London Stansted Airport', 'type' => 'airport', 'code' => 'STN', 'lat' => 51.8850, 'lng' => 0.2350],
            ['name' => 'Manchester Airport Terminal 1', 'type' => 'airport', 'code' => 'MAN', 'lat' => 53.3589, 'lng' => -2.2727],
            ['name' => 'Manchester Airport Terminal 2', 'type' => 'airport', 'code' => 'MAN', 'lat' => 53.3563, 'lng' => -2.2753],
            ['name' => 'Manchester Airport Terminal 3', 'type' => 'airport', 'code' => 'MAN', 'lat' => 53.3615, 'lng' => -2.2701],
            ['name' => 'Newcastle Airport', 'type' => 'airport', 'code' => 'NCL', 'lat' => 55.0375, 'lng' => -1.6917],
            ['name' => 'Newquay Cornwall Airport', 'type' => 'airport', 'code' => 'NQY', 'lat' => 50.4406, 'lng' => -4.9954],
            ['name' => 'Norwich Airport', 'type' => 'airport', 'code' => 'NWI', 'lat' => 52.6758, 'lng' => 1.2828],
            ['name' => 'Robin Hood Airport', 'type' => 'airport', 'code' => 'DSA', 'lat' => 53.4805, 'lng' => -1.0105],
            ['name' => 'Southampton Airport', 'type' => 'airport', 'code' => 'SOU', 'lat' => 50.9503, 'lng' => -1.3568],
            ['name' => 'Southend Airport', 'type' => 'airport', 'code' => 'SEN', 'lat' => 51.5714, 'lng' => 0.6956],

            // ── Rail Stations ─────────────────────────────────────────
            ['name' => 'Birmingham New Street Station', 'type' => 'station', 'code' => 'BHM', 'lat' => 52.4778, 'lng' => -1.9003],
            ['name' => 'Bristol Temple Meads Station', 'type' => 'station', 'code' => 'BRI', 'lat' => 51.4492, 'lng' => -2.5814],
            ['name' => 'Edinburgh Waverley Station', 'type' => 'station', 'code' => 'EDB', 'lat' => 55.9521, 'lng' => -3.1892],
            ['name' => 'Glasgow Central Station', 'type' => 'station', 'code' => 'GLC', 'lat' => 55.8596, 'lng' => -4.2584],
            ['name' => 'London Kings Cross Station', 'type' => 'station', 'code' => 'KGX', 'lat' => 51.5308, 'lng' => -0.1238],
            ['name' => 'Leeds Station', 'type' => 'station', 'code' => 'LDS', 'lat' => 53.7954, 'lng' => -1.5482],
            ['name' => 'Liverpool Lime Street Station', 'type' => 'station', 'code' => 'LIV', 'lat' => 53.4074, 'lng' => -2.9779],
            ['name' => 'London Bridge Station', 'type' => 'station', 'code' => 'LBG', 'lat' => 51.5055, 'lng' => -0.0863],
            ['name' => 'London Euston Station', 'type' => 'station', 'code' => 'EUS', 'lat' => 51.5282, 'lng' => -0.1337],
            ['name' => 'London Paddington Station', 'type' => 'station', 'code' => 'PAD', 'lat' => 51.5154, 'lng' => -0.1755],
            ['name' => 'London St Pancras International Station', 'type' => 'station', 'code' => 'STP', 'lat' => 51.5313, 'lng' => -0.1262],
            ['name' => 'London Victoria Station', 'type' => 'station', 'code' => 'VIC', 'lat' => 51.4952, 'lng' => -0.1441],
            ['name' => 'London Waterloo Station', 'type' => 'station', 'code' => 'WAT', 'lat' => 51.5031, 'lng' => -0.1132],
            ['name' => 'Manchester Piccadilly Station', 'type' => 'station', 'code' => 'MAN', 'lat' => 53.4774, 'lng' => -2.2309],
            ['name' => 'Newcastle Central Station', 'type' => 'station', 'code' => 'NCL', 'lat' => 54.9686, 'lng' => -1.6170],
            ['name' => 'York Station', 'type' => 'station', 'code' => 'YRK', 'lat' => 53.9581, 'lng' => -1.0932],

            // ── Ports ─────────────────────────────────────────────────
            ['name' => 'Dover Port', 'type' => 'port', 'code' => 'DVR', 'lat' => 51.1279, 'lng' => 1.3134],
            ['name' => 'Folkestone Eurotunnel', 'type' => 'port', 'code' => 'FOL', 'lat' => 51.0946, 'lng' => 1.1276],
            ['name' => 'Harwich International Port', 'type' => 'port', 'code' => 'HRW', 'lat' => 51.9466, 'lng' => 1.2579],
            ['name' => 'Hull Ferry Terminal', 'type' => 'port', 'code' => 'HUL', 'lat' => 53.7414, 'lng' => -0.2633],
            ['name' => 'Portsmouth International Port', 'type' => 'port', 'code' => 'PME', 'lat' => 50.8100, 'lng' => -1.0900],
            ['name' => 'Southampton Cruise Terminal', 'type' => 'port', 'code' => 'SOU', 'lat' => 50.8985, 'lng' => -1.4201],
        ];

        foreach ($locations as $location) {
            MeetGreetLocation::updateOrCreate(
                ['name' => $location['name']],
                $location
            );
        }
    }
}
