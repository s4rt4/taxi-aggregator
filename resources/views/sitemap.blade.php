{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('privacy-policy') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{ route('terms-of-service') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{ route('cookie-policy') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{ route('login') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('register') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    {{-- City landing pages --}}
    @foreach(['london', 'manchester', 'birmingham', 'edinburgh', 'glasgow', 'liverpool', 'leeds', 'bristol', 'newcastle', 'southampton', 'cardiff', 'belfast'] as $citySlug)
    <url>
        <loc>{{ route('city.show', $citySlug) }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    {{-- Airport landing pages --}}
    @foreach(['heathrow', 'gatwick', 'manchester-airport', 'stansted', 'luton', 'edinburgh-airport', 'birmingham-airport', 'bristol-airport', 'glasgow-airport', 'newcastle-airport', 'leeds-bradford', 'southampton-airport'] as $airportSlug)
    <url>
        <loc>{{ route('airport.show', $airportSlug) }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
