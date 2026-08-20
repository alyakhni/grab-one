# Grab One — Project Blueprint

## Purpose

Build a memorable, fast and trustworthy golf-cart rental website for San Pedro / Ambergris Caye, Belize, with strong Google SEO and strong machine-readable/entity clarity for AI-assisted search (GEO).

The project should remain focused enough to ship. We prefer a small number of excellent pages over a large site full of weak or repetitive content.

## Product model

Grab One is a rental-service website, not an e-commerce checkout product.

### Booking flow

1. Visitor selects cart type.
2. Visitor selects rental dates/times.
3. Visitor provides pickup/delivery information and contact details.
4. System records a booking request.
5. Grab One confirms availability and final details.
6. Payment happens directly/in person according to business operations.

Online payment is intentionally out of scope at this stage.

## Current architecture

```text
Laravel 12
├── Blade public frontend
├── BookingController
├── ContactController
├── Booking model
├── Contact model
└── Filament admin
```

The public frontend is currently mostly one landing page assembled from Blade sections.

## Target architecture

```text
                    Internet
                       |
                       v
              Next.js public site
              /  /rates /book ...
                       |
                   /api/v1
                       |
                       v
              Laravel application
           business rules + API layer
                 /             \
                v               v
             Database       Filament Admin
```

### Responsibility split

#### Next.js

Owns:

- public page rendering
- visual design and responsive UX
- SEO metadata
- sitemap/robots
- Guide pages
- structured content presentation
- booking form UX

Does not own:

- booking business rules
- transactional source of truth
- database writes outside Laravel API

#### Laravel

Owns:

- bookings
- validation/business rules
- transactional persistence
- public API
- operational logic
- future email/event orchestration

#### Filament

Owns:

- operational/admin workflows
- booking management
- contact management
- future fleet/rates/settings management when justified

## Public page map

### Commercial/core

| Route | Primary job |
|---|---|
| `/` | Brand + primary San Pedro golf-cart rental intent |
| `/golf-carts` | Fleet overview |
| `/golf-carts/4-seater` | 4-seat product intent |
| `/golf-carts/6-seater` | 6-seat product intent |
| `/rates` | Clear rates/what is included |
| `/book` | Booking-request conversion |
| `/delivery-pickup` | Airport, water taxi, hotel and delivery/pickup process |
| `/faq` | Pre-booking objections/questions |
| `/about` | Trust/business story |
| `/contact` | Direct contact |
| `/rental-policy` | Rental/cancellation/responsibility terms |
| `/privacy` | Privacy information |

### Initial evergreen Guides

| Route | Purpose |
|---|---|
| `/guides/renting-a-golf-cart-san-pedro-belize` | Complete practical rental guide |
| `/guides/secret-beach-by-golf-cart` | High-relevance local journey/use case |
| `/guides/getting-around-san-pedro` | Transportation decision guide |

This is a starting map, not a requirement to create all pages at once.

## Page-creation rule

A new page must satisfy at least one:

1. clear commercial intent
2. recurring real customer question
3. verified search opportunity with enough unique value to deserve its own page

Keyword variations alone are not a reason.

## Content management decision

Because Guide content will be small and infrequently edited, initial content should live in source control rather than a database/CMS.

When Next.js is introduced, use a typed data file such as:

```text
frontend/src/data/guides.ts
```

This gives us:

- version history
- code review
- almost no CMS complexity
- consistent metadata fields
- easy static page generation
- easy migration to CMS later if content volume actually grows

## Deferred decisions

The following are intentionally deferred:

- Laravel 12 -> Laravel 13 upgrade
- online payments
- Brevo/email automation details
- full real-time vehicle inventory engine
- large CMS/blog
- multilingual expansion

They should not block the current foundation.

## Definition of a successful site

The site should be:

- visually distinctive and strongly tied to Belize/San Pedro
- extremely usable on mobile
- fast
- easy to understand in seconds
- transparent about rental process and rates
- easy for Google to crawl and understand
- rich in real business/local facts
- structured so AI/search systems can extract reliable answers
- easy to maintain with few moving parts
