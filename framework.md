# KERANGKA KERJA PROYEK

# Taxi Aggregator Platform

*Minicabit-Style — UK Market*

---

## 1. Ringkasan Eksekutif

Platform agregator taksi berbasis web untuk pasar UK. Menghubungkan penumpang dengan ratusan
operator taksi independen, memberikan perbandingan harga real-time, dan memfasilitasi pemesanan online.

*Referensi utama: Minicabit, Cabcompare, Kabbee — semuanya beroperasi di UK.*

| Parameter | Detail |
|---|---|
| Tech Stack | PHP 8.2+ (Laravel 11) + MySQL 8 + Bootstrap 5 + Alpine.js |
| Real-time | Laravel Reverb (WebSocket) + Email (Mailgun) + SMS (Vonage/Twilio) |
| Payment | Stripe Checkout + Stripe Connect (Express) |
| Maps | Google Maps Platform API |
| Target Market | United Kingdom |
| Timeline | 6 bulan (26 minggu) |
| Compliance | GDPR, PCI-DSS (via Stripe), UK Transport Licensing |

---

## 2. Arsitektur Sistem

### 2.1 Stack Teknologi

| Layer | Teknologi | Fungsi |
|---|---|---|
| Backend | Laravel 11 | Framework utama, routing, ORM, auth |
| Frontend | Bootstrap 5 + Alpine.js | UI responsive, mobile-first |
| Database | MySQL 8 | Primary data store |
| Cache/Queue | Redis | Session, job queue, real-time cache |
| WebSocket | Laravel Reverb | Notifikasi real-time |
| Email | Laravel Mail + Mailgun | Transactional email |
| SMS | Vonage / Twilio | OTP, booking status |
| Maps | Google Maps Platform | Autocomplete, distance matrix, routing |
| Payment | Stripe | Checkout, Connect, Webhooks, Refund |
| Storage | Laravel Storage + S3 | Dokumen operator, foto driver |
| Server | Nginx + PHP-FPM | Production web server |

### 2.2 User Roles

| Role | Deskripsi | Akses Utama |
|---|---|---|
| Passenger | Penumpang / customer | Search, book, pay, review, track |
| Operator | Perusahaan / individu taksi | Manage fleet, pricing, terima/tolak job |
| Driver | Pengemudi (opsional MVP) | Lihat job, update status perjalanan |
| Admin | Tim platform | Full access, approval, monitoring |

---

## 3. Database Schema (37 tabel)

> **Status: DONE** — Seluruh migration sudah dibuat dan di-push ke GitHub.

### 3.1 Core Tables
- `users` — roles (passenger/operator/driver/admin), phone, soft delete
- `password_reset_tokens`, `sessions` — Auth Laravel
- `cache`, `cache_locks` — Cache layer
- `jobs`, `job_batches`, `failed_jobs` — Queue system

### 3.2 Operator Tables
- `operators` — Company details, contact, licence, payment type, tier, Stripe Connect
- `operator_contacts` — Authorised contacts (primary + secondary)

### 3.3 Fleet & Driver Tables
- `fleet_types` — Vehicle sizes by passenger count (1-4, 5-6, 7, 8, 9, 10-14, 15-16)
- `vehicles` — Actual vehicles per operator
- `drivers` — Licence, mobile, vehicle info, DBS check status

### 3.4 Pricing Tables (Minicabit-style, 3-tier priority)
```
Prioritas: PAP > LP > PMP
```
- `per_mile_prices` — Base rate per mile per fleet type
- `per_mile_price_ranges` — Mileage bracket rates (0-5mi, 5-10mi, dst)
- `per_mile_uplifts` — Uplift % grid (distance band x fleet type)
- `location_prices` — Fixed price antara 2 postcode + radius
- `postcode_areas` — Referensi UK postcode areas
- `postcode_area_prices` — Harga per pasangan postcode area
- `meet_greet_locations` — Referensi airport/station
- `meet_greet_charges` — Extra charge per lokasi per operator
- `flash_sales` + `flash_sale_fleet_types` — Time-limited discount
- `dead_leg_discounts` — Diskon return trip kosong
- `free_pickup_postcodes` — Free pickup area coverage

### 3.5 Availability Tables
- `vehicle_availability` — Jumlah kendaraan per hari per fleet type
- `notice_periods` — Minimum jam notice per fleet type
- `postcode_lead_times` — Extra notice per postcode area
- `trip_ranges` — Pickup/dropoff radius dari base
- `operating_hours` — Jam operasional per fleet type
- `availability_pauses` — Pause sementara (immediate/scheduled)

### 3.6 Booking & Quote Tables
- `quote_searches` — Search input dari passenger
- `quotes` — Results dengan price_source tracking (pmp/lp/pap)
- `bookings` — Full lifecycle, meet & greet, payment type

### 3.7 Financial Tables
- `payments` — Stripe payment tracking
- `statements` — Weekly financial statements
- `statement_items` — Per-booking breakdown

### 3.8 Quality & Support Tables
- `reviews` — 5 sub-ratings (timing, fare, driver, vehicle, route)
- `trip_issues` — Issue tracking per booking
- `operator_weekly_stats` — Weekly performance aggregation
- `disputes` + `dispute_messages` — Dispute management dengan thread

### 3.9 System Tables
- `notifications` — Laravel notifications
- `notification_preferences` — Per-user per-channel preferences

---

## 4. Development Plan — 6 Bulan (26 Minggu)

Timeline disusun berdasarkan kompleksitas, dimulai dari modul paling rumit.
Setiap tier memiliki modul-modul yang bisa dikerjakan paralel setelah dependensi terpenuhi.

---

### TIER 0: Foundation (Minggu 1-2) — *2 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 0.1 | Laravel 11 project setup, config, .env | 1 hari | DONE |
| 0.2 | Database schema & migrations (37 tabel) | 2 hari | DONE |
| 0.3 | Models & Eloquent relationships | 2 hari | |
| 0.4 | Seeders: fleet_types, postcode_areas, meet_greet_locations | 1 hari | |
| 0.5 | Auth scaffolding (register, login, verify email, reset password) | 2 hari | |
| 0.6 | Role-based middleware (passenger, operator, driver, admin) | 1 hari | |
| 0.7 | Base layout: Bootstrap 5 + Alpine.js + Vite setup | 1 hari | |
| 0.8 | Operator & Admin sidebar layout (Minicabit-style dark sidebar) | 1 hari | |

---

### TIER 1: Pricing Engine — *Modul Paling Rumit* (Minggu 3-7) — *5 minggu*

Ini adalah jantung platform. Tanpa pricing engine, tidak ada quotes, tidak ada booking.
Harus benar dari awal karena semua modul lain bergantung pada ini.

#### 1A. Per Mile Prices / PMP (Minggu 3-4) — *2 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 1A.1 | Operator UI: PMP rate entry per fleet type (matrix form) | 2 hari | |
| 1A.2 | Mileage range brackets CRUD (0-5mi, 5-10mi, dst) | 1 hari | |
| 1A.3 | Minimum fare per fleet type | 0.5 hari | |
| 1A.4 | Uplift pricing grid UI (distance band x fleet type, % input) | 2 hari | |
| 1A.5 | PMP calculation engine (Service class) | 2 hari | |
| 1A.6 | Mileage pricing calculator (preview tool, seperti Minicabit) | 1 hari | |
| 1A.7 | Unit tests untuk PMP calculation | 1 hari | |

#### 1B. Location Prices / LP (Minggu 4-5) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 1B.1 | Operator UI: LP entry form (start postcode + radius, finish + radius, price) | 1.5 hari | |
| 1B.2 | "Also create reverse direction" checkbox logic | 0.5 hari | |
| 1B.3 | LP list view with pagination, edit, delete | 1 hari | |
| 1B.4 | LP calculation engine (postcode matching with radius) | 2 hari | |
| 1B.5 | Auto-generate prices for other fleet types based on uplift % | 1 hari | |
| 1B.6 | Unit tests untuk LP matching | 1 hari | |

#### 1C. Postcode Area Prices / PAP (Minggu 5-6) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 1C.1 | UK postcode areas seeder (124 areas dengan lat/lng) | 0.5 hari | |
| 1C.2 | Operator UI: PAP grid view (postcode pairs x fleet types) | 2 hari | |
| 1C.3 | Bulk edit / import PAP prices | 1 hari | |
| 1C.4 | PAP calculation engine (exact postcode area matching) | 1 hari | |
| 1C.5 | Unit tests untuk PAP matching | 0.5 hari | |

#### 1D. Quote Engine — Orchestrator (Minggu 6-7) — *2 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 1D.1 | QuoteService: orchestrate PAP > LP > PMP priority | 2 hari | |
| 1D.2 | Meet & Greet charge lookup & addition | 1 hari | |
| 1D.3 | Flash Sale discount application | 1 hari | |
| 1D.4 | Dead Leg Discount matching & application | 1 hari | |
| 1D.5 | Night/weekend surcharge calculation | 0.5 hari | |
| 1D.6 | Commission calculation (operator rate) | 0.5 hari | |
| 1D.7 | Availability check (vehicle count, notice period, trip range, operating hours, pause) | 2 hari | |
| 1D.8 | Top Routes & Price Checker tool (Minicabit-style) | 1 hari | |
| 1D.9 | Integration tests: end-to-end quote generation | 1 hari | |

---

### TIER 2: Operator Dashboard (Minggu 5-11) — *6 minggu*

*Mulai paralel dengan Tier 1D setelah pricing engine dasar selesai.*
Dashboard operator adalah area terbesar — 20+ halaman berdasarkan screenshot Minicabit.

#### 2A. My Account (Minggu 5-6) — *2 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 2A.1 | Operator registration & onboarding flow | 2 hari | |
| 2A.2 | Company details tab (account ID, operator name, legal name) | 1 hari | |
| 2A.3 | Contact details tab (email, address, postcode, phone, website) | 1 hari | |
| 2A.4 | Authorised contact tab (primary + secondary CRUD) | 1 hari | |
| 2A.5 | Licence & Fleet tab (licence upload, insurance, fleet size, dispatch system) | 2 hari | |
| 2A.6 | Payment type tab (prepaid/cash toggle) | 0.5 hari | |
| 2A.7 | Password tab (change password) | 0.5 hari | |
| 2A.8 | Operator tier display (Basic → Airport Approved → TOP TIER progress) | 1 hari | |

#### 2B. Fleet & Drivers (Minggu 6-7) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 2B.1 | Add Fleet Type UI (operator selects which fleet types they offer) | 1 hari | |
| 2B.2 | Drivers list page (table with licence, mobile, vehicle, DBS) | 1 hari | |
| 2B.3 | Add/Edit/Delete driver | 1.5 hari | |
| 2B.4 | Driver DBS status tracking | 0.5 hari | |
| 2B.5 | Vehicle management (add/edit vehicles per fleet type) | 1.5 hari | |

#### 2C. Pricing Pages (Minggu 7-9) — *2 minggu*

*UI pages untuk semua pricing yang engine-nya sudah dibuat di Tier 1.*

| # | Task | Est. | Status |
|---|------|------|--------|
| 2C.1 | Per Mile Prices page (full Minicabit-style matrix) | 2 hari | |
| 2C.2 | Location Prices page (add form + list) | 1.5 hari | |
| 2C.3 | Postcode Area Prices page (grid view) | 2 hari | |
| 2C.4 | Meet & Greet Charges page (location list with charge input) | 1 hari | |
| 2C.5 | More Pricing Options page (free pickup postcodes) | 1 hari | |
| 2C.6 | Flash Sales page (create/manage/view expired) | 1.5 hari | |
| 2C.7 | Dead Leg Discounts page (list with status) | 1 hari | |

#### 2D. Availability Pages (Minggu 9-10) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 2D.1 | Number of Vehicles page (day x fleet type grid) | 1.5 hari | |
| 2D.2 | Notice Periods page (hours per fleet type + postcode lead times) | 1 hari | |
| 2D.3 | Trip Range page (pickup/dropoff radius settings) | 0.5 hari | |
| 2D.4 | Operating Hours page (24h toggle / time range) | 0.5 hari | |
| 2D.5 | Pause Availability page (immediate + scheduled, fleet type checkboxes) | 1.5 hari | |

#### 2E. Dashboard & Views (Minggu 10-11) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 2E.1 | My Dashboard (tier progress, fleet stats, upcoming pickups countdown) | 2 hari | |
| 2E.2 | Booking Log (list with filters, status, detail expand) | 2 hari | |
| 2E.3 | Booking actions (accept/reject, assign driver, update status) | 1.5 hari | |
| 2E.4 | Trip Issues & Ratings — Trip Issues tab (weekly table) | 1 hari | |
| 2E.5 | Trip Issues & Ratings — Latest Ratings tab (sub-rating bars) | 1 hari | |
| 2E.6 | Trip Issues & Ratings — Ratings Trends tab (chart) | 0.5 hari | |
| 2E.7 | Statements page (date range filter, weekly breakdown, print invoice) | 1.5 hari | |

---

### TIER 3: Passenger Booking Flow (Minggu 8-13) — *5 minggu*

*Mulai paralel setelah quote engine (Tier 1D) selesai.*

#### 3A. Search & Quotes (Minggu 8-10) — *2.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 3A.1 | Homepage / landing page design | 1.5 hari | |
| 3A.2 | Search form (pickup, destination, date/time, passengers) | 1 hari | |
| 3A.3 | Google Maps Autocomplete integration (Places API) | 1 hari | |
| 3A.4 | Distance & duration calculation (Distance Matrix API) | 1 hari | |
| 3A.5 | Quote request → QuoteService → results | 1.5 hari | |
| 3A.6 | Comparison page (sort by price/rating, filter by fleet type) | 2 hari | |
| 3A.7 | Quote card design (operator name, rating, vehicle, price, amenities) | 1 hari | |
| 3A.8 | Return journey option | 0.5 hari | |
| 3A.9 | Guest search (tanpa login) → convert ke user saat booking | 1 hari | |

#### 3B. Booking & Payment (Minggu 10-12) — *2 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 3B.1 | Booking detail form (passenger info, requirements, flight/train number) | 1.5 hari | |
| 3B.2 | Booking confirmation page (summary, T&C checkbox) | 1 hari | |
| 3B.3 | Stripe Checkout integration (card, Apple Pay, Google Pay) | 2 hari | |
| 3B.4 | Stripe webhook handling (payment success/failure) | 1.5 hari | |
| 3B.5 | Booking reference generation (TX-YYYYMMDD-XXXX) | 0.5 hari | |
| 3B.6 | Booking confirmation email (passenger + operator) | 1 hari | |
| 3B.7 | Cash booking flow (no Stripe, direct to operator) | 1 hari | |

#### 3C. Passenger Portal (Minggu 12-13) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 3C.1 | My Bookings list (upcoming, past, cancelled) | 1.5 hari | |
| 3C.2 | Booking detail page (status timeline, driver info) | 1 hari | |
| 3C.3 | Cancel booking (with cancellation policy check) | 1 hari | |
| 3C.4 | Review & Rating form (overall + 5 sub-ratings + comment) | 1 hari | |
| 3C.5 | Download invoice PDF | 1 hari | |
| 3C.6 | Profile page (edit name, email, phone, password) | 0.5 hari | |
| 3C.7 | GDPR: Request data deletion | 0.5 hari | |

---

### TIER 4: Admin Panel (Minggu 12-16) — *4 minggu*

*Mulai paralel dengan akhir Tier 3.*

#### 4A. Operator Management (Minggu 12-13) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 4A.1 | Admin layout & sidebar | 1 hari | |
| 4A.2 | Operator list (filter by status, tier, city) | 1 hari | |
| 4A.3 | Operator detail view (semua info, documents, activity) | 1.5 hari | |
| 4A.4 | Approve/Reject operator (with reason) | 1 hari | |
| 4A.5 | Suspend/Reactivate operator | 0.5 hari | |
| 4A.6 | Operator tier management (upgrade/downgrade) | 0.5 hari | |
| 4A.7 | Commission rate per operator | 0.5 hari | |

#### 4B. Booking & Financial Monitoring (Minggu 13-14) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 4B.1 | All Bookings list (global view, filters, search) | 1.5 hari | |
| 4B.2 | Booking detail admin view (override status, add notes) | 1 hari | |
| 4B.3 | Revenue dashboard (total revenue, commission earned, charts) | 2 hari | |
| 4B.4 | Statement generation (weekly batch job) | 1 hari | |
| 4B.5 | Statement approval & payout trigger | 1 hari | |

#### 4C. Disputes & Quality (Minggu 14-15) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 4C.1 | Dispute list (filter by status, type) | 1 hari | |
| 4C.2 | Dispute detail (message thread, passenger/operator sides) | 1.5 hari | |
| 4C.3 | Resolve dispute (refund/credit/no action/warning/suspend) | 1 hari | |
| 4C.4 | Trip Issues overview (cross-operator view) | 1 hari | |
| 4C.5 | Operator performance leaderboard | 0.5 hari | |
| 4C.6 | Fine management (apply/waive fines) | 0.5 hari | |

#### 4D. System Settings (Minggu 15-16) — *1 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 4D.1 | Global commission settings (default rate, tiers) | 0.5 hari | |
| 4D.2 | Fleet types management (add/edit/disable) | 0.5 hari | |
| 4D.3 | Meet & Greet locations management | 0.5 hari | |
| 4D.4 | Postcode areas management | 0.5 hari | |
| 4D.5 | User management (list, view, deactivate) | 1 hari | |
| 4D.6 | System logs & audit trail | 1 hari | |
| 4D.7 | Email templates management | 0.5 hari | |

---

### TIER 5: Notifications & Real-time (Minggu 15-18) — *3 minggu*

#### 5A. Email & SMS Notifications (Minggu 15-16) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 5A.1 | Notification system setup (Laravel Notifications) | 0.5 hari | |
| 5A.2 | Booking confirmation email (passenger) | 0.5 hari | |
| 5A.3 | New job notification email (operator) | 0.5 hari | |
| 5A.4 | Booking status change SMS (passenger) | 1 hari | |
| 5A.5 | Trip completed email + SMS (passenger + operator) | 0.5 hari | |
| 5A.6 | Payment confirmed email | 0.5 hari | |
| 5A.7 | New review notification email (operator) | 0.5 hari | |
| 5A.8 | Payout/statement processed email (operator) | 0.5 hari | |
| 5A.9 | Notification preferences UI (per user, per channel) | 1 hari | |
| 5A.10 | SMS provider integration (Vonage/Twilio) | 1 hari | |

#### 5B. WebSocket Real-time (Minggu 16-18) — *2 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 5B.1 | Laravel Reverb setup & configuration | 1 hari | |
| 5B.2 | Laravel Echo + Pusher JS client frontend setup | 1 hari | |
| 5B.3 | Event: NewBookingReceived (broadcast ke operator) | 1 hari | |
| 5B.4 | Event: BookingStatusUpdated (broadcast ke passenger) | 1 hari | |
| 5B.5 | Event: DriverLocationUpdated (real-time tracking) | 1 hari | |
| 5B.6 | Private channels per user role | 1 hari | |
| 5B.7 | Toast/bell notification UI component | 1 hari | |
| 5B.8 | Notification center dropdown (unread count, mark as read) | 1 hari | |
| 5B.9 | Dashboard real-time updates (new booking counter, status changes) | 1 hari | |
| 5B.10 | Fallback: polling every 30s jika WebSocket terputus | 0.5 hari | |

---

### TIER 6: Payment & Financial (Minggu 17-20) — *3 minggu*

#### 6A. Stripe Integration (Minggu 17-18) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 6A.1 | Stripe Checkout Session creation | 1 hari | |
| 6A.2 | Stripe webhook endpoint (payment_intent events) | 1.5 hari | |
| 6A.3 | Payment status tracking & sync | 1 hari | |
| 6A.4 | Refund API integration (full/partial) | 1 hari | |
| 6A.5 | Cancellation policy engine (time-based refund rules) | 1 hari | |

#### 6B. Stripe Connect & Payouts (Minggu 18-19) — *1.5 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 6B.1 | Stripe Connect Express onboarding (operator KYC) | 2 hari | |
| 6B.2 | Connected account status tracking | 0.5 hari | |
| 6B.3 | Statement generation job (weekly cron) | 1 hari | |
| 6B.4 | Stripe Transfer to connected account | 1 hari | |
| 6B.5 | Payout reconciliation & status tracking | 1 hari | |

#### 6C. Invoicing (Minggu 19-20) — *1 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 6C.1 | Passenger invoice PDF generation (DomPDF/Snappy) | 1.5 hari | |
| 6C.2 | Operator self-billing invoice PDF | 1.5 hari | |
| 6C.3 | Email invoice on payment/statement completion | 0.5 hari | |
| 6C.4 | VAT calculation (if applicable) | 0.5 hari | |

---

### TIER 7: Testing, Security & QA (Minggu 20-23) — *3 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 7.1 | Feature tests: Auth flow (register, login, roles) | 1 hari | |
| 7.2 | Feature tests: Pricing engine (PMP, LP, PAP priority) | 2 hari | |
| 7.3 | Feature tests: Quote generation end-to-end | 1 hari | |
| 7.4 | Feature tests: Booking lifecycle (create → complete) | 1.5 hari | |
| 7.5 | Feature tests: Payment flow (Stripe mock) | 1.5 hari | |
| 7.6 | Feature tests: Operator CRUD (all pages) | 1 hari | |
| 7.7 | Feature tests: Admin panel operations | 1 hari | |
| 7.8 | Security audit: OWASP top 10 check | 2 hari | |
| 7.9 | Security: Rate limiting, CSRF, XSS prevention | 1 hari | |
| 7.10 | Security: Input validation & sanitization review | 1 hari | |
| 7.11 | Performance: N+1 query audit | 1 hari | |
| 7.12 | Performance: Redis caching strategy | 1 hari | |
| 7.13 | Performance: Load testing (Pest stress test) | 1 hari | |
| 7.14 | Browser testing: Chrome, Firefox, Safari, Edge | 1 hari | |
| 7.15 | Mobile responsive testing | 1 hari | |
| 7.16 | Bug fixes from testing | 3 hari | |

---

### TIER 8: Launch Preparation (Minggu 23-26) — *3 minggu*

| # | Task | Est. | Status |
|---|------|------|--------|
| 8.1 | GDPR compliance checklist | 2 hari | |
| 8.2 | Privacy Policy page | 1 hari | |
| 8.3 | Terms of Service page | 1 hari | |
| 8.4 | Cookie consent banner | 0.5 hari | |
| 8.5 | Data retention policy implementation | 1 hari | |
| 8.6 | Production server setup (Nginx + PHP-FPM + MySQL + Redis) | 2 hari | |
| 8.7 | SSL certificate (Let's Encrypt) | 0.5 hari | |
| 8.8 | CI/CD pipeline (GitHub Actions) | 1.5 hari | |
| 8.9 | Staging environment | 1 hari | |
| 8.10 | DNS & domain setup | 0.5 hari | |
| 8.11 | Monitoring & logging (Laravel Telescope / Sentry) | 1 hari | |
| 8.12 | Backup strategy (DB + storage) | 0.5 hari | |
| 8.13 | SEO basics (meta tags, sitemap, robots.txt) | 0.5 hari | |
| 8.14 | Seed production data (fleet types, postcode areas, M&G locations) | 0.5 hari | |
| 8.15 | Operator onboarding test (20+ operator dummy) | 1 hari | |
| 8.16 | End-to-end UAT (user acceptance testing) | 3 hari | |
| 8.17 | Soft launch & monitoring | 2 hari | |
| 8.18 | Go-live | 1 hari | |

---

## 5. Timeline Visual

```
BULAN 1   |████████████████████████████████████|
           Tier 0: Foundation (W1-2)
           Tier 1A-1C: Pricing Engine (W3-6)

BULAN 2   |████████████████████████████████████|
           Tier 1D: Quote Engine (W6-7)
           Tier 2A-2B: Operator Account & Fleet (W5-7)
           Tier 2C: Operator Pricing Pages (W7-9)

BULAN 3   |████████████████████████████████████|
           Tier 2D-2E: Operator Availability & Dashboard (W9-11)
           Tier 3A: Passenger Search & Quotes (W8-10)

BULAN 4   |████████████████████████████████████|
           Tier 3B-3C: Booking, Payment, Passenger Portal (W10-13)
           Tier 4A-4B: Admin Operator & Booking Mgmt (W12-14)

BULAN 5   |████████████████████████████████████|
           Tier 4C-4D: Admin Disputes & Settings (W14-16)
           Tier 5: Notifications & Real-time (W15-18)
           Tier 6A-6B: Stripe & Payouts (W17-19)

BULAN 6   |████████████████████████████████████|
           Tier 6C: Invoicing (W19-20)
           Tier 7: Testing & QA (W20-23)
           Tier 8: Launch Prep (W23-26)
```

---

## 6. Estimasi Waktu per Tier

| Tier | Modul | Durasi | Kompleksitas | Status |
|------|-------|--------|-------------|--------|
| **0** | Foundation (setup, auth, layout) | 2 minggu | Low | ON PROGRESS |
| **1** | Pricing Engine (PMP, LP, PAP, quotes) | 5 minggu | **CRITICAL** | |
| **2** | Operator Dashboard (20+ halaman) | 6 minggu | **HIGH** | |
| **3** | Passenger Booking Flow | 5 minggu | HIGH | |
| **4** | Admin Panel | 4 minggu | MEDIUM | |
| **5** | Notifications & Real-time | 3 minggu | MEDIUM | |
| **6** | Payment & Financial | 3 minggu | HIGH | |
| **7** | Testing & QA | 3 minggu | MEDIUM | |
| **8** | Launch Preparation | 3 minggu | LOW | |
| | **TOTAL** | **26 minggu (6 bulan)** | | |

> **Catatan**: Tier 1-3 saling overlap. Operator pricing pages (Tier 2C) bergantung pada
> pricing engine (Tier 1). Passenger search (Tier 3A) bergantung pada quote engine (Tier 1D).
> Kerja paralel memungkinkan timeline 26 minggu tercapai.

---

## 7. Sistem Notifikasi

| Event | Channel | Penerima |
|-------|---------|----------|
| Booking baru masuk | WebSocket + Email | Operator |
| Operator accept/decline | WebSocket + SMS | Passenger |
| Driver en route | WebSocket + SMS | Passenger |
| Driver arrived | WebSocket + SMS | Passenger |
| Perjalanan selesai | Email + SMS | Passenger + Operator |
| Payment confirmed | Email | Passenger + Operator |
| New review received | Email | Operator |
| Statement/payout processed | Email | Operator |

---

## 8. Payment & Revenue Model

| Model | Keterangan | Prioritas |
|-------|-----------|-----------|
| Commission % | Platform ambil 10-15% dari setiap booking | Utama |
| Performance Fines | Denda untuk no-show, late, rejected | Tier 1 |
| Featured Placement | Operator bayar untuk posisi atas | Phase 2 |
| Insurance Add-on | Upsell asuransi perjalanan | Phase 2 |

---

## 9. Compliance & Legal (UK)

### GDPR
- Privacy Policy — jelas dan accessible
- Cookie Consent — wajib sebelum set non-essential cookies
- Data Subject Rights — user bisa request delete akun & data
- Data Breach Protocol — notifikasi ICO dalam 72 jam
- Data Retention Policy — berapa lama data disimpan

### Transport & Business
- Platform = aggregator/marketplace, bukan operator transport
- Setiap operator wajib punya Private Hire Operator Licence
- Verifikasi lisensi sebelum approval
- Terms of Service membatasi liability platform

### PCI-DSS
- Stripe hosted fields / Stripe Checkout — jangan store card data
- HTTPS wajib di semua halaman

---

## 10. Risiko & Mitigasi

| Risiko | Level | Mitigasi |
|--------|-------|---------|
| Operator network kosong saat launch | TINGGI | Onboard min. 20 operator sebelum launch |
| GDPR violation | TINGGI | Gunakan template GDPR-ready, konsultasi solicitor UK |
| Stripe account suspended | MEDIUM | Patuhi Stripe ToS, verifikasi KYC operator |
| Pricing engine bugs | TINGGI | Extensive unit tests, price checker tool untuk operator |
| Scalability saat traffic tinggi | MEDIUM | Redis caching, queue jobs, CDN |
| Operator tidak adopsi platform | MEDIUM | UX semudah mungkin, onboarding support |

---

## 11. Estimasi Biaya Operasional Bulanan

| Service | Estimasi/bulan | Keterangan |
|---------|----------------|------------|
| Server (VPS/Cloud) | £30 - £80 | DigitalOcean / Hetzner |
| Google Maps Platform | £50 - £200 | Tergantung volume |
| Vonage / Twilio SMS | £20 - £100 | Per SMS |
| Mailgun / Email | £10 - £30 | Transactional email |
| Stripe Fees | 1.5% + £0.20/tx | Standard UK rate |
| SSL Certificate | £0 | Let's Encrypt |
| **Total** | **£110 - £410+** | Belum termasuk Stripe per-transaksi |

---

*Dokumen ini di-maintain sepanjang development. Update status task setiap kali modul selesai.*

*--- END OF DOCUMENT ---*
