# New Chat / Agent Handoff

Use this when work moves to another ChatGPT conversation or another coding agent.

## Minimal instruction

Upload/open the current repository and say:

> Continue the Grab One project. Read `AGENTS.md` first, then the documents in the reading order it specifies. Inspect the current Git status and recent commits before proposing or changing code. Treat the repository documents as the durable project context; if I give a newer explicit decision, update the docs in the same task.

## What the new agent should do first

1. Read `AGENTS.md`.
2. Read the referenced `docs/` files.
3. Run `git status`.
4. Inspect recent commits.
5. Inspect only the code needed for the current task.
6. Do not re-design the project from memory or assumptions.

## Durable decisions already documented

- Laravel + Filament remain backend/admin.
- Next.js is the planned public frontend.
- No online payment checkout at this stage.
- Booking is request + confirmation; payment is direct/in person.
- Email automation such as Brevo is deferred.
- Guides are a small evergreen content set, not a high-volume blog.
- Guide content should initially be source-controlled in `frontend/src/data/guides.ts` once Next.js exists.
- SEO/GEO are core requirements.
- Avoid low-value page proliferation.
- Laravel 13 upgrade decision is deferred.

## Important limitation

A new chat may remember high-level project decisions, but it should not assume it has the current repository bytes. The current repository (or relevant files) must be available to that chat/agent so it can inspect the actual latest code.
