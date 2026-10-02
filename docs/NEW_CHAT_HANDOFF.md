# Grab One – New Chat Handoff

## Preferred opening

> Continue Grab One. Read AGENTS.md and NEW_CHAT_HANDOFF.md first. Tell me the current status before changing anything.

## Current stage

The Laravel booking/fleet operational foundation is complete, fully UAT-verified, merged into `main`, pushed to `origin/main`, and remote-verified.

Current local branch:

`main`

Current application baseline before this documentation commit:

`10997e7 chore: resolve dependency advisories`

The previous feature branch is retained as a checkpoint:

`feature/booking-fleet-foundation`

It is retained temporarily as a local checkpoint after remote `main` verification.

`origin/main` is synchronized with local `main`.

## Runtime baseline

- PHP 8.5.9
- Laravel 13.30.0
- Livewire 4.4.1
- Node 24.15.0
- npm 11.12.1
- Vite 7.3.6
- MySQL / MariaDB locally
- Business timezone: `America/Belize`
- Booking buffer: 60 minutes

## Completed Laravel operational foundation

- Belize timezone foundation
- source-controlled cart/status/pickup configuration
- Customers
- Booking Items and quantities
- physical fleet
- physical Cart assignments
- Fleet Admin
- availability engine
- 60-minute booking buffer
- Admin confirmation workflow
- exact physical Cart assignment
- Booking search/filter/sort
- Fleet search/filter/sort
- Contact search/filter/sort
- demo operational data seeder
- operational Dashboard
- customer identity matching
- normalized email and phone matching
- public Contact linking
- safe historical Contact retro-linking
- Identity Review
- Customer operational history
- completed/cancelled historical preservation
- confirmed → pending Assignment cleanup
- historical Cart type protection
- public Booking/privacy automated regression coverage
- dependency security remediation

## Booking/fleet invariants

- timezone: `America/Belize`
- Cart types: `4_seater`, `6_seater`
- Mix is UI-only
- pending does not block physical Carts
- confirmed requires exact physical assignments
- completed/cancelled do not block future availability
- completed/cancelled may retain Assignment history
- reopening confirmed → pending removes physical Assignments
- physical Cart type becomes immutable after Assignment history exists
- Cart status is never `reserved`
- current rental is derived from confirmed + Belize time
- availability is calculated
- public users never see physical Cart codes or internal availability

## Customer identity invariants

- email normalized using trim + lowercase
- phone normalized conservatively
- Belize 7-digit phone maps to `+501...`
- unique normalized email may identify Customer
- if email has no match, unique normalized phone may identify Customer
- never match by name
- conflicting email/phone Customers are never auto-merged
- ambiguous identities remain unlinked
- conflicts surface as `Identity Review`
- Booking may create/reuse Customer when identity is clear
- Contact never creates a Customer
- later safe Booking may retro-link historical Contact

## Final UAT

Final report:

`.local/reports/UAT_FINAL_BROWSER_REGRESSION_2026-10-02.md`

Result:

`PASS`

Merge readiness:

`READY FOR MERGE`

Validated:

- real public browser Booking flow
- public privacy before and after confirmation
- Admin Booking review
- exact Cart confirmation
- final agreed price
- 60-minute availability buffer
- confirmed → pending Assignment cleanup
- reconfirmation
- historical Cart type protection
- Dashboard reconciliation
- Customer history
- Contact identity behavior
- completed/cancelled history
- Belize-local datetime preservation
- database integrity
- dependency security

Admin browser authentication was unavailable to the automated Chrome profile, so Admin workflow verification used the authenticated production Livewire/application harness against the real operational database. Public flow was browser-verified against Herd/nginx.

## Final automated baseline

- 118 tests passed
- 515 assertions
- Blade compile passed
- Vite production build passed
- Composer audit: 0 advisories
- npm audit: 0 vulnerabilities
- `git diff --check`: passed
- tracked working tree clean after local merge

## Final database integrity baseline

Latest Final UAT checks:

- invalid Booking windows: 0
- pending Bookings with Assignments: 0
- confirmed Assignment quantity mismatches: 0
- Cart / Booking Item type mismatches: 0
- confirmed physical Cart overlap pairs including buffer: 0
- stored `mix` Booking Item types: 0
- stored `reserved` Cart statuses: 0
- stored `active` Booking statuses: 0
- duplicate normalized Customer emails: 0
- duplicate normalized Customer phones: 0

## Known historical note

The old Phase 2 UAT Booking disappeared before Phase 3 and its ID was later reused by Phase 3 data.

This remains an historical continuity anomaly.

No new unexpected persistence loss or database drift was observed during Phase 4 or Final Regression.

Do not restore the old Phase 2 database snapshot because IDs were subsequently reused.

## Git state

The booking/fleet feature was fast-forward merged into `main` and pushed to `origin/main`.

Pre-documentation merge HEAD:

`10997e7fa2b515606eb52da01eb2d831d2261e48`

Important final commits include:

- `3b7dd81 fix: clear assignments when reopening bookings`
- `88eda3e fix: protect assigned cart types`
- `72f685b test: cover public booking privacy flow`
- `10997e7 chore: resolve dependency advisories`

The feature branch remains present locally as a checkpoint.

Local `main` and `origin/main` were verified synchronized after the merge and documentation checkpoint.

## Next action

The Laravel operational foundation is closed and `main` is remote-verified.

Next development action:

1. create a new dedicated branch from verified `main`
2. begin the planned Next.js public frontend
3. keep Laravel as backend/business/API/Admin
4. preserve all booking, fleet, identity, privacy, and Belize-time invariants

Do not begin Next.js work on the completed booking/fleet branch.

## Next.js boundary

Planned public frontend:

Next.js App Router.

Laravel remains:

- backend
- business/domain logic
- persistence
- API
- Admin / Livewire

Admin remains Laravel/Livewire.

No Filament.

No DB-driven CMS/settings architecture.

Public content, navigation, FAQ, SEO, guides, labels, and site definitions remain source-controlled.
