# TaxiAggregator

UK taxi aggregator platform (Minicabit-style). Connects passengers with hundreds of licensed taxi operators, providing real-time price comparison and online booking.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+ / Laravel 11 |
| Database | MySQL 8 |
| Frontend | Bootstrap 5 + Alpine.js |
| Build | Vite |
| Payment | Stripe Checkout + Webhooks |
| Maps | Google Maps Places Autocomplete + Distance Matrix |
| Email | Laravel Notifications (mail + database) |
| Testing | PHPUnit (195 tests, 405 assertions) |

## Features

### Passenger
- Search & compare quotes from multiple operators
- Google Maps address autocomplete (UK restricted)
- Real distance calculation via Distance Matrix API
- Online booking with Stripe payment
- Booking management (view, cancel, review)
- 5-star rating system with sub-ratings (timing, fare, driver, vehicle, route)
- Invoice with VAT breakdown (20% UK standard)
- Cancellation policy with time-based refunds
- Notification centre (email + in-app)
- GDPR account deletion

### Operator (Minicabit-style Dashboard)
- Dark sidebar with VIEW / ACTIONS / PRICING / AVAILABILITY sections
- **Pricing Engine** (3-tier priority: PAP > LP > PMP)
  - Per Mile Prices (PMP) with mileage brackets + uplifts
  - Location Prices (LP) with postcode radius matching
  - Postcode Area Prices (PAP) for 124 UK areas
- Meet & Greet charges for 59 airports/stations/ports
- Flash Sales with fleet type targeting
- Dead Leg Discounts
- Free Pickup Postcodes
- Fleet & driver management
- Vehicle availability (7-day grid per fleet type)
- Notice periods + postcode lead times
- Trip range (pickup/dropoff radius)
- Operating hours (24h or custom)
- Pause availability (immediate + scheduled)
- Booking log with search/filters
- Trip Issues & Ratings (3 tabs)
- Financial statements
- My Account (6 tabs: company, contact, authorised contacts, licence, payment, password)
- Price checker / top routes

### Admin Panel
- Dashboard with real-time stats
- Operator management (approve/reject/suspend/tier/commission)
- Booking management with admin notes
- Revenue dashboard with period filtering
- Dispute management with message thread
- User management (activate/deactivate)
- Fleet type management
- System settings
- Trip issues overview
- Statement management

### Platform
- Role-based access (passenger, operator, driver, admin)
- UK Postcode validation
- VAT calculation service (20%)
- Cancellation policy engine (5-tier time-based refunds)
- Printable invoice with VAT breakdown
- 8 email notification types (queued)
- Stripe Checkout + webhook handling + refunds
- Cookie consent banner (GDPR)
- Privacy Policy, Terms of Service, Cookie Policy pages
- SEO meta tags + sitemap.xml + robots.txt
- Security headers (X-Frame-Options, CSP, etc.)
- Rate limiting (search: 10/min, booking: 5/min)
- Configurable brand name + logo via .env

## Database

37 tables covering:
- Users & auth (4 tables)
- Operators & fleet (4 tables)
- Drivers (1 table)
- Pricing (8 tables: PMP, LP, PAP, meet & greet, flash sales, dead leg, free pickup)
- Availability (6 tables)
- Quotes & bookings (3 tables)
- Payments (1 table)
- Reviews & issues (3 tables)
- Disputes (2 tables)
- Statements (2 tables)
- Notifications (2 tables)

## Seeded Data

- 8 fleet types (1-4 passengers through 15-16 passengers)
- 123 UK postcode areas with coordinates
- 59 meet & greet locations (airports, stations, ports)

## Testing

```bash
php artisan test
```

195 tests, 405 assertions covering:
- Pricing calculators (PMP, LP, PAP) - 49 unit tests
- Quote engine + availability checker - 13 unit tests
- VAT service - 14 unit tests
- Cancellation service - 12 unit tests
- UK postcode helper - 18 unit tests
- Auth flow - 10 feature tests
- Booking lifecycle - 14 feature tests
- Operator account/pricing/availability - 28 feature tests
- Admin panel - 14 feature tests
- Passenger portal - 5 feature tests

## Quick Start

```bash
# Clone
git clone https://github.com/s4rt4/taxi-aggregator.git
cd taxi-aggregator

# Install
composer install
npm install

# Configure
cp .env.example .env
php artisan key:generate

# Set in .env:
# DB_CONNECTION=mysql
# DB_DATABASE=taxi_aggregator
# GOOGLE_MAPS_API_KEY=your_key
# STRIPE_KEY=pk_test_...
# STRIPE_SECRET=sk_test_...

# Database
php artisan migrate --seed

# Build & serve
npm run build
php artisan serve
```

### Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@test.com | password |
| Operator | operator@test.com | password |
| Passenger | passenger@test.com | password |

## Configuration

```env
APP_NAME=TaxiAggregator
APP_BRAND_PREFIX=taxi
APP_BRAND_HIGHLIGHT=aggregat
APP_BRAND_SUFFIX=or
APP_BRAND_LOGO=              # path to logo image (optional)
```

## Routes

115+ routes across 4 role areas:
- Public: homepage, search, legal pages, sitemap
- Passenger: booking flow, portal, profile, notifications, invoices
- Operator: 18+ dashboard pages with full CRUD
- Admin: operators, bookings, revenue, disputes, users, settings

## License

Proprietary. All rights reserved.
