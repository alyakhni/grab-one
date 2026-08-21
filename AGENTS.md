# Grab One — Agent Operating Guide

This file is the mandatory starting point for any AI agent or developer working on this repository.
Read it before changing code.

## 1. Product

Grab One is a golf-cart rental business website for San Pedro / Ambergris Caye, Belize.
The product goal is not merely a landing page. It should become a fast, visually distinctive, trustworthy rental website that can rank well in Google and be easy for AI/search systems to understand and cite.

## 2. Current codebase

- Runtime: PHP 8.5
- Backend: Laravel 13
- Admin: Filament 5.x
- Reactive stack: Livewire 4.x
- Current public frontend: Laravel Blade + Tailwind
- Current public site: mostly a one-page landing page
- Current booking behavior: creates a booking request; there is no online checkout
- Current main entities: Booking, Contact
- Current fleet data is partly hard-coded/config-based
- Git baseline was created from the uploaded project before architecture documentation was added

The approved runtime/framework baseline is PHP 8.5 + Laravel 13 + Filament 5.x + Livewire 4.x.
Do not upgrade major framework/runtime versions unless a later task explicitly approves it.

## 3. Approved target architecture

The intended direction is:

```text
Public visitor
    |
    v
Next.js public frontend
    |
    | /api/v1
    v
Laravel business backend
    |
    +--> Database
    |
    +--> Filament admin
```

Rules:

1. Laravel remains the business backend and source of truth for transactional data.
2. Filament remains the admin/operations interface.
3. Next.js is planned for the public website.
4. Do not let Next.js query the Laravel database directly.
5. Do not keep duplicate business rules in Laravel and Next.js.
6. The existing Blade frontend should be replaced gradually, not deleted before the Next.js frontend reaches functional parity.

## 4. Booking decision — fixed unless explicitly changed

Grab One does NOT need online payment/checkout at this stage.

Expected flow:

```text
Customer selects cart + dates + delivery/pickup details
-> sends booking request
-> Grab One confirms availability/details
-> customer pays in person/directly according to business process
```

Recommended booking statuses:

- pending
- confirmed
- active
- completed
- cancelled

Do not add Stripe, online checkout, payment capture, shopping cart, or payment gateway logic unless explicitly requested later.

Email automation (possibly Brevo) is a future phase. Do not make it a prerequisite for the current architecture.

## 5. Content strategy — fixed unless explicitly changed

This is NOT intended to become a high-volume blog.

The preferred model is a small number of evergreen Guides with strong search/user value.
Content changes are expected to be infrequent, so a source-controlled data file in the Next.js frontend is preferred initially over a CMS/database.

Planned path when Next.js is created:

```text
frontend/src/data/guides.ts
```

The data file must drive server/static-rendered Guide pages. Do NOT render Guide content only in the browser via client-side JavaScript.

Initial Guides:

1. Renting a Golf Cart in San Pedro, Belize
2. Secret Beach by Golf Cart
3. Getting Around San Pedro

Add new Guides only when there is a clear customer question, commercial intent, or verified search opportunity.

## 6. Page strategy

Initial public architecture should remain intentionally small.

Core pages:

- `/`
- `/golf-carts`
- `/golf-carts/4-seater`
- `/golf-carts/6-seater`
- `/rates`
- `/book`
- `/delivery-pickup`
- `/faq`
- `/about`
- `/contact`
- `/rental-policy`
- `/privacy`

Initial content pages:

- `/guides/renting-a-golf-cart-san-pedro-belize`
- `/guides/secret-beach-by-golf-cart`
- `/guides/getting-around-san-pedro`

Do not create pages merely to target keyword variations.
No page should exist without a clear reason.

## 7. SEO + GEO principles

SEO and AI-search discoverability are first-class product requirements.

Every indexable page must have:

- one clear search/user intent
- unique title and meta description
- one clear H1
- useful visible content, not keyword filler
- sensible internal links
- canonical URL
- crawlable server-rendered/static HTML
- image alt text where appropriate
- good mobile performance

The site should also have:

- sitemap
- robots configuration
- Open Graph metadata
- structured data that accurately represents visible content
- LocalBusiness/appropriate business entity information
- Breadcrumb structured data where useful
- strong real-world business facts and local expertise

GEO does not mean creating hundreds of AI-written pages. Prefer explicit facts, clear answers, local expertise, meaningful headings, updated information, and consistent business/entity data.

## 8. Agent workflow

Never accept broad instructions like "make everything better" as permission to rewrite unrelated systems.

Each implementation task should define:

- scope
- files/domains allowed to change
- things that must not change
- acceptance criteria
- tests/checks

Prefer small commits.

Example:

```text
TASK: Introduce Guide content model in Next.js.

Allowed:
- frontend/src/data/guides.ts
- frontend/src/app/guides/**
- related Guide components/tests

Do not:
- alter booking workflow
- alter Laravel migrations
- upgrade dependencies

Acceptance:
- all Guide slugs build
- each Guide has unique metadata
- output is crawlable without client JS
- sitemap contains published Guides
```

## 9. Current phase

We are currently in architecture/foundation work.

Before major frontend migration:

1. preserve a clean Git baseline
2. document decisions
3. make current Laravel setup reproducible
4. define the business/domain boundary
5. then introduce Next.js deliberately

## 10. Source-of-truth reading order for a new chat/agent

Read in this order:

1. `AGENTS.md`
2. `docs/PROJECT_BLUEPRINT.md`
3. `docs/SEO_GEO_CONTENT_STRATEGY.md`
4. `docs/NEXT_GUIDES_DESIGN.md`
5. `docs/CURRENT_CODEBASE_SNAPSHOT.md`
6. current Git history/status

If a chat instruction conflicts with these documents, the newest explicit user decision wins, and the docs should be updated in the same change so the repository remains the durable source of truth.
