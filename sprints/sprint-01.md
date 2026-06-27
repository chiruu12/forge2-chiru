# Sprint 01 — Auth, Tenancy, Ticket CRUD, Tenant Isolation

Goal: Ship the multi-tenant ticketing core — authenticated users belong to organizations, tickets are scoped per-org, and cross-tenant access is impossible (404, never 403).

Models: Hermes=z-ai/glm-5.1 (product owner / planner), OpenClaw=gpt-5.4-nano (coding agent / hands)

## Issues

- [x] **I1** — `GET /api/health` route + Pest feature test → PR #1 (merged)
- [x] **I2** — Auth & tenancy foundation: Sanctum token auth, register flow (new org + admin user), Organization/User models → PR #2 (merged)
- [x] **I3** — Tenant isolation spine: `SetTenantContext` middleware, registered in bootstrap group, all queries auto-scoped to authed user's org → PR #3 + PR #4 (merged)
- [x] **I4** — Ticket CRUD + authorization with tenant scope: Ticket model, controller, role-based policies (customer sees own; agent/admin manage any in org) → PR #5 (merged)
- [ ] **I5** — DatabaseSeeder (2 orgs × 5 users × ~12 tickets) + Pest acceptance suite proving cross-tenant 404, role enforcement, full CRUD lifecycle → **IN PROGRESS** (branch `feat/s1-seeder-acceptance`, not yet started)

## Outcome

- Shipped: I1–I4 (health, auth/tenancy, tenant middleware, ticket CRUD + authz)
- Slipped / moved to next sprint: none
- I5 in flight: seeder + acceptance suite is the capstone that closes Sprint 1
- PRs: #1, #2, #3, #4, #5 (all merged by OpenClaw)
- I5 status: work order delivered, nudge sent, coder has not yet created the branch
