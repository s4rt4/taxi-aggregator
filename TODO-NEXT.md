# Next Session - Client Requirements

From client (handwritten note + Telegram) - to be implemented next session.

## Priority Features

### 1. Commission Adjustment (Small)
Current: tier-based 15%/12%/10%
Client wants: **20% commission**
- Decide: 20% flat or adjust tier (basic=20, airport=18, top_tier=15)

### 2. Homepage Login Buttons (Small)
Add 4 distinct login buttons on homepage:
- **Guest Log in** (default - search/book without account)
- **Account Log in** (personal passenger)
- **Operators Log in** (taxi operators)
- **Corporate Log in** (business accounts)

### 3. Corporate Account Module (LARGE)
Business/agency accounts with hierarchy:

**Company Level:**
- Corporate profile (company name, billing contact, VAT)
- Super User (company admin) - manages everything
- Monthly/weekly auto-invoicing to company
- Company-wide budget controls
- Centralized reporting dashboard

**User Level (under company):**
- Individual users (company employees)
- Book under company account (no personal payment)
- Per-user budget limits
- Booking approval workflow (optional)

**Invoicing:**
- Auto-generate invoice weekly OR monthly
- Email PDF invoice to company billing contact
- Payment terms (30 days net)
- Bank transfer details on invoice

### 4. Admin Job Allocation (Medium)
Admin portal feature:
- View all pending bookings
- Manually assign/allocate job to specific licensed operator
- Override auto-matching
- Operator notified of allocated job

### 5. Native Mobile App (Future - Separate Project)
Client mentions "possibly thru app" - native mobile app.
- Technology: React Native or Flutter
- Features: all web features + push notifications
- Separate project, needs mobile dev team
- For now: mobile responsive web works

## Database Changes Needed

New tables:
- `corporates` - company profile
- `corporate_users` - link users to corporate with role (super_user, user) + budget
- `corporate_invoices` - invoice records per billing cycle
- `job_allocations` - admin-assigned jobs to operators

## Architecture Notes

- Add new role `corporate` to users.role enum (alongside passenger/operator/driver/admin)
- Add `corporate_id` to users table (nullable FK)
- Corporate bookings use different payment flow (invoice-based, not Stripe checkout)
- Separate corporate dashboard/sidebar (similar to operator but different features)

## Estimated Effort

| Feature | Effort | Sessions |
|---------|--------|----------|
| Commission 20% + 4 login buttons | Small | 0.5 |
| Corporate Account Module | Large | 2-3 |
| Admin Job Allocation | Medium | 1 |
| Native App | Very Large | Separate project |

## Source References

- Handwritten note photo (RushXO business model diagram)
- Telegram messages dated 17:47-17:50:
  - "i want to incorporate guest log in and account log in"
  - "agency and operator log in corporate log in (super user) with automatic invoicing and budget controls for company as a whole and individual"
  - "corporate log in (individual user)"
  - "all of above also should be accessible thru mobile version and possibly thru app"
  - "also we should be able to allocate the jobs to other licensed operators thru our admin portal"
