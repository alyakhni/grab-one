# Grab One Golf Cart Rental

Golf-cart rental website for San Pedro / Ambergris Caye, Belize.

## Before changing anything

Every developer or AI agent must read:

1. [`AGENTS.md`](AGENTS.md)
2. [`docs/PROJECT_BLUEPRINT.md`](docs/PROJECT_BLUEPRINT.md)
3. [`docs/SEO_GEO_CONTENT_STRATEGY.md`](docs/SEO_GEO_CONTENT_STRATEGY.md)
4. [`docs/NEXT_GUIDES_DESIGN.md`](docs/NEXT_GUIDES_DESIGN.md)
5. [`docs/CURRENT_CODEBASE_SNAPSHOT.md`](docs/CURRENT_CODEBASE_SNAPSHOT.md)

These files preserve the product and architecture decisions across chats and agents.

## Current stack

- Laravel 12
- Filament 5.x
- Blade/Tailwind public frontend

## Planned direction

- Laravel remains the backend/business layer
- Filament remains admin/operations
- Next.js will become the public frontend
- Booking is request + confirmation; no online payment checkout at this stage
- Evergreen Guide content will initially live in a small typed source file in the Next.js frontend
- SEO and GEO/search-machine clarity are first-class requirements

See the project docs for the complete plan.
