# RushXO - UK Taxi Aggregator Platform

UK taxi aggregator platform (Minicabit-style). Connects passengers with hundreds of licensed taxi operators, providing real-time price comparison and online booking.

## Branches

| Branch | Description |
|--------|------------|
| `main` | Full platform with iCabbi integration |
| `feature/icabbi-integration` | Same as main (kept for reference) |

Both branches include all features: admin roles, site settings, city/airport pages, iCabbi dispatch.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+ / Laravel 11 |
| Database | MySQL 8 |
| Frontend | Bootstrap 5 + Alpine.js |
| Build | Vite |
| Payment | Stripe Checkout + Webhooks |
| Maps | Google Maps Places Autocomplete + Distance Matrix |
| SMS | Twilio |
| Email | Laravel Notifications (mail + database) |
| WebSocket | Laravel Reverb (real-time) |
| Dispatch | iCabbi API (optional, per operator) |
| Testing | PHPUnit (211 tests, 422 assertions) |

## Features

### Passenger
- Search & compare quotes from multiple operators
- Google Maps address autocomplete (UK restricted)
- Real distance calculation via Distance Matrix API
- Online booking with Stripe payment
- Booking management (view, cancel, review)
- 5-star rating system with sub-ratings (timing, fare, driver, vehicle, route)
- Invoice with VAT breakdown (20% UK standard)
- Cancellation policy with time-based refunds (48h=100%, 24h=75%, 4h=50%, 2h=25%)
- Notification centre (email + SMS + in-app)
- GDPR account deletion with data anonymisation

### Operator (Minicabit-style Dashboard)
- Dark sidebar with VIEW / ACTIONS / PRICING / AVAILABILITY sections
- **5-step onboarding wizard** with UK legal compliance notices
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
- My Account (7 tabs: company, contact, authorised contacts, licence, payment, password, iCabbi)
- Price checker / top routes
- **iCabbi dispatch integration** (optional, configurable per operator)

### Admin Panel
- Dashboard with real-time stats
- **Role-based access control** with 22 permissions across 6 groups
- **4 system roles**: Super Admin, Admin, Finance, Support
- **Custom roles** with checkbox permissions (created by Super Admin)
- **Admin user management** (only Super Admin can create admin accounts)
- Operator management (approve/reject/suspend/tier/commission)
- Booking management with admin notes
- Revenue dashboard with period filtering
- Dispute management with message thread
- User management (activate/deactivate)
- Fleet type management
- **Dynamic site settings** (company name, contact info, social links - editable via GUI)
- Trip issues overview
- Statement management

### SEO Landing Pages
- **12 city pages**: London, Manchester, Birmingham, Edinburgh, Glasgow, Liverpool, Leeds, Bristol, Newcastle, Southampton, Cardiff, Belfast
- **12 airport pages**: Heathrow, Gatwick, Manchester, Stansted, Luton, Edinburgh, Birmingham, Bristol, Glasgow, Newcastle, Leeds Bradford, Southampton
- Each page has unique content (descriptions, tips, FAQs, popular routes)
- `/taxi/{city}` and `/airport-taxi/{airport}` URL structure

### Static Pages
- About Us, How It Works, For Operators, Contact Us
- Privacy Policy, Terms of Service, Cookie Policy (UK GDPR)
- XML sitemap with all pages

### Platform
- Role-based access (passenger, operator, driver, admin)
- **Admin roles & permissions** (22 permissions, custom roles, Super Admin only creates admins)
- **Database-driven site settings** (editable from Admin > Settings GUI)
- UK Postcode validation
- VAT calculation service (20%)
- Cancellation policy engine (5-tier time-based refunds)
- Printable invoice with VAT breakdown
- 8 email notification types (queued)
- SMS notifications via Twilio
- Real-time WebSocket via Laravel Reverb
- Stripe Checkout + webhook handling + refunds
- **iCabbi dispatch** (Add/Cancel/Update Booking endpoints)
- Cookie consent banner (GDPR)
- SEO meta tags + sitemap.xml + robots.txt
- Security headers (X-Frame-Options, XSS, etc.)
- Rate limiting (search: 10/min, booking: 5/min)
- Configurable brand name + logo via .env and admin GUI

## Database

40 tables covering:
- Users & auth (4 tables)
- Admin roles (1 table)
- Site settings (1 table)
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
- 4 admin roles (Super Admin, Admin, Finance, Support)
- 14 site settings (company info, contact, social links)

## Testing

```bash
php artisan test
```

211 tests, 422 assertions covering:
- Pricing calculators (PMP, LP, PAP) - 49 unit tests
- Quote engine + availability checker - 13 unit tests
- VAT service - 14 unit tests
- Cancellation service - 12 unit tests
- UK postcode helper - 18 unit tests
- SMS service - 6 unit tests
- Dispatch manager - 9 unit tests
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
# TWILIO_SID=your_sid (optional)
# TWILIO_AUTH_TOKEN=your_token (optional)
# TWILIO_FROM_NUMBER=+44... (optional)

# Database
php artisan migrate --seed

# Build & serve
npm run build
php artisan serve
```

## Test Accounts

All passwords: `password123`

| Email | Role | Access |
|-------|------|--------|
| superadmin@test.com | Super Admin | Full admin + manage admins/roles |
| admin@test.com | Admin | Operators, bookings, disputes, users |
| finance@test.com | Finance | Revenue, statements |
| support@test.com | Support | Bookings, disputes, issues |
| operator@test.com | Operator | Operator dashboard |
| passenger@test.com | Passenger | Search, book, review |

## Configuration

### .env (brand)
```env
APP_NAME=RushXO
APP_BRAND_PREFIX=rush
APP_BRAND_HIGHLIGHT=x
APP_BRAND_SUFFIX=o
APP_BRAND_LOGO=              # path to logo image (optional)
```

### Admin GUI Settings (Admin > Settings)
All company info is editable from the admin panel without touching code:
- Company name, legal name, registration, VAT
- Email, phone, WhatsApp, address
- Website URL, tagline
- Social media links (Facebook, X, Instagram, LinkedIn)

## Admin Roles & Permissions

| Permission Group | Permissions |
|-----------------|-------------|
| Operators | view, approve, suspend, edit-tier, edit-commission |
| Bookings | view, edit-status, add-notes |
| Financial | revenue.view, statements.view, statements.manage |
| Quality | disputes.view, disputes.resolve, issues.view |
| Users | users.view, users.manage |
| System | settings.view, settings.edit, fleet-types.manage, admin-users.view, admin-users.manage, admin-roles.manage |

Super Admin can create custom roles with any combination of these 22 permissions.

## iCabbi Integration

Optional per-operator dispatch integration. See [icabbi-setup.md](icabbi-setup.md) for full setup guide.

- Operator enables iCabbi in My Account > iCabbi Integration tab
- Bookings auto-dispatch to iCabbi when operator accepts
- Webhook receives status updates from iCabbi
- Supports Add/Update/Cancel Booking endpoints

## Routes

140+ routes across 5 areas:
- Public: homepage, search, 24 city/airport landing pages, legal pages, sitemap
- Passenger: booking flow, portal, profile, notifications, invoices
- Operator: 18+ dashboard pages with full CRUD + onboarding wizard
- Admin: operators, bookings, revenue, disputes, users, settings, admin users, roles
- Webhooks: Stripe, iCabbi (CSRF exempt)

## Documentation

- [README.md](README.md) - This file
- [before-live.md](before-live.md) - Production deployment checklist
- [icabbi-setup.md](icabbi-setup.md) - iCabbi integration guide
- [framework.md](framework.md) - Original 6-month development plan

## License

Proprietary. All rights reserved.
