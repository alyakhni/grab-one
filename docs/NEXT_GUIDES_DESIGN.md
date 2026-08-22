# Next.js Guide Content Design

## Decision

Use a typed source-controlled data file for Guides initially.

Preferred location once the Next.js frontend exists:

```text
frontend/src/data/guides.ts
```

Although the original idea was `data.js`, TypeScript is preferable if the frontend itself uses TypeScript because content fields can be validated by the compiler.

## Important distinction

"Dynamic from a data file" must NOT mean client-side-only rendering.

Each Guide should resolve to a normal crawlable URL and be rendered at build/server level.

Conceptual routes:

```text
frontend/src/app/guides/page.tsx
frontend/src/app/guides/[slug]/page.tsx
```

The `[slug]` page reads from `guides.ts` and generates:

- the visible Guide HTML
- per-Guide metadata
- canonical URL
- Open Graph data
- optional Article JSON-LD when appropriate
- breadcrumbs

Guide slugs should also be available to sitemap generation.

## Proposed type

```ts
export type GuideSection = {
  id: string;
  heading: string;
  paragraphs: string[];
};

export type GuideFaq = {
  question: string;
  answer: string;
};

export type Guide = {
  slug: string;
  title: string;
  seoTitle: string;
  seoDescription: string;
  summary: string;
  heroImage: string;
  heroAlt: string;
  updatedAt: string;
  sections: GuideSection[];
  faqs?: GuideFaq[];
  relatedRoutes: string[];
};
```

## Example data shape

```ts
export const guides: Guide[] = [
  {
    slug: 'secret-beach-by-golf-cart',
    title: 'Secret Beach by Golf Cart',
    seoTitle: 'Secret Beach by Golf Cart from San Pedro, Belize | Grab One',
    seoDescription:
      'A practical guide to reaching Secret Beach by golf cart from San Pedro, with route and rental tips for Ambergris Caye.',
    summary:
      'What to know before driving a golf cart from San Pedro to Secret Beach.',
    heroImage: '/images/guides/secret-beach-golf-cart.jpg',
    heroAlt: 'Golf cart trip to Secret Beach on Ambergris Caye, Belize',
    updatedAt: '2026-08-20',
    sections: [
      {
        id: 'before-you-go',
        heading: 'Before You Go',
        paragraphs: [
          'Guide content goes here. Facts should be verified before publication.',
        ],
      },
    ],
    relatedRoutes: ['/golf-carts', '/rates', '/book'],
  },
];
```

This is a schema example, not approved final copy.

## Helper functions

Keep lookup logic outside page components where practical:

```ts
export function getGuideBySlug(slug: string) {
  return guides.find((guide) => guide.slug === slug);
}

export function getGuideSlugs() {
  return guides.map((guide) => guide.slug);
}
```

## Static generation concept

With the App Router, the Guide route should predeclare known slugs and generate metadata from the same data source.

Conceptually:

```ts
export function generateStaticParams() {
  return getGuideSlugs().map((slug) => ({ slug }));
}

export async function generateMetadata({ params }) {
  const guide = getGuideBySlug((await params).slug);

  return {
    title: guide?.seoTitle,
    description: guide?.seoDescription,
    alternates: {
      canonical: `/guides/${guide?.slug}`,
    },
  };
}
```

The exact implementation should follow the Next.js version installed at implementation time.

## Why this model fits Grab One

Advantages:

- very low maintenance
- no CMS/admin complexity
- Git tracks every content change
- content and metadata stay together
- easy to statically render
- fast
- simple to test for duplicate slugs/missing SEO fields
- can migrate to a Laravel/Livewire-managed CMS later without changing public URLs

## When to move Guides to a CMS

Only reconsider the data-file approach when one or more becomes true:

- non-developers need frequent content editing
- content volume grows materially
- publishing workflow/approvals are needed
- scheduled publishing becomes important
- multilingual editorial workflow becomes important

Until then, the file-based model is intentionally simpler.
