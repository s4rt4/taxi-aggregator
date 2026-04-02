<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationPageController extends Controller
{
    public function city(string $slug)
    {
        $cities = $this->getCityData();
        $city = $cities[$slug] ?? abort(404);
        $allCities = $cities;
        $allAirports = $this->getAirportData();
        return view('pages.city', compact('city', 'slug', 'allCities', 'allAirports'));
    }

    public function airport(string $slug)
    {
        $airports = $this->getAirportData();
        $airport = $airports[$slug] ?? abort(404);
        $allCities = $this->getCityData();
        $allAirports = $airports;
        return view('pages.airport', compact('airport', 'slug', 'allCities', 'allAirports'));
    }

    protected function getCityData(): array
    {
        return [
            'london' => [
                'name' => 'London',
                'title' => 'London Taxi & Minicab Quotes',
                'description' => 'Compare taxi prices from licensed London operators covering all 32 boroughs. Whether you need a ride across Zone 1 or a transfer to any of the capital\'s six airports, find the best fares instantly.',
                'image' => 'https://picsum.photos/seed/london/800/400',
                'popular_routes' => [
                    ['from' => 'London City Centre', 'to' => 'Heathrow Airport', 'price_from' => 35],
                    ['from' => 'London City Centre', 'to' => 'Gatwick Airport', 'price_from' => 45],
                    ['from' => 'London', 'to' => 'Oxford', 'price_from' => 85],
                    ['from' => 'London', 'to' => 'Cambridge', 'price_from' => 90],
                    ['from' => 'London', 'to' => 'Brighton', 'price_from' => 65],
                    ['from' => 'London', 'to' => 'Stansted Airport', 'price_from' => 55],
                ],
                'about' => 'London is the busiest taxi market in the UK, with thousands of private hire vehicles operating around the clock. From the West End to Canary Wharf, our operators cover every corner of the capital. Pre-booking is often cheaper than hailing a black cab, especially for airport transfers and cross-city journeys. Our London operators are licensed by Transport for London (TfL) and offer fixed-price fares with no surge pricing.',
                'tips' => [
                    'Book airport transfers at least 24 hours in advance to secure the best rates, especially during peak travel periods around Bank Holidays.',
                    'For journeys within central London during rush hour (7-10am, 4-7pm), allow extra time as traffic congestion around the Congestion Charge zone can add 20-40 minutes.',
                    'If travelling to events at the O2, Wembley, or ExCeL, pre-book your return journey as well since taxis in these areas fill up quickly after major events.',
                ],
                'faqs' => [
                    ['q' => 'How much does a taxi from central London to Heathrow cost?', 'a' => 'Pre-booked fares from central London to Heathrow typically start from around £35-45 for a standard saloon. The exact price depends on your pickup postcode and time of travel. Night-time journeys may carry a small supplement.'],
                    ['q' => 'Are London minicabs cheaper than black cabs?', 'a' => 'Pre-booked minicabs are generally 20-40% cheaper than metered black cabs for the same journey. Unlike black cabs, minicab prices are fixed at booking so you won\'t be affected by traffic delays or route changes.'],
                    ['q' => 'Can I book a taxi for early morning flights from London?', 'a' => 'Yes, many of our London operators run 24/7 and specialise in early morning airport pickups. You can book for any time, including 3am and 4am collections, with no extra booking fees.'],
                    ['q' => 'Do London taxis accept card payments?', 'a' => 'All bookings through our platform are paid online via secure card payment at the time of booking. You won\'t need cash on the day of travel.'],
                ],
            ],

            'manchester' => [
                'name' => 'Manchester',
                'title' => 'Manchester Taxi & Minicab Quotes',
                'description' => 'Get instant quotes from Manchester\'s top-rated private hire operators. Compare prices for city centre rides, airport transfers to Manchester Airport, and long-distance journeys across the North West.',
                'image' => 'https://picsum.photos/seed/manchester/800/400',
                'popular_routes' => [
                    ['from' => 'Manchester City Centre', 'to' => 'Manchester Airport', 'price_from' => 22],
                    ['from' => 'Manchester', 'to' => 'Liverpool', 'price_from' => 40],
                    ['from' => 'Manchester', 'to' => 'Leeds', 'price_from' => 45],
                    ['from' => 'Manchester', 'to' => 'Sheffield', 'price_from' => 42],
                    ['from' => 'Manchester', 'to' => 'Birmingham', 'price_from' => 75],
                    ['from' => 'Manchester', 'to' => 'London', 'price_from' => 180],
                ],
                'about' => 'Manchester\'s private hire industry is one of the largest outside London, with hundreds of licensed operators serving the Greater Manchester area. The city\'s thriving nightlife, major sporting venues, and business district create high demand for reliable taxi services. Our Manchester operators are licensed by the relevant Greater Manchester councils and offer competitive fixed-price fares for all journey types.',
                'tips' => [
                    'Manchester Airport transfers are best booked 48 hours ahead. The airport is about 10 miles south of the city centre, and peak-time journeys along the M56 can take up to 50 minutes.',
                    'After events at Old Trafford or the Etihad Stadium, avoid the post-match surge by pre-booking your return taxi with a pickup time 20 minutes after the final whistle.',
                    'The Northern Quarter and Deansgate areas have restricted vehicle access on weekend evenings, so confirm your exact pickup point with the operator to avoid delays.',
                ],
                'faqs' => [
                    ['q' => 'How long does a taxi take from Manchester city centre to the airport?', 'a' => 'A standard taxi journey from Manchester city centre to the airport takes 25-40 minutes depending on traffic. During rush hour or event days, allow up to 50 minutes.'],
                    ['q' => 'Can I get a taxi from Manchester to other Northern cities?', 'a' => 'Absolutely. Inter-city transfers to Liverpool, Leeds, Sheffield, and other Northern cities are among our most popular routes. Fixed prices mean no surprises on longer journeys.'],
                    ['q' => 'Are there extra charges for luggage on Manchester taxis?', 'a' => 'Standard luggage (up to 2 suitcases and 2 carry-on bags) is included in the fare. If you have excess luggage or oversized items, an estate or MPV vehicle may be recommended at a slightly higher rate.'],
                    ['q' => 'Do Manchester operators cover Salford and Stockport?', 'a' => 'Yes, our operators cover the entire Greater Manchester area including Salford, Stockport, Bolton, Oldham, Rochdale, Bury, Wigan, and Tameside.'],
                ],
            ],

            'birmingham' => [
                'name' => 'Birmingham',
                'title' => 'Birmingham Taxi & Minicab Quotes',
                'description' => 'Find affordable minicab fares across the West Midlands. Compare quotes from Birmingham\'s licensed operators for journeys to Birmingham Airport, the NEC, and cities throughout central England.',
                'image' => 'https://picsum.photos/seed/birmingham/800/400',
                'popular_routes' => [
                    ['from' => 'Birmingham City Centre', 'to' => 'Birmingham Airport', 'price_from' => 18],
                    ['from' => 'Birmingham', 'to' => 'London', 'price_from' => 120],
                    ['from' => 'Birmingham', 'to' => 'Coventry', 'price_from' => 25],
                    ['from' => 'Birmingham', 'to' => 'Wolverhampton', 'price_from' => 18],
                    ['from' => 'Birmingham', 'to' => 'NEC & Resorts World', 'price_from' => 15],
                    ['from' => 'Birmingham', 'to' => 'Manchester', 'price_from' => 75],
                ],
                'about' => 'As England\'s second-largest city, Birmingham has a vast private hire network covering the West Midlands metropolitan area. The city\'s central location makes it a popular hub for cross-country transfers, while the NEC and Birmingham Airport generate strong demand for transfer services. Our operators are licensed by Birmingham City Council and surrounding authorities, ensuring full compliance and passenger safety.',
                'tips' => [
                    'If attending events at the NEC or Resorts World, book a taxi well in advance. These venues generate heavy traffic on the A45 corridor, and pre-booked rides guarantee your pickup.',
                    'Birmingham\'s Clean Air Zone covers the city centre within the A4540 ring road. All our operators use compliant vehicles, so there are no extra charges passed on to passengers.',
                    'For journeys to Birmingham Airport, specify your terminal when booking. Terminal 1 and Terminal 2 have separate drop-off areas, and getting the right one saves time on arrival.',
                ],
                'faqs' => [
                    ['q' => 'How much is a taxi from Birmingham to the NEC?', 'a' => 'Fares from Birmingham city centre to the NEC start from around £15 for a standard saloon. The journey takes approximately 20-30 minutes depending on the time of day and which part of the city you\'re travelling from.'],
                    ['q' => 'Is it cheaper to pre-book a taxi from Birmingham Airport?', 'a' => 'Pre-booked airport transfers are typically 30-50% cheaper than the taxi rank at Birmingham Airport. Our operators offer fixed fares so the price won\'t change regardless of traffic.'],
                    ['q' => 'Do Birmingham taxis go to Solihull and the Black Country?', 'a' => 'Yes, our operators cover the entire West Midlands including Solihull, Dudley, Walsall, Sandwell, and Wolverhampton, as well as surrounding areas in Warwickshire and Staffordshire.'],
                    ['q' => 'Can I book a wheelchair-accessible taxi in Birmingham?', 'a' => 'Several of our Birmingham operators offer wheelchair-accessible vehicles. Select the WAV option when searching or contact our support team to arrange a suitable vehicle for your needs.'],
                ],
            ],

            'edinburgh' => [
                'name' => 'Edinburgh',
                'title' => 'Edinburgh Taxi & Minicab Quotes',
                'description' => 'Compare taxi fares from Edinburgh\'s trusted private hire operators. Get quotes for Festival transfers, Edinburgh Airport runs, and scenic journeys across Scotland\'s historic capital.',
                'image' => 'https://picsum.photos/seed/edinburgh/800/400',
                'popular_routes' => [
                    ['from' => 'Edinburgh City Centre', 'to' => 'Edinburgh Airport', 'price_from' => 20],
                    ['from' => 'Edinburgh', 'to' => 'Glasgow', 'price_from' => 45],
                    ['from' => 'Edinburgh', 'to' => 'St Andrews', 'price_from' => 55],
                    ['from' => 'Edinburgh', 'to' => 'Stirling', 'price_from' => 40],
                    ['from' => 'Edinburgh', 'to' => 'Perth', 'price_from' => 55],
                    ['from' => 'Edinburgh', 'to' => 'Newcastle', 'price_from' => 90],
                ],
                'about' => 'Edinburgh attracts millions of visitors each year, particularly during the world-famous Edinburgh Festival in August. The city\'s Georgian New Town and medieval Old Town create a compact centre where taxis are the most convenient way to navigate the hilly streets. Our Edinburgh operators are licensed by the City of Edinburgh Council and offer fixed-price fares throughout the year, with no Festival surge pricing.',
                'tips' => [
                    'During the Edinburgh Festival (August), book airport transfers and city rides at least 48 hours in advance. Demand peaks during this period and last-minute availability can be limited.',
                    'Edinburgh\'s Old Town has narrow cobbled streets with restricted vehicle access. Specify your exact accommodation address when booking so the driver can identify the nearest accessible drop-off point.',
                    'If you\'re heading to the Highlands or St Andrews for golf, many operators offer scenic route options at no extra cost. Just mention your preference in the booking notes.',
                ],
                'faqs' => [
                    ['q' => 'How much is a taxi from Edinburgh Airport to the city centre?', 'a' => 'Pre-booked fares from Edinburgh Airport to the city centre start from around £20 for a standard saloon. The journey takes approximately 25-35 minutes depending on traffic and your exact destination.'],
                    ['q' => 'Can I book a taxi from Edinburgh to Glasgow?', 'a' => 'Yes, Edinburgh to Glasgow is one of Scotland\'s most popular inter-city routes. The journey takes around 1 hour via the M8 motorway, with fares starting from £45.'],
                    ['q' => 'Are Edinburgh taxi prices higher during the Festival?', 'a' => 'Our pre-booked prices remain fixed throughout the year, including during the Edinburgh Festival. This is a significant advantage over hailing taxis on the street, where demand can make it difficult to find available vehicles in August.'],
                    ['q' => 'Do Edinburgh operators cover Leith and Musselburgh?', 'a' => 'Yes, our operators serve the entire Edinburgh area including Leith, Musselburgh, Dalkeith, South Queensferry, and surrounding East Lothian and Midlothian areas.'],
                ],
            ],

            'glasgow' => [
                'name' => 'Glasgow',
                'title' => 'Glasgow Taxi & Minicab Quotes',
                'description' => 'Book affordable taxis across Glasgow and the west of Scotland. Our licensed Glasgow operators provide competitive fares for airport runs, city journeys, and transfers to the Highlands and Loch Lomond.',
                'image' => 'https://picsum.photos/seed/glasgow/800/400',
                'popular_routes' => [
                    ['from' => 'Glasgow City Centre', 'to' => 'Glasgow Airport', 'price_from' => 18],
                    ['from' => 'Glasgow', 'to' => 'Edinburgh', 'price_from' => 45],
                    ['from' => 'Glasgow', 'to' => 'Loch Lomond', 'price_from' => 30],
                    ['from' => 'Glasgow', 'to' => 'Prestwick Airport', 'price_from' => 35],
                    ['from' => 'Glasgow', 'to' => 'Stirling', 'price_from' => 35],
                    ['from' => 'Glasgow', 'to' => 'Oban', 'price_from' => 85],
                ],
                'about' => 'Glasgow is Scotland\'s largest city with a vibrant cultural scene, world-class shopping, and a busy events calendar at venues like the SEC, Hydro, and Hampden Park. The city\'s grid-pattern streets make navigation straightforward, but a pre-booked taxi remains the most reliable option, especially for airport transfers and late-night journeys. Our Glasgow operators hold valid Glasgow City Council licences and operate across the Greater Glasgow area.',
                'tips' => [
                    'Glasgow Airport is 8 miles west of the city centre via the M8. Rush-hour traffic can double the journey time, so book your airport transfer with a generous buffer before your flight.',
                    'After concerts at the OVO Hydro or SEC, the surrounding roads become extremely congested. Pre-book your return taxi and arrange a pickup from the dedicated private hire area on Stobcross Road.',
                    'If you\'re heading to Loch Lomond or the Trossachs, consider booking a return journey as well. Taxi availability in rural areas around the national park is very limited.',
                ],
                'faqs' => [
                    ['q' => 'How do I get a taxi from Glasgow Airport to the city centre?', 'a' => 'Pre-booked taxis from Glasgow Airport to the city centre start from around £18 and take 15-25 minutes outside rush hour. Drivers meet you in the arrivals hall or at the designated pickup point.'],
                    ['q' => 'Is Glasgow Airport or Prestwick Airport closer to the city?', 'a' => 'Glasgow Airport (GLA) is much closer at about 8 miles from the city centre. Prestwick Airport (PIK) is 32 miles south. Fares to Prestwick start from around £35.'],
                    ['q' => 'Can I book a taxi from Glasgow to the Highlands?', 'a' => 'Yes, long-distance transfers to Highland destinations like Fort William, Oban, and Inverness are available. These scenic journeys can be booked with fixed prices so you know the cost upfront.'],
                    ['q' => 'Do Glasgow operators cover Paisley and East Kilbride?', 'a' => 'Our operators cover the entire Greater Glasgow area including Paisley, East Kilbride, Clydebank, Hamilton, Motherwell, and surrounding towns in Renfrewshire, Lanarkshire, and East Dunbartonshire.'],
                ],
            ],

            'liverpool' => [
                'name' => 'Liverpool',
                'title' => 'Liverpool Taxi & Minicab Quotes',
                'description' => 'Compare minicab prices across Merseyside with Liverpool\'s licensed operators. Get competitive quotes for rides to Liverpool Airport, match-day transfers, and journeys around the North West.',
                'image' => 'https://picsum.photos/seed/liverpool/800/400',
                'popular_routes' => [
                    ['from' => 'Liverpool City Centre', 'to' => 'Liverpool John Lennon Airport', 'price_from' => 15],
                    ['from' => 'Liverpool', 'to' => 'Manchester', 'price_from' => 40],
                    ['from' => 'Liverpool', 'to' => 'Manchester Airport', 'price_from' => 45],
                    ['from' => 'Liverpool', 'to' => 'Chester', 'price_from' => 30],
                    ['from' => 'Liverpool', 'to' => 'Anfield Stadium', 'price_from' => 8],
                    ['from' => 'Liverpool', 'to' => 'London', 'price_from' => 195],
                ],
                'about' => 'Liverpool\'s waterfront, music heritage, and Premier League football clubs make it one of the UK\'s most visited cities. The private hire sector is well established across Merseyside, with operators serving everything from quick city-centre rides to Manchester Airport transfers. Our Liverpool operators are licensed by Liverpool City Council and offer transparent, fixed-price fares for all journeys.',
                'tips' => [
                    'On match days at Anfield or Goodison Park, pre-book your taxi and arrange a pickup from a nearby side street rather than the stadium entrance. Post-match road closures make stadium-area pickups difficult.',
                    'Liverpool John Lennon Airport is only 7 miles from the city centre, making it one of the quickest airport transfers in the UK. Even at peak times, the journey rarely exceeds 25 minutes.',
                    'For nights out in the Ropewalks and Baltic Triangle areas, set a specific pickup point on a main road. Many of the smaller streets in these areas are pedestrianised or one-way.',
                ],
                'faqs' => [
                    ['q' => 'How much is a taxi from Liverpool city centre to John Lennon Airport?', 'a' => 'Pre-booked fares to Liverpool John Lennon Airport start from around £15. The journey takes 15-25 minutes from the city centre via the A561.'],
                    ['q' => 'Can I get a taxi from Liverpool to Manchester Airport?', 'a' => 'Yes, this is one of our most popular cross-city routes. The journey takes approximately 50-70 minutes via the M62 and M56, with fares starting from around £45.'],
                    ['q' => 'Are taxis available on match days in Liverpool?', 'a' => 'Demand is very high on match days, so we strongly recommend pre-booking. Our operators are familiar with the road closures and parking restrictions around both Anfield and Goodison Park.'],
                    ['q' => 'Do Liverpool operators cover the Wirral?', 'a' => 'Yes, our operators serve the whole Merseyside area including the Wirral Peninsula, Birkenhead, St Helens, Southport, and surrounding areas via the Mersey Tunnel and Queensway routes.'],
                ],
            ],

            'leeds' => [
                'name' => 'Leeds',
                'title' => 'Leeds Taxi & Minicab Quotes',
                'description' => 'Book taxis across West Yorkshire with our Leeds private hire comparison service. Find the best fares for Leeds Bradford Airport, inter-city transfers, and local journeys throughout the region.',
                'image' => 'https://picsum.photos/seed/leeds/800/400',
                'popular_routes' => [
                    ['from' => 'Leeds City Centre', 'to' => 'Leeds Bradford Airport', 'price_from' => 18],
                    ['from' => 'Leeds', 'to' => 'Manchester', 'price_from' => 45],
                    ['from' => 'Leeds', 'to' => 'York', 'price_from' => 30],
                    ['from' => 'Leeds', 'to' => 'Harrogate', 'price_from' => 22],
                    ['from' => 'Leeds', 'to' => 'Sheffield', 'price_from' => 35],
                    ['from' => 'Leeds', 'to' => 'Manchester Airport', 'price_from' => 55],
                ],
                'about' => 'Leeds is the economic powerhouse of West Yorkshire, with a growing financial district and thriving entertainment scene around the Headrow and Call Lane. The city\'s private hire operators handle a wide range of journeys, from business travel to Leeds Bradford Airport transfers. Operators on our platform are licensed by Leeds City Council and adhere to strict vehicle and driver standards.',
                'tips' => [
                    'Leeds Bradford Airport is situated on a hilltop north of the city and the access road can be congested. Allow at least 35 minutes from the city centre during peak hours.',
                    'If heading to Headingley for cricket or rugby, book your return taxi before the match. The Otley Road corridor becomes heavily congested as events finish.',
                    'For business travellers heading to the financial district around Wellington Street, ask your driver to use the inner ring road to avoid the one-way system through the city centre.',
                ],
                'faqs' => [
                    ['q' => 'How far is Leeds Bradford Airport from the city centre?', 'a' => 'Leeds Bradford Airport is approximately 8 miles northwest of Leeds city centre. Pre-booked taxi fares start from around £18, and the journey typically takes 25-35 minutes.'],
                    ['q' => 'Can I book a taxi from Leeds to York or Harrogate?', 'a' => 'Yes, both are popular inter-city routes. York is about 25 miles east (30-40 minutes, from £30) and Harrogate is 15 miles north (20-30 minutes, from £22).'],
                    ['q' => 'Are there taxis available late at night in Leeds?', 'a' => 'Many of our Leeds operators provide 24-hour service, with availability throughout the night. Pre-booking guarantees a vehicle, especially on busy weekend evenings when the city centre nightlife generates high demand.'],
                    ['q' => 'Do Leeds operators cover Bradford and Wakefield?', 'a' => 'Yes, our operators cover the entire West Yorkshire area including Bradford, Wakefield, Huddersfield, Halifax, and Dewsbury, as well as surrounding North and South Yorkshire areas.'],
                ],
            ],

            'bristol' => [
                'name' => 'Bristol',
                'title' => 'Bristol Taxi & Minicab Quotes',
                'description' => 'Compare taxi prices across Bristol and the South West. Our licensed operators offer great rates for Bristol Airport transfers, harbour-area rides, and journeys to Bath, Cardiff, and beyond.',
                'image' => 'https://picsum.photos/seed/bristol/800/400',
                'popular_routes' => [
                    ['from' => 'Bristol City Centre', 'to' => 'Bristol Airport', 'price_from' => 22],
                    ['from' => 'Bristol', 'to' => 'Bath', 'price_from' => 25],
                    ['from' => 'Bristol', 'to' => 'Cardiff', 'price_from' => 40],
                    ['from' => 'Bristol', 'to' => 'Exeter', 'price_from' => 65],
                    ['from' => 'Bristol', 'to' => 'Cheltenham', 'price_from' => 45],
                    ['from' => 'Bristol', 'to' => 'London', 'price_from' => 145],
                ],
                'about' => 'Bristol\'s creative energy, harbourside attractions, and proximity to Bath make it a popular destination in the South West. The city has a strong private hire market serving the tech corridor around Temple Quarter, the universities, and the conference facilities at Ashton Gate. Our Bristol operators are fully licensed and provide reliable service across the city and surrounding North Somerset area.',
                'tips' => [
                    'Bristol Airport is located 8 miles south of the city in North Somerset. The A38 approach road has limited overtaking opportunities, so build in extra time during holiday periods.',
                    'Bristol\'s harbourside area has restricted vehicle access in places. If staying near the SS Great Britain or Millennium Square, confirm the best drop-off point with your operator.',
                    'The Clifton Suspension Bridge has a toll for vehicles. If your journey routes via Clifton, the small toll charge is typically absorbed by the operator in the pre-booked fare.',
                ],
                'faqs' => [
                    ['q' => 'How much is a taxi from Bristol to Bath?', 'a' => 'Pre-booked fares from Bristol to Bath start from around £25 for a standard saloon. The 12-mile journey takes approximately 25-35 minutes via the A4 or A39.'],
                    ['q' => 'How do I get from Bristol Airport to the city centre by taxi?', 'a' => 'A pre-booked taxi from Bristol Airport to the city centre costs from around £22 and takes 25-35 minutes. Drivers meet you at the arrivals area outside the terminal building.'],
                    ['q' => 'Can I take a taxi from Bristol to Cardiff?', 'a' => 'Yes, Bristol to Cardiff across the Severn Bridge is a popular route taking around 50-60 minutes. The Severn Bridge toll has been removed, so fares start from approximately £40.'],
                    ['q' => 'Do Bristol operators serve South Gloucestershire?', 'a' => 'Yes, our operators cover Bristol, South Gloucestershire (including Bradley Stoke, Thornbury, and Yate), North Somerset, and Bath & North East Somerset.'],
                ],
            ],

            'newcastle' => [
                'name' => 'Newcastle',
                'title' => 'Newcastle Taxi & Minicab Quotes',
                'description' => 'Find the best minicab fares across Tyneside and the North East. Compare quotes from Newcastle\'s licensed operators for airport transfers, match-day travel, and city-to-city journeys.',
                'image' => 'https://picsum.photos/seed/newcastle/800/400',
                'popular_routes' => [
                    ['from' => 'Newcastle City Centre', 'to' => 'Newcastle Airport', 'price_from' => 15],
                    ['from' => 'Newcastle', 'to' => 'Durham', 'price_from' => 25],
                    ['from' => 'Newcastle', 'to' => 'Sunderland', 'price_from' => 15],
                    ['from' => 'Newcastle', 'to' => 'Edinburgh', 'price_from' => 90],
                    ['from' => 'Newcastle', 'to' => 'Middlesbrough', 'price_from' => 35],
                    ['from' => 'Newcastle', 'to' => 'Alnwick', 'price_from' => 35],
                ],
                'about' => 'Newcastle upon Tyne is the North East\'s largest city, famed for its nightlife along the Quayside and Bigg Market, St James\' Park football ground, and the iconic Tyne Bridge. The city\'s private hire operators provide extensive coverage across Tyneside, from Whitley Bay on the coast to the MetroCentre in Gateshead. Our operators are licensed by Newcastle City Council and neighbouring North East authorities.',
                'tips' => [
                    'Newcastle Airport is conveniently located just 7 miles north of the city centre. Journey times are usually under 20 minutes, but allow extra time during the rush hour along the A696.',
                    'The Quayside and Bigg Market areas are popular nightlife hotspots with limited vehicle access on weekend nights. Arrange to be picked up from a main road like the Central Motorway or Grey Street.',
                    'If you\'re visiting the Northumberland coast or Hadrian\'s Wall, pre-book a return journey. Rural taxi availability north of Newcastle is very limited, especially during summer weekends.',
                ],
                'faqs' => [
                    ['q' => 'How much does a taxi cost from Newcastle Airport to the city?', 'a' => 'Pre-booked fares from Newcastle Airport to the city centre start from around £15. The journey takes approximately 15-20 minutes via the A696.'],
                    ['q' => 'Can I get a taxi from Newcastle to Edinburgh?', 'a' => 'Yes, Newcastle to Edinburgh is a popular cross-border route. The 100-mile journey via the A1 takes approximately 2 hours, with fares starting from around £90.'],
                    ['q' => 'Are taxis available for Newcastle United match days?', 'a' => 'St James\' Park is right in the city centre, which means significant road closures on match days. We recommend pre-booking and arranging a pickup point away from the immediate stadium area.'],
                    ['q' => 'Do Newcastle operators cover Gateshead and Sunderland?', 'a' => 'Yes, our operators cover the whole Tyne and Wear area including Gateshead, Sunderland, North and South Shields, Whitley Bay, Washington, and surrounding Northumberland towns.'],
                ],
            ],

            'southampton' => [
                'name' => 'Southampton',
                'title' => 'Southampton Taxi & Minicab Quotes',
                'description' => 'Compare taxi prices for Southampton cruise port transfers, airport runs, and journeys across Hampshire. Our licensed operators specialise in cruise terminal pickups and South Coast travel.',
                'image' => 'https://picsum.photos/seed/southampton/800/400',
                'popular_routes' => [
                    ['from' => 'Southampton', 'to' => 'Southampton Airport', 'price_from' => 12],
                    ['from' => 'Southampton', 'to' => 'Heathrow Airport', 'price_from' => 65],
                    ['from' => 'Southampton', 'to' => 'Gatwick Airport', 'price_from' => 70],
                    ['from' => 'Southampton Cruise Terminal', 'to' => 'London', 'price_from' => 110],
                    ['from' => 'Southampton', 'to' => 'Bournemouth', 'price_from' => 30],
                    ['from' => 'Southampton', 'to' => 'Portsmouth', 'price_from' => 25],
                ],
                'about' => 'Southampton is the UK\'s premier cruise port, handling over 2 million cruise passengers annually. The city\'s four cruise terminals generate enormous demand for taxi transfers, particularly to London airports and the South Coast. Our Southampton operators are experienced with cruise schedules, luggage handling, and early-morning terminal drop-offs. All operators are licensed by Southampton City Council.',
                'tips' => [
                    'If you\'re catching a cruise from Southampton, book your taxi transfer at least 72 hours in advance. Cruise embarkation days (typically Saturdays) create very high demand on the local road network.',
                    'Southampton has four cruise terminals (City Cruise Terminal, Ocean Cruise Terminal, Mayflower, and Horizon). Confirm your exact terminal and berth number when booking to avoid mix-ups.',
                    'For transfers from Southampton to Heathrow, the M3 motorway can be slow between junctions 2-4. Allow at least 90 minutes for the journey, or 2 hours during peak periods.',
                ],
                'faqs' => [
                    ['q' => 'How much is a taxi from Southampton cruise terminal to Heathrow?', 'a' => 'Pre-booked fares from Southampton cruise terminals to Heathrow Airport start from approximately £65 for a standard saloon. The journey takes 75-100 minutes depending on traffic.'],
                    ['q' => 'Can I book a taxi to meet me at Southampton cruise port?', 'a' => 'Yes, our operators regularly handle cruise disembarkation pickups. Drivers monitor ship arrival times and will adjust for any delays. Meet points are clearly communicated before travel.'],
                    ['q' => 'Is Southampton Airport far from the city centre?', 'a' => 'Southampton Airport (Eastleigh) is just 4 miles north of the city centre. Pre-booked fares start from £12, with typical journey times of 10-15 minutes.'],
                    ['q' => 'Do operators cover the New Forest and Isle of Wight ferries?', 'a' => 'Yes, transfers to the New Forest, Lymington (for Isle of Wight ferries), and the Red Funnel terminal in Southampton are all popular routes covered by our operators.'],
                ],
            ],

            'cardiff' => [
                'name' => 'Cardiff',
                'title' => 'Cardiff Taxi & Minicab Quotes',
                'description' => 'Get instant taxi quotes from Cardiff\'s licensed private hire operators. Compare prices for Principality Stadium event transfers, Cardiff Airport runs, and journeys across South Wales.',
                'image' => 'https://picsum.photos/seed/cardiff/800/400',
                'popular_routes' => [
                    ['from' => 'Cardiff City Centre', 'to' => 'Cardiff Airport', 'price_from' => 25],
                    ['from' => 'Cardiff', 'to' => 'Bristol', 'price_from' => 40],
                    ['from' => 'Cardiff', 'to' => 'Swansea', 'price_from' => 40],
                    ['from' => 'Cardiff', 'to' => 'Newport', 'price_from' => 18],
                    ['from' => 'Cardiff', 'to' => 'Brecon Beacons', 'price_from' => 45],
                    ['from' => 'Cardiff', 'to' => 'Heathrow Airport', 'price_from' => 130],
                ],
                'about' => 'Cardiff is the capital of Wales and home to the Principality Stadium, Cardiff Castle, and a thriving Bay area. The city\'s compact centre means most local journeys are short and affordable, while the M4 corridor provides easy connections to Bristol, Swansea, and London. Our Cardiff operators are licensed by Cardiff County Council and specialise in event-day transfers and airport connections.',
                'tips' => [
                    'On international rugby and football match days, the streets around the Principality Stadium are completely closed to traffic. Pre-book and arrange pickup from Cardiff Bay or the outer ring road.',
                    'Cardiff Airport is located in Rhoose, about 12 miles west of the city. The journey takes 25-35 minutes, but there is no motorway connection, so allow extra time during peak periods.',
                    'If exploring the Brecon Beacons or Valleys from Cardiff, book a return journey. Taxi availability in rural Mid Wales is extremely limited, especially on weekends.',
                ],
                'faqs' => [
                    ['q' => 'How much is a taxi from Cardiff to Cardiff Airport?', 'a' => 'Pre-booked fares from Cardiff city centre to Cardiff Airport start from around £25. The journey along the A48 takes approximately 25-35 minutes.'],
                    ['q' => 'Can I get a taxi from Cardiff to Bristol?', 'a' => 'Yes, the Cardiff to Bristol route via the M4 and Severn Crossing is very popular. Since the Severn Bridge tolls were abolished, fares start from around £40 for the 45-minute journey.'],
                    ['q' => 'Are taxis available during events at the Principality Stadium?', 'a' => 'Yes, but road closures mean taxis cannot access streets immediately around the stadium on event days. Pre-book and use the designated pickup points at Cardiff Bay or along Boulevard de Nantes.'],
                    ['q' => 'Do Cardiff operators cover the Vale of Glamorgan?', 'a' => 'Yes, our operators serve Cardiff, the Vale of Glamorgan (including Barry, Penarth, and Cowbridge), Caerphilly, Newport, and the South Wales Valleys.'],
                ],
            ],

            'belfast' => [
                'name' => 'Belfast',
                'title' => 'Belfast Taxi & Minicab Quotes',
                'description' => 'Compare taxi prices across Belfast and Northern Ireland. Our licensed operators offer competitive fares for Belfast International and City Airport transfers, Titanic Quarter rides, and journeys along the Causeway Coast.',
                'image' => 'https://picsum.photos/seed/belfast/800/400',
                'popular_routes' => [
                    ['from' => 'Belfast City Centre', 'to' => 'Belfast International Airport', 'price_from' => 28],
                    ['from' => 'Belfast City Centre', 'to' => 'Belfast City Airport', 'price_from' => 10],
                    ['from' => 'Belfast', 'to' => 'Dublin', 'price_from' => 95],
                    ['from' => 'Belfast', 'to' => 'Giant\'s Causeway', 'price_from' => 75],
                    ['from' => 'Belfast', 'to' => 'Derry~Londonderry', 'price_from' => 70],
                    ['from' => 'Belfast', 'to' => 'Titanic Quarter', 'price_from' => 6],
                ],
                'about' => 'Belfast has undergone a remarkable transformation and is now one of the UK\'s most vibrant cities, with the Titanic Quarter, Cathedral Quarter, and thriving restaurant scene drawing visitors from around the world. The city is served by two airports and is the gateway to the Causeway Coast and Antrim Glens. Our Belfast operators hold valid Department for Infrastructure (DfI) licences and offer reliable service across Northern Ireland.',
                'tips' => [
                    'Belfast has two airports: Belfast International (Aldergrove), 18 miles northwest, and George Best Belfast City Airport, just 3 miles from the centre. Confirm which airport you need when booking.',
                    'The Titanic Quarter and Cathedral Quarter are well served by taxis, but some streets around St George\'s Market and the Cathedral area have restricted access at weekends. Use Victoria Street as your pickup point.',
                    'For day trips to the Giant\'s Causeway or Carrick-a-Rede, book a return taxi in advance. There is virtually no private hire availability along the North Antrim coast.',
                ],
                'faqs' => [
                    ['q' => 'How much is a taxi from Belfast to Belfast International Airport?', 'a' => 'Pre-booked fares from Belfast city centre to Belfast International Airport (Aldergrove) start from around £28. The journey via the M2 motorway takes approximately 25-35 minutes.'],
                    ['q' => 'Can I take a taxi from Belfast to Dublin?', 'a' => 'Yes, Belfast to Dublin is a popular cross-border route. The 100-mile journey via the M1 and A1 takes about 2 hours, with fares starting from around £95.'],
                    ['q' => 'Are Belfast taxis metered or fixed price?', 'a' => 'All pre-booked journeys through our platform are fixed price. You\'ll know exactly what you\'re paying before you travel. This typically works out cheaper than metered fares, especially for airport transfers.'],
                    ['q' => 'Do Belfast operators cover Lisburn and Bangor?', 'a' => 'Yes, our operators serve the Greater Belfast area including Lisburn, Bangor, Newtownabbey, Carrickfergus, Hollywood, and surrounding areas in Antrim and Down.'],
                ],
            ],
        ];
    }

    protected function getAirportData(): array
    {
        return [
            'heathrow' => [
                'name' => 'Heathrow Airport',
                'code' => 'LHR',
                'title' => 'Heathrow Airport Taxi Transfers',
                'description' => 'Pre-book your Heathrow Airport taxi transfer and save. Compare prices from operators covering all five terminals, with meet and greet service and flight monitoring included.',
                'image' => 'https://picsum.photos/seed/heathrow/800/400',
                'terminals' => ['Terminal 2', 'Terminal 3', 'Terminal 4', 'Terminal 5'],
                'popular_routes' => [
                    ['to' => 'London City Centre', 'price_from' => 35, 'duration' => '45-60 min'],
                    ['to' => 'Oxford', 'price_from' => 65, 'duration' => '60-75 min'],
                    ['to' => 'Reading', 'price_from' => 35, 'duration' => '30-40 min'],
                    ['to' => 'Windsor', 'price_from' => 20, 'duration' => '15-20 min'],
                    ['to' => 'Gatwick Airport', 'price_from' => 55, 'duration' => '50-70 min'],
                    ['to' => 'Cambridge', 'price_from' => 95, 'duration' => '90-120 min'],
                ],
                'meet_greet_info' => 'Our Heathrow meet and greet service covers all four active terminals. Your driver will track your flight in real time and wait in the arrivals hall holding a name board. For Terminal 5 (British Airways\' main hub), drivers position themselves near the Costa Coffee after customs. Allow 15-20 minutes from landing to meeting your driver, as Heathrow\'s terminals are large and immigration queues can be lengthy.',
                'parking_tip' => 'Heathrow\'s drop-off charges apply to all terminals. Pre-booked taxis use the designated free pickup zones and the driver will direct you to the correct meeting point, saving you the £5 drop-off fee that applies to private vehicles entering the forecourt.',
                'faqs' => [
                    ['q' => 'How much does a taxi from Heathrow to central London cost?', 'a' => 'Pre-booked fares from Heathrow to central London start from around £35 for a standard saloon. The journey takes 45-60 minutes depending on traffic and your specific London destination.'],
                    ['q' => 'Do I need to tell the operator which Heathrow terminal I\'m using?', 'a' => 'Yes, please specify your terminal when booking. Heathrow has four active terminals (T2, T3, T4, T5) spread across a large area, and each has a separate pickup point. Your terminal is shown on your boarding pass or airline booking.'],
                    ['q' => 'What happens if my flight is delayed at Heathrow?', 'a' => 'Our operators monitor flight arrivals in real time. If your flight is delayed, your driver will adjust their arrival time accordingly. There is typically no extra charge for flight delays of up to 60 minutes.'],
                    ['q' => 'Can I book a taxi between Heathrow terminals?', 'a' => 'For inter-terminal transfers, Heathrow provides free shuttle buses and the Elizabeth Line. However, if you have heavy luggage or limited mobility, a pre-booked taxi between terminals can be arranged from around £15.'],
                ],
            ],

            'gatwick' => [
                'name' => 'Gatwick Airport',
                'code' => 'LGW',
                'title' => 'Gatwick Airport Taxi Transfers',
                'description' => 'Book affordable taxi transfers from Gatwick Airport\'s North and South Terminals. Compare fares to London, Brighton, and destinations across the South East with free flight monitoring.',
                'image' => 'https://picsum.photos/seed/gatwick/800/400',
                'terminals' => ['North Terminal', 'South Terminal'],
                'popular_routes' => [
                    ['to' => 'London City Centre', 'price_from' => 45, 'duration' => '55-75 min'],
                    ['to' => 'Brighton', 'price_from' => 30, 'duration' => '30-40 min'],
                    ['to' => 'Crawley', 'price_from' => 10, 'duration' => '10-15 min'],
                    ['to' => 'Heathrow Airport', 'price_from' => 55, 'duration' => '50-70 min'],
                    ['to' => 'Southampton', 'price_from' => 75, 'duration' => '75-90 min'],
                    ['to' => 'Tunbridge Wells', 'price_from' => 30, 'duration' => '30-40 min'],
                ],
                'meet_greet_info' => 'Gatwick meet and greet is available at both the North and South Terminals. Drivers meet arrivals passengers in the main concourse area after customs. The South Terminal driver meeting point is near WHSmith in arrivals, while the North Terminal point is by the information desk. Your driver will hold a name board and send you a text message when they arrive.',
                'parking_tip' => 'Gatwick has introduced drop-off charges for both terminals. Pre-booked taxi customers avoid this hassle as drivers use the authorised taxi pickup areas. If you are being dropped off for a departure, the driver will use the short-stay car park free zone to minimise costs.',
                'faqs' => [
                    ['q' => 'How do I get from Gatwick to central London by taxi?', 'a' => 'A pre-booked taxi from Gatwick to central London costs from around £45 and takes 55-75 minutes via the M23 and M25. This is often cheaper than two train tickets for couples or families travelling together.'],
                    ['q' => 'Which Gatwick terminal do I need?', 'a' => 'Gatwick has two terminals: North and South. Budget airlines like easyJet typically use the North Terminal, while British Airways uses the South Terminal. Check your airline\'s website or booking confirmation. The terminals are connected by a free shuttle train.'],
                    ['q' => 'Can I book a taxi from Gatwick to Brighton?', 'a' => 'Yes, Gatwick to Brighton is one of our most popular airport routes. The 28-mile journey via the A23 takes approximately 30-40 minutes, with fares from around £30.'],
                    ['q' => 'Is there a taxi rank at Gatwick Airport?', 'a' => 'Yes, but taxi rank fares are significantly more expensive than pre-booked transfers. Pre-booking typically saves 30-40% and guarantees a vehicle waiting for you on arrival.'],
                ],
            ],

            'manchester-airport' => [
                'name' => 'Manchester Airport',
                'code' => 'MAN',
                'title' => 'Manchester Airport Taxi Transfers',
                'description' => 'Pre-book taxi transfers from Manchester Airport\'s three terminals. Compare quotes from local operators for journeys across the North West, Yorkshire, and the Midlands.',
                'image' => 'https://picsum.photos/seed/manairport/800/400',
                'terminals' => ['Terminal 1', 'Terminal 2', 'Terminal 3'],
                'popular_routes' => [
                    ['to' => 'Manchester City Centre', 'price_from' => 22, 'duration' => '25-40 min'],
                    ['to' => 'Liverpool', 'price_from' => 45, 'duration' => '50-65 min'],
                    ['to' => 'Leeds', 'price_from' => 55, 'duration' => '60-80 min'],
                    ['to' => 'Sheffield', 'price_from' => 50, 'duration' => '55-70 min'],
                    ['to' => 'Chester', 'price_from' => 35, 'duration' => '35-45 min'],
                    ['to' => 'Birmingham', 'price_from' => 75, 'duration' => '80-100 min'],
                ],
                'meet_greet_info' => 'Manchester Airport meet and greet is offered at all three terminals. Drivers wait in the arrivals hall with a name board at the designated meeting point. Terminal 1 and 3 share a common arrivals area, while Terminal 2 has a separate building. Your booking confirmation will include the specific meeting point for your terminal and the driver\'s mobile number.',
                'parking_tip' => 'Manchester Airport charges for both short-stay parking and the drop-off zone at all terminals. Pre-booked taxis use dedicated pickup lanes at no extra cost to you. If your flight lands after midnight, confirm with the operator that late-night collection is included in the fare.',
                'faqs' => [
                    ['q' => 'How much is a taxi from Manchester Airport to the city centre?', 'a' => 'Pre-booked fares from Manchester Airport to the city centre start from around £22. Journey time is typically 25-40 minutes depending on which terminal you arrive at and the time of day.'],
                    ['q' => 'Which terminal do I fly from at Manchester Airport?', 'a' => 'Manchester has three terminals. Terminal 1 handles many charter and short-haul flights, Terminal 2 is the newest and serves long-haul carriers, and Terminal 3 handles budget airlines. Your terminal is printed on your boarding pass.'],
                    ['q' => 'Can I get a taxi from Manchester Airport to Liverpool or Leeds?', 'a' => 'Yes, cross-Pennine and inter-city transfers from Manchester Airport are very popular. Liverpool takes 50-65 minutes (from £45) and Leeds takes 60-80 minutes (from £55).'],
                    ['q' => 'Is there a late-night surcharge for Manchester Airport taxis?', 'a' => 'Some operators apply a small night supplement for pickups between midnight and 6am. This will be clearly shown in the fare comparison before you book, so there are no surprises.'],
                ],
            ],

            'stansted' => [
                'name' => 'Stansted Airport',
                'code' => 'STN',
                'title' => 'Stansted Airport Taxi Transfers',
                'description' => 'Book budget-friendly taxi transfers from Stansted Airport. Perfect for late-night arrivals on low-cost airlines, with door-to-door service across London, Essex, and East Anglia.',
                'image' => 'https://picsum.photos/seed/stansted/800/400',
                'terminals' => ['Main Terminal'],
                'popular_routes' => [
                    ['to' => 'London City Centre', 'price_from' => 55, 'duration' => '55-75 min'],
                    ['to' => 'Cambridge', 'price_from' => 35, 'duration' => '30-40 min'],
                    ['to' => 'Colchester', 'price_from' => 35, 'duration' => '35-45 min'],
                    ['to' => 'Chelmsford', 'price_from' => 30, 'duration' => '30-40 min'],
                    ['to' => 'Norwich', 'price_from' => 60, 'duration' => '70-90 min'],
                    ['to' => 'Ipswich', 'price_from' => 50, 'duration' => '55-70 min'],
                ],
                'meet_greet_info' => 'Stansted has a single terminal building, making the meet and greet process straightforward. Your driver will wait at the arrivals exit near the car hire desks. After passing through customs and baggage reclaim, turn right and walk towards the main exit. The driver will hold a name board with your surname and contact you via text when they are in position.',
                'parking_tip' => 'Stansted\'s drop-off zone is located directly outside the terminal with a 15-minute free period. However, the short-stay car park charges apply quickly after that. Pre-booked taxis pick up from the dedicated taxi area in front of the terminal, which is free for authorised operators.',
                'faqs' => [
                    ['q' => 'How much is a taxi from Stansted to central London?', 'a' => 'Pre-booked fares from Stansted to central London start from around £55. Stansted is the furthest London airport from the centre (approximately 40 miles), so the journey takes 55-75 minutes.'],
                    ['q' => 'Is a taxi from Stansted cheaper than the Stansted Express?', 'a' => 'For solo travellers, the train is usually cheaper. However, for groups of 2-4 sharing a taxi, a pre-booked minicab often works out at a similar per-person cost with the added convenience of door-to-door service and no luggage restrictions.'],
                    ['q' => 'My Ryanair flight arrives late at night - can I still get a taxi?', 'a' => 'Yes, many of our Stansted operators specialise in late-night and early-morning transfers. Stansted handles a high volume of budget airline flights arriving after 11pm, and our operators are well prepared for these schedules.'],
                    ['q' => 'Can I book a taxi from Stansted to Cambridge?', 'a' => 'Yes, Stansted to Cambridge is one of the shortest airport-to-city routes we offer. The 30-mile journey takes only 30-40 minutes, with fares starting from around £35.'],
                ],
            ],

            'luton' => [
                'name' => 'Luton Airport',
                'code' => 'LTN',
                'title' => 'Luton Airport Taxi Transfers',
                'description' => 'Pre-book your Luton Airport taxi and skip the long queue at the rank. Compare fares from operators serving London, Hertfordshire, Bedfordshire, and the Home Counties.',
                'image' => 'https://picsum.photos/seed/luton/800/400',
                'terminals' => ['Main Terminal'],
                'popular_routes' => [
                    ['to' => 'London City Centre', 'price_from' => 45, 'duration' => '45-65 min'],
                    ['to' => 'St Albans', 'price_from' => 15, 'duration' => '15-20 min'],
                    ['to' => 'Milton Keynes', 'price_from' => 30, 'duration' => '30-40 min'],
                    ['to' => 'Watford', 'price_from' => 25, 'duration' => '25-35 min'],
                    ['to' => 'Heathrow Airport', 'price_from' => 55, 'duration' => '45-65 min'],
                    ['to' => 'Oxford', 'price_from' => 55, 'duration' => '55-70 min'],
                ],
                'meet_greet_info' => 'At Luton Airport, the meet and greet point is in the arrivals hall near the exit doors. Due to the airport\'s ongoing redevelopment, the exact meeting location may vary. Your operator will send specific instructions by text message before your arrival, including the driver\'s name and contact number. Drivers hold a name board and are typically positioned near the ATMs in arrivals.',
                'parking_tip' => 'Luton Airport\'s drop-off zone now has a charge per visit. The Mid Stay car park offers a cheaper alternative if you need more time. Pre-booked taxis use the priority pickup area which is free for licensed operators, so you avoid all parking costs.',
                'faqs' => [
                    ['q' => 'How much is a taxi from Luton Airport to London?', 'a' => 'Pre-booked fares from Luton Airport to central London start from around £45 for a standard saloon. The 32-mile journey via the M1 takes approximately 45-65 minutes depending on traffic.'],
                    ['q' => 'Is Luton Airport easy to get to by taxi?', 'a' => 'Luton has straightforward access from the M1 motorway (Junction 10). The airport is compact with a single terminal, so navigation is simple. Drivers know the quickest routes from the motorway to the terminal.'],
                    ['q' => 'Can I book an early morning taxi to Luton Airport?', 'a' => 'Yes, Luton handles many early-morning budget flights. Our operators offer pickups from 3am onwards, and early-morning M1 traffic is usually light, making journey times shorter than during the day.'],
                    ['q' => 'How does Luton Airport compare to other London airports by taxi?', 'a' => 'Luton is generally cheaper to reach from north and west London than Gatwick or Stansted. It\'s the closest airport for travellers in Hertfordshire, Bedfordshire, and Buckinghamshire.'],
                ],
            ],

            'edinburgh-airport' => [
                'name' => 'Edinburgh Airport',
                'code' => 'EDI',
                'title' => 'Edinburgh Airport Taxi Transfers',
                'description' => 'Book reliable taxi transfers from Edinburgh Airport to the city centre, Fife, and beyond. Our Scottish operators provide fixed-price fares with free flight tracking and meet and greet.',
                'image' => 'https://picsum.photos/seed/ediairport/800/400',
                'terminals' => ['Main Terminal'],
                'popular_routes' => [
                    ['to' => 'Edinburgh City Centre', 'price_from' => 20, 'duration' => '20-30 min'],
                    ['to' => 'Glasgow', 'price_from' => 55, 'duration' => '60-75 min'],
                    ['to' => 'St Andrews', 'price_from' => 60, 'duration' => '65-80 min'],
                    ['to' => 'Dundee', 'price_from' => 65, 'duration' => '70-85 min'],
                    ['to' => 'Perth', 'price_from' => 55, 'duration' => '55-70 min'],
                    ['to' => 'Stirling', 'price_from' => 45, 'duration' => '45-60 min'],
                ],
                'meet_greet_info' => 'Edinburgh Airport has a single terminal with a clear arrivals area. Your driver will wait just outside the arrivals exit, near the taxi rank area. During the Edinburgh Festival in August, the airport handles a significantly higher volume of passengers, so drivers may position themselves in the short-stay car park and walk to meet you. A text message with exact instructions will be sent before your flight lands.',
                'parking_tip' => 'Edinburgh Airport introduced a drop-off charge for vehicles using the forecourt. Pre-booked taxi customers benefit from the free operator pickup zone, which is a short walk from the terminal exit. Drivers will guide you to the exact location via text.',
                'faqs' => [
                    ['q' => 'How long does a taxi take from Edinburgh Airport to the city centre?', 'a' => 'The journey from Edinburgh Airport to the city centre takes approximately 20-30 minutes via the A8. During Festival season in August, allow an extra 10-15 minutes due to increased traffic.'],
                    ['q' => 'Can I take a taxi from Edinburgh Airport to St Andrews?', 'a' => 'Yes, many visitors fly into Edinburgh for golf at St Andrews. The journey takes about 65-80 minutes via the Forth Road Bridge, with fares starting from around £60.'],
                    ['q' => 'Is a taxi from Edinburgh Airport cheaper than the tram?', 'a' => 'For solo travellers, the Airlink bus or tram is cheaper. For couples or groups sharing a taxi, the per-person cost is similar, with the advantage of door-to-door service and no luggage hassle.'],
                    ['q' => 'Do Edinburgh Airport taxis serve the Highlands?', 'a' => 'Yes, long-distance transfers to Highland destinations such as Inverness, Fort William, and Aviemore can be pre-booked. These are scenic but lengthy journeys, and prices are fixed at booking.'],
                ],
            ],

            'birmingham-airport' => [
                'name' => 'Birmingham Airport',
                'code' => 'BHX',
                'title' => 'Birmingham Airport Taxi Transfers',
                'description' => 'Compare taxi fares from Birmingham Airport to the NEC, Midlands cities, and beyond. Our operators offer fixed-price transfers with meet and greet service at both arrival halls.',
                'image' => 'https://picsum.photos/seed/bhxairport/800/400',
                'terminals' => ['Terminal 1', 'Terminal 2'],
                'popular_routes' => [
                    ['to' => 'Birmingham City Centre', 'price_from' => 18, 'duration' => '20-30 min'],
                    ['to' => 'NEC & Resorts World', 'price_from' => 8, 'duration' => '5-10 min'],
                    ['to' => 'Coventry', 'price_from' => 20, 'duration' => '20-30 min'],
                    ['to' => 'Stratford-upon-Avon', 'price_from' => 30, 'duration' => '30-40 min'],
                    ['to' => 'Warwick', 'price_from' => 22, 'duration' => '20-25 min'],
                    ['to' => 'London', 'price_from' => 120, 'duration' => '120-150 min'],
                ],
                'meet_greet_info' => 'Birmingham Airport has two connected terminals. Meet and greet drivers wait inside the arrivals hall at the designated meeting point, which is clearly signed. Terminal 1 handles most European flights, while Terminal 2 covers domestic and some charter routes. The NEC exhibition centre is just a 5-minute drive from the airport, and drivers are familiar with the complex\'s multiple entrance points.',
                'parking_tip' => 'Birmingham Airport\'s drop-off area is free for the first 10 minutes, after which charges apply. The airport is adjacent to the NEC, so if you are attending an exhibition, a combined airport-NEC transfer is extremely efficient. Pre-booked taxis use the free priority pickup lane.',
                'faqs' => [
                    ['q' => 'How much is a taxi from Birmingham Airport to the city centre?', 'a' => 'Pre-booked fares from Birmingham Airport to the city centre start from around £18. The journey takes approximately 20-30 minutes via the A45 Coventry Road.'],
                    ['q' => 'Is the NEC close to Birmingham Airport?', 'a' => 'Yes, the NEC is less than a mile from Birmingham Airport. A taxi transfer takes only 5-10 minutes and costs from around £8, making it the quickest and easiest way to travel between the two.'],
                    ['q' => 'Can I get a taxi from Birmingham Airport to Stratford-upon-Avon?', 'a' => 'Yes, many international visitors fly into Birmingham for visits to Shakespeare\'s birthplace. Stratford-upon-Avon is approximately 25 miles away, with fares from around £30 and journey times of 30-40 minutes.'],
                    ['q' => 'Do Birmingham Airport taxis run late at night?', 'a' => 'Yes, our operators provide 24-hour coverage at Birmingham Airport. Late-night flights are common and drivers are available for arrivals at any hour, though a small night supplement may apply.'],
                ],
            ],

            'bristol-airport' => [
                'name' => 'Bristol Airport',
                'code' => 'BRS',
                'title' => 'Bristol Airport Taxi Transfers',
                'description' => 'Pre-book taxi transfers from Bristol Airport to the city, Bath, and the South West. Our operators provide door-to-door service with competitive fixed prices and free wait time for delays.',
                'image' => 'https://picsum.photos/seed/brsairport/800/400',
                'terminals' => ['Main Terminal'],
                'popular_routes' => [
                    ['to' => 'Bristol City Centre', 'price_from' => 22, 'duration' => '25-35 min'],
                    ['to' => 'Bath', 'price_from' => 30, 'duration' => '35-45 min'],
                    ['to' => 'Weston-super-Mare', 'price_from' => 18, 'duration' => '20-25 min'],
                    ['to' => 'Cardiff', 'price_from' => 50, 'duration' => '55-70 min'],
                    ['to' => 'Taunton', 'price_from' => 40, 'duration' => '40-50 min'],
                    ['to' => 'Cheltenham', 'price_from' => 55, 'duration' => '60-75 min'],
                ],
                'meet_greet_info' => 'Bristol Airport has a single terminal. Your driver will meet you in the arrivals area just outside the exit doors, near the car hire desks. Bristol Airport is relatively compact, so the walk from baggage reclaim to the meeting point is short. The driver will contact you by text as your flight lands and will be holding a name board at the agreed meeting point.',
                'parking_tip' => 'Bristol Airport offers limited free drop-off time in the Silver Zone car park, but the closest drop-off area to the terminal is chargeable. Pre-booked taxis pick up from the designated taxi rank directly outside arrivals, avoiding all parking fees.',
                'faqs' => [
                    ['q' => 'How far is Bristol Airport from the city centre?', 'a' => 'Bristol Airport is approximately 8 miles south of Bristol city centre, in the village of Lulsgate Bottom. Pre-booked taxis take 25-35 minutes and cost from around £22.'],
                    ['q' => 'Can I get a taxi from Bristol Airport to Bath?', 'a' => 'Yes, many visitors fly into Bristol to visit the Roman Baths and Georgian architecture. The journey to Bath takes 35-45 minutes and fares start from around £30. This is often the most cost-effective option for groups.'],
                    ['q' => 'Is Bristol Airport well served by taxis late at night?', 'a' => 'Yes, Bristol Airport handles evening flights from European destinations and our operators are available for late-night pickups. Pre-booking guarantees a vehicle will be waiting when you land.'],
                    ['q' => 'Can I get a taxi from Bristol Airport to Cardiff?', 'a' => 'Yes, the cross-border route to Cardiff via the M4 and Severn Crossing takes 55-70 minutes, with pre-booked fares from around £50.'],
                ],
            ],

            'glasgow-airport' => [
                'name' => 'Glasgow Airport',
                'code' => 'GLA',
                'title' => 'Glasgow Airport Taxi Transfers',
                'description' => 'Book fixed-price taxi transfers from Glasgow Airport to the city centre, Edinburgh, and destinations across Scotland. Our operators provide meet and greet with full flight tracking.',
                'image' => 'https://picsum.photos/seed/glaairport/800/400',
                'terminals' => ['Main Terminal'],
                'popular_routes' => [
                    ['to' => 'Glasgow City Centre', 'price_from' => 18, 'duration' => '15-25 min'],
                    ['to' => 'Edinburgh', 'price_from' => 55, 'duration' => '60-75 min'],
                    ['to' => 'Loch Lomond', 'price_from' => 35, 'duration' => '30-40 min'],
                    ['to' => 'Stirling', 'price_from' => 40, 'duration' => '40-50 min'],
                    ['to' => 'Ayr', 'price_from' => 35, 'duration' => '35-45 min'],
                    ['to' => 'Fort William', 'price_from' => 100, 'duration' => '120-150 min'],
                ],
                'meet_greet_info' => 'Glasgow Airport has a single main terminal. Arrivals are on the ground floor, and your driver will wait at the meeting point near the information desk by the exit doors. The airport is compact, so you will typically meet your driver within 5 minutes of leaving baggage reclaim. For international arrivals, allow a little longer for passport control queues.',
                'parking_tip' => 'Glasgow Airport has a drop-off zone directly in front of the terminal with time-limited free access. Pre-booked taxi pickups use the taxi rank area just outside arrivals. If you are being collected from domestic arrivals, the driver will text you the exact location.',
                'faqs' => [
                    ['q' => 'How much is a taxi from Glasgow Airport to the city centre?', 'a' => 'Pre-booked fares from Glasgow Airport to Glasgow city centre start from around £18. The 8-mile journey via the M8 takes approximately 15-25 minutes.'],
                    ['q' => 'Can I get a taxi from Glasgow Airport to Edinburgh?', 'a' => 'Yes, Glasgow Airport to Edinburgh is a popular route for connecting travellers. The journey via the M8 takes approximately 60-75 minutes, with fares from around £55.'],
                    ['q' => 'Are taxis from Glasgow Airport available 24 hours?', 'a' => 'Yes, our Glasgow Airport operators run 24/7 to accommodate flights arriving at all hours. Early morning and late-night pickups are available, with some operators applying a small supplement for antisocial hours.'],
                    ['q' => 'Can I take a taxi from Glasgow Airport to Loch Lomond?', 'a' => 'Yes, Loch Lomond is only 30-40 minutes from Glasgow Airport, making it a popular first stop for visitors exploring the Scottish Highlands. Fares start from around £35.'],
                ],
            ],

            'newcastle-airport' => [
                'name' => 'Newcastle Airport',
                'code' => 'NCL',
                'title' => 'Newcastle Airport Taxi Transfers',
                'description' => 'Pre-book taxi transfers from Newcastle Airport to the city centre, Sunderland, and across the North East. Our operators offer competitive fixed fares with complimentary meet and greet.',
                'image' => 'https://picsum.photos/seed/nclairport/800/400',
                'terminals' => ['Main Terminal'],
                'popular_routes' => [
                    ['to' => 'Newcastle City Centre', 'price_from' => 15, 'duration' => '15-20 min'],
                    ['to' => 'Sunderland', 'price_from' => 22, 'duration' => '25-35 min'],
                    ['to' => 'Durham', 'price_from' => 25, 'duration' => '25-35 min'],
                    ['to' => 'Middlesbrough', 'price_from' => 40, 'duration' => '45-55 min'],
                    ['to' => 'Alnwick & Northumberland', 'price_from' => 35, 'duration' => '35-45 min'],
                    ['to' => 'Edinburgh', 'price_from' => 90, 'duration' => '110-130 min'],
                ],
                'meet_greet_info' => 'Newcastle Airport has a single terminal with a straightforward arrivals area. Your driver will meet you at the exit point from baggage reclaim, near the car hire desks. Newcastle is a smaller airport, so walking distances are short and you should meet your driver within a few minutes of collecting your luggage. The driver will send a text as your flight lands.',
                'parking_tip' => 'Newcastle Airport has a free 10-minute drop-off zone close to the terminal. For pickups, pre-booked taxis wait in the designated rank area immediately outside arrivals. The airport also has a Metro station for those preferring public transport into the city.',
                'faqs' => [
                    ['q' => 'How far is Newcastle Airport from the city centre?', 'a' => 'Newcastle Airport is just 7 miles north of the city centre. A pre-booked taxi takes 15-20 minutes and costs from around £15, making it one of the most convenient airport-to-city transfers in the UK.'],
                    ['q' => 'Can I get a taxi from Newcastle Airport to Durham?', 'a' => 'Yes, Newcastle Airport to Durham is a popular route for visitors to the cathedral city. The journey takes approximately 25-35 minutes, with fares from around £25.'],
                    ['q' => 'Do Newcastle Airport taxis go to the Northumberland coast?', 'a' => 'Yes, transfers to Alnwick, Bamburgh, and the Northumberland coast are available. These scenic routes can be pre-booked with fixed prices, and return journeys can be arranged for day trips.'],
                    ['q' => 'Is there a Metro from Newcastle Airport?', 'a' => 'Yes, the Tyne and Wear Metro connects the airport to the city centre. However, for groups with luggage or those arriving late at night, a pre-booked taxi is more convenient and cost-effective.'],
                ],
            ],

            'leeds-bradford' => [
                'name' => 'Leeds Bradford Airport',
                'code' => 'LBA',
                'title' => 'Leeds Bradford Airport Taxi Transfers',
                'description' => 'Book affordable taxi transfers from Leeds Bradford Airport to Leeds, Bradford, Harrogate, and across Yorkshire. Our operators navigate the hilltop airport road with ease, whatever the weather.',
                'image' => 'https://picsum.photos/seed/lbaairport/800/400',
                'terminals' => ['Main Terminal'],
                'popular_routes' => [
                    ['to' => 'Leeds City Centre', 'price_from' => 18, 'duration' => '25-35 min'],
                    ['to' => 'Bradford', 'price_from' => 15, 'duration' => '20-30 min'],
                    ['to' => 'Harrogate', 'price_from' => 22, 'duration' => '20-30 min'],
                    ['to' => 'York', 'price_from' => 35, 'duration' => '40-50 min'],
                    ['to' => 'Manchester', 'price_from' => 55, 'duration' => '60-80 min'],
                    ['to' => 'Sheffield', 'price_from' => 45, 'duration' => '50-65 min'],
                ],
                'meet_greet_info' => 'Leeds Bradford Airport has a single terminal building. Your driver will meet you in the arrivals area near the main exit. The airport sits on high ground between Leeds and Bradford, and the terminal is compact, so the walk from baggage reclaim to the meeting point is very short. Drivers send a text message with their location and vehicle details as your flight lands.',
                'parking_tip' => 'Leeds Bradford Airport has a drop-off zone with a charge that applies immediately. The short-stay car park opposite the terminal is an alternative for longer waits. Pre-booked taxis use the authorised rank outside arrivals at no additional cost.',
                'faqs' => [
                    ['q' => 'How much is a taxi from Leeds Bradford Airport to Leeds?', 'a' => 'Pre-booked fares from Leeds Bradford Airport to Leeds city centre start from around £18. The journey takes approximately 25-35 minutes via the A658 and A65.'],
                    ['q' => 'Is Leeds Bradford Airport hard to reach in winter?', 'a' => 'The airport sits at one of the highest points in the area, and the access road can be affected by winter weather. Our operators are experienced with these conditions and will adjust timing and routes as needed.'],
                    ['q' => 'Can I get a taxi from Leeds Bradford Airport to Harrogate?', 'a' => 'Yes, Harrogate is actually closer to the airport than Leeds city centre. The journey takes about 20-30 minutes, with fares from around £22. It\'s a popular route for visitors to the spa town.'],
                    ['q' => 'Do Leeds Bradford Airport taxis serve the Yorkshire Dales?', 'a' => 'Yes, transfers to Skipton, Ilkley, and the Yorkshire Dales are available. Pre-book your return journey as well, since taxi availability in the Dales is very limited.'],
                ],
            ],

            'southampton-airport' => [
                'name' => 'Southampton Airport',
                'code' => 'SOU',
                'title' => 'Southampton Airport Taxi Transfers',
                'description' => 'Pre-book your Southampton Airport taxi transfer for the quickest route to the cruise port, New Forest, and South Coast destinations. Our local operators know every shortcut in Hampshire.',
                'image' => 'https://picsum.photos/seed/souairport/800/400',
                'terminals' => ['Main Terminal'],
                'popular_routes' => [
                    ['to' => 'Southampton City Centre', 'price_from' => 12, 'duration' => '10-15 min'],
                    ['to' => 'Southampton Cruise Terminal', 'price_from' => 15, 'duration' => '15-20 min'],
                    ['to' => 'Winchester', 'price_from' => 18, 'duration' => '15-20 min'],
                    ['to' => 'Portsmouth', 'price_from' => 28, 'duration' => '30-40 min'],
                    ['to' => 'Bournemouth', 'price_from' => 35, 'duration' => '35-45 min'],
                    ['to' => 'New Forest (Lyndhurst)', 'price_from' => 22, 'duration' => '20-30 min'],
                ],
                'meet_greet_info' => 'Southampton Airport is one of the UK\'s smallest commercial airports, making it extremely easy to navigate. Your driver will meet you at the arrivals exit, which is just a short walk from the baggage carousel. The single terminal handles all flights, and there is rarely a queue at passport control. The driver will be holding a name board at the exit.',
                'parking_tip' => 'Southampton Airport has a free 10-minute pick-up/drop-off zone in the car park. However, pre-booked taxis wait at the taxi rank directly outside arrivals for the quickest possible transfer. The airport\'s compact size means you can be in your taxi within minutes of collecting your bags.',
                'faqs' => [
                    ['q' => 'How far is Southampton Airport from the cruise terminal?', 'a' => 'Southampton Airport is only 4-5 miles from the main cruise terminals. A pre-booked taxi takes 15-20 minutes and costs from around £15, making it an ideal connection for cruise passengers.'],
                    ['q' => 'Can I get a taxi from Southampton Airport to the New Forest?', 'a' => 'Yes, the New Forest is a popular destination for visitors arriving at Southampton Airport. Lyndhurst, the forest\'s main village, is only 20-30 minutes away, with fares from around £22.'],
                    ['q' => 'Is Southampton Airport easy to get to?', 'a' => 'Southampton Airport is located in Eastleigh, just off the M27. Its small size and straightforward layout make it one of the easiest UK airports to navigate. The airport also has its own railway station.'],
                    ['q' => 'Do taxis from Southampton Airport go to Portsmouth?', 'a' => 'Yes, Portsmouth is approximately 20 miles from Southampton Airport. The journey via the M27 takes 30-40 minutes, with fares from around £28. This is a convenient route for ferry passengers heading to the Isle of Wight.'],
                ],
            ],
        ];
    }
}
