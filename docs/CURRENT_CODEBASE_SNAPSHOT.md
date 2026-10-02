# Grab One – Current Codebase Snapshot

## Git

Current branch:

`main`

Booking/fleet foundation was fast-forward merged locally from:

`feature/booking-fleet-foundation`

Verified application HEAD before this documentation commit:

`10997e7fa2b515606eb52da01eb2d831d2261e48`

Latest application commit:

`chore: resolve dependency advisories`

Working tree was clean after merge verification.

Remote `origin/main` is synchronized with the merged local `main` baseline.

The old feature branch remains available temporarily as a local checkpoint after remote verification.

## Runtime

- PHP 8.5.9
- Laravel 13.30.0
- Composer 2.10.2
- Livewire 4.4.1
- Node 24.15.0
- npm 11.12.1
- Vite 7.3.6
- MySQL / MariaDB locally
- timezone `America/Belize`

## Local environment

Repo:

`C:\project\grab-one`

URL:

`http://grab-one.test`

Herd/nginx serves the local application.

## Architecture

### Backend

Laravel 13 / PHP 8.5.

Laravel owns:

- booking logic
- customer identity
- fleet availability
- exact physical Cart assignment
- operational persistence
- Admin
- future API endpoints

### Admin

Laravel + Livewire 4.

Operational areas:

- Dashboard
- Bookings
- Fleet
- Contacts
- Customers

Internal `Backoffice` namespace remains intentional.

Filament is not used.

### Public frontend

Current:

Blade / Tailwind.

Planned next stage:

Next.js App Router.

Laravel remains backend/Admin/API after the public frontend migration.

## Architecture boundary

### Code/config owns

- Cart type definitions and visible labels
- pickup locations
- status definitions
- booking buffer
- timezone
- public content
- navigation
- FAQ
- SEO metadata
- structured data
- guides/posts/updates
- page definitions
- public images/content references
- automated pricing rules/defaults if introduced

### Database/Admin owns

- Bookings
- Booking Items and quantities
- Customers
- Contacts/leads
- final agreed Booking price
- physical Carts
- Booking-to-Cart Assignments
- customer operational history
- workflow/read states
- calculated operational state

Do not turn Admin into a CMS/settings manager.

## Booking and fleet rules

- Business timezone: `America/Belize`
- Booking buffer: 60 minutes
- Cart types: `4_seater`, `6_seater`
- Mix is UI-only
- default quantity: 1
- availability is calculated, never stored
- Cart operational statuses: `active`, `maintenance`, `inactive`
- never store `reserved`
- Booking statuses: `pending`, `confirmed`, `completed`, `cancelled`
- never store Booking status `active`
- pending does not reserve/block Carts
- confirmed requires exact physical Cart assignments
- completed/cancelled do not block future availability
- completed/cancelled may preserve Assignment history
- current rental is derived from confirmed + Belize time
- exact pickup and return wall times are preserved
- confirmed → pending clears all physical Assignments
- Cart type cannot change after the physical Cart has Assignment history
- public users never see physical Cart codes or internal availability

## Customer identity

- normalize email using trim + lowercase
- normalize phone conservatively
- Belize 7-digit phone maps to `+501...`
- unique normalized email may identify a Customer
- if email has no match, unique normalized phone may identify a Customer
- never match by name
- email/phone conflict never auto-merges
- ambiguous identifiers remain unlinked
- conflicts surface as `Identity Review`
- Booking may create/reuse Customer when clear
- Contact never creates a Customer
- safe historical Contacts may retro-link later

## Main services

### FleetAvailabilityService

- active Cart filtering
- Cart type filtering
- confirmed conflict checks
- 60-minute buffer
- current Booking exclusion during editing
- exact buffer boundary support

### BookingCartAssignmentService

- exact Cart count validation
- correct type validation
- active Cart validation
- duplicate prevention
- availability validation
- Booking confirmation
- transaction safety
- row locking

### CustomerIdentityService

- email normalization
- phone normalization
- Booking Customer resolution/creation
- Contact linking
- conflict/ambiguity protection
- historical Contact retro-linking

## Admin routes

- `/admin`
- `/admin/login`
- `/admin/bookings`
- `/admin/bookings/create`
- `/admin/bookings/{booking}/edit`
- `/admin/carts`
- `/admin/carts/create`
- `/admin/carts/{cart}/edit`
- `/admin/contacts`
- `/admin/contacts/create`
- `/admin/contacts/{contact}/edit`
- `/admin/customers`
- `/admin/customers/{customer}`
- `POST /admin/logout`

## Public operational routes

- `POST /book-now`
- `POST /contact-us`

## Final verification baseline

Final Browser Regression:

`PASS`

Merge readiness:

`READY FOR MERGE`

Automated verification:

- 118 tests passed
- 515 assertions
- Blade compile passed
- Vite 7.3.6 production build passed
- Composer audit clean
- npm audit clean
- `git diff --check` passed

## Final UAT operational state

At the end of Final Browser Regression:

- Bookings: 19
- Customers: 13
- Contacts: 8
- Carts: 8
- Assignments: 11

Integrity:

- invalid Booking windows: 0
- pending Bookings with Assignments: 0
- confirmed Assignment quantity mismatches: 0
- Cart/Booking Item type mismatches: 0
- confirmed physical Cart overlap pairs including 60-minute buffer: 0
- stored `mix`: 0
- stored `reserved`: 0
- stored Booking `active`: 0
- duplicate normalized Customer emails: 0
- duplicate normalized Customer phones: 0

Do not assume these operational counts remain static later; re-check the database before future UAT or operational changes.

## Final UAT report

`.local/reports/UAT_FINAL_BROWSER_REGRESSION_2026-10-02.md`

The report is intentionally ignored/local and is not part of tracked source.

## Known historical UAT note

The old Phase 2 UAT record disappeared before Phase 3.

The cause was not proven.

Phase 3 and Phase 4 evidence remained intact through Final Regression, and no new unexpected persistence loss was observed.

Do not restore the old Phase 2 snapshot because IDs were subsequently reused.

## Dependency state

Resolved security baseline:

- Laravel 13.30.0
- Livewire 4.4.1
- Vite 7.3.6
- Composer audit: 0 advisories
- npm audit: 0 vulnerabilities

## Next planned stage

The Laravel operational foundation is merged, pushed, and remote-verified on `main`.

Create a new dedicated branch from verified `main` for the Next.js public frontend.

Do not expand Next.js work on the completed booking/fleet branch.

Admin remains Laravel/Livewire.

No online payment checkout is planned.

Booking remains:

request → Admin reviews availability/final price → Customer pays directly/in person.
