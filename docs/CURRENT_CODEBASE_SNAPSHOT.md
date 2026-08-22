# Current Codebase Snapshot

This document describes the uploaded Grab One code at the point the Git baseline was created.

## Framework/runtime declarations

From `composer.json`:

- PHP: `^8.2`
- Laravel: `^13.0`
- Admin: custom Laravel + Livewire 4.x

Frontend build dependencies are currently Laravel Vite/Tailwind based. There is no Next.js app yet.

## Public routes

Current `routes/web.php` exposes:

- `GET /` -> `welcome` Blade view
- `POST /book-now` -> BookingController
- `POST /contact-us` -> ContactController

## Public page structure

`resources/views/welcome.blade.php` composes the home page from sections:

- hero
- features
- fleet
- gallery
- faq
- contact

This is why the current public experience behaves as a one-page site.

## Current main application models/admin areas

- Booking
- Contact
- User
- Custom Livewire booking management at `/admin/bookings`
- Custom Livewire contact management at `/admin/contacts`

## Fleet

A `config/fleet.php` file exists, but fleet presentation is not yet a mature domain model/database-backed system.

The future architecture should remove duplicate/hard-coded fleet knowledge rather than reproduce it in Next.js.

## Booking positioning

The existing project receives booking requests. The approved product direction remains request/confirmation, not online payment checkout.

## Git baseline

The repository was initialized locally before these architecture documents were added.

Initial baseline commit message:

```text
chore: baseline existing Grab One application
```

`.env` and SQLite database files are excluded by existing ignore rules and were not tracked in the baseline.
