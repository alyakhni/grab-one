# Grab One — SEO + GEO + Content Strategy

## Principle

The goal is not "more pages". The goal is more useful, discoverable answers and stronger commercial landing pages.

A small site with 15 strong pages is preferable to 100 weak pages.

## SEO foundations

Each indexable page should have:

- distinct search intent
- descriptive URL
- unique `<title>`
- unique meta description
- one useful H1
- semantic headings
- enough original visible content to satisfy the intent
- relevant internal links
- canonical URL
- indexable HTML without requiring client-side execution for main content
- optimized images and descriptive alt text where appropriate
- strong mobile/Core Web Vitals behavior

Global requirements:

- XML sitemap
- robots configuration
- Open Graph/social metadata
- consistent business name/contact/location facts
- sensible redirects if URLs change
- Search Console after launch
- analytics/conversion tracking after launch

## GEO / AI-search approach

Treat GEO as clarity + authority + retrievability, not as an AI-content hack.

Priorities:

1. Write direct answers to real traveler questions.
2. State business facts explicitly and consistently.
3. Use strong local context: San Pedro, Ambergris Caye, Belize, Secret Beach, airport/water taxi/hotel delivery where accurate.
4. Keep key facts in normal visible HTML text, not hidden in scripts only.
5. Use structured data only when it accurately matches visible content.
6. Keep pages internally connected by meaning.
7. Prefer original photos, rental-process details, real policies and firsthand local advice.
8. Update factual Guides when conditions change.

## Structured data plan

Use schema selectively and accurately.

Likely global/site patterns:

- Organization / appropriate LocalBusiness subtype
- WebSite
- BreadcrumbList

Page-specific patterns may include:

- Article for substantial Guide content where appropriate
- product/service-related schema only when semantics and visible page content genuinely match

Do not add schema solely because a validator accepts it.
Do not duplicate claims that are not visible to visitors.

## Guide philosophy

Guides are evergreen resources, not a publishing treadmill.

Initial set:

1. Renting a Golf Cart in San Pedro, Belize
2. Secret Beach by Golf Cart
3. Getting Around San Pedro

A Guide should contain practical information someone might genuinely search for before/during their trip.

Good reasons for a future Guide:

- many customers ask the same question
- Search Console shows a meaningful query cluster
- the topic naturally supports the rental decision
- Grab One can add real local/business value

Bad reasons:

- an AI tool can generate it quickly
- a keyword tool lists a near-duplicate phrase
- a competitor has a page so we copy the topic without unique value

## Avoid page cannibalization

Do not create near-duplicate pages such as:

- best golf cart rental san pedro
- cheap golf cart rental san pedro
- affordable golf cart rental san pedro
- golf cart hire san pedro

Instead create one excellent commercial page and let its copy naturally cover relevant language.

## Page hierarchy and internal linking

Suggested high-level structure:

```text
Home
├── Golf Carts
│   ├── 4-Seater
│   └── 6-Seater
├── Rates
├── Book
├── Delivery & Pickup
├── Guides
│   ├── Rental Guide
│   ├── Secret Beach
│   └── Getting Around San Pedro
├── FAQ
├── About
└── Contact
```

Examples of meaningful internal linking:

- Secret Beach Guide -> suitable cart pages + booking
- Getting Around Guide -> Golf Carts + Rates
- 4-Seater -> Rates + FAQ + Book
- Delivery & Pickup -> Book + FAQ

## Guide content quality checklist

Before publishing a Guide:

- Does it answer a real question quickly near the top?
- Does it contain information specific to San Pedro/Ambergris Caye?
- Is there anything here that is better than generic travel copy?
- Are factual claims verified?
- Is the page useful even if the visitor does not book immediately?
- Is the commercial CTA natural rather than intrusive?
- Does it link to the relevant rental page(s)?
- Does it have unique metadata?
- Does it have an appropriate image?
- Does it deserve its own URL?

## Performance is part of SEO

This project already contains large source images. The Next.js migration must include a deliberate image strategy:

- responsive sizes
- WebP/AVIF where practical
- lazy-load non-critical images
- optimize the LCP/hero image
- avoid sending multi-megabyte source assets to mobile clients

A visually rich site should still be fast.
