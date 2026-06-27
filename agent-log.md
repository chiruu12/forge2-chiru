# Agent log — the human → Hermes → OpenClaw loop

Real exchanges, in order. Two **separate Slack apps** give the agents distinct identities so they can
trigger each other genuinely: **Hermes** = `Forge-Unplug` (PO/planner, `z-ai/glm-5.1`), **OpenClaw** =
`pulsedesk-coder` (coder, `moonshotai/kimi-k2.7-code`). All model calls go through EastRouter.

The loop per issue: human posts the issue in `#agent_coder` → Hermes writes the full work order and
@mentions the coder → the coder auto-dispatches, builds on a branch, runs Pest, opens a PR (never merges),
posts a report to `#agent_log`, and pings Hermes back → the human reviews + merges.

---

## Sprint 1 — tenanted ticketing core

### I1 — health route  → PR #1 (merged)
Coder scaffolded `GET /api/health` + a Pest feature test. Human merged.

### I2 — auth + Organization/User (Sanctum)  → PR #2 (merged)
Coder: installed Sanctum, `Organization` model+migration+factory, `organization_id`+`role` on users,
`AuthController` (register/login/me), `AuthTest` (6 passing). Human merged.

### I3 — tenant isolation  → PR #3 + #4 (merged)
**Human (#agent_coder):** `@unplug-manager next issue: I3 — tenant isolation … cross-org access → 404 …
branch feat/s1-tenant-scope.`

**Hermes → coder (work order, excerpt):**
> `<@pulsedesk-coder>` New issue — *Issue I3 — Tenant Isolation*. Branch `feat/s1-tenant-scope`.
> Goal: any tenant-owned row is auto-scoped to the auth user's `organization_id`; a user in Org B
> requesting Org A's data gets a **404, never 403**.
> Do: `BelongsToOrganization` trait (global scope + auto-set org_id on create, never from request) ·
> `SetTenantContext` middleware after Sanctum · `Ticket` model+migration+factory · route-model-bound
> read · Pest `TenantIsolationTest`. Standing rules: pull main first; open a PR but **do NOT merge**;
> report to `#agent_log`; reply here starting with `<@Forge-Unplug>` when done.
> _(Hermes loaded its skills — sprint-issue, tenant-scope, domain-model, eloquent-model — to write this.)_

**Coder → report:** built the trait + middleware + `Ticket` model/migration/factory + `TenantIsolationTest`
(cross-org → 404, same-org → 200), opened the PR, cc'd `@Forge-Unplug`. **Human merged.**

### I4 — Ticket CRUD + authorization  → PR #5 (merged)
**Hermes → coder:** full work order for `TicketController` CRUD + `TicketPolicy` (same-org + role),
branch `feat/s1-ticket-crud`. Hermes then chased the coder autonomously: *"friendly nudge → second ping
⚠️ → 🚨 third and final ping, sprint blocked."*

**Coder → report (#agent_coder, verbatim excerpt):**
> *What I Did* — Branched `feat/s1-ticket-crud`; extended `TicketController` with full CRUD
> (index/store/show/update/destroy); 5 routes under `auth:sanctum` + `SetTenantContext`; `TicketCrudTest`
> covering org-scoped list, auto org+requester on create, show/update/delete, 404 cross-tenant, role create.
> **All 18 tests pass, 50 assertions.** Committed, pushed, opened **PR #5**. *cc: @Forge-Unplug — done! ✅*

**Human merged PR #5.** This issue ran with **zero human steps in the chain** — Hermes's mention
dispatched the coder directly (bot→bot), it built, reported, and pinged back.

### I5 — seeder + acceptance suite  → PR #6 (open)
**Hermes → coder:** work order for a `DatabaseSeeder` (2 orgs, admin/agents/customers, ~12 tickets) +
a Pest acceptance suite. Coder wrote `DatabaseSeeder` + `OrganizationSeeder` + `UserSeeder` +
`TicketSeeder`, opened **PR #6** ("I5: Seeder + Pest Acceptance Suite — closes Sprint 1").

---

## Note on the genuine bot→bot dispatch
Getting Hermes's mention to actually *run* the coder's agent (not just be received) required, on the
coder's Slack app: `allowBots: true` **and** `allowFrom` listing the manager's bot user id
(`U0BBYPJ3YP7`). With only `allowBots`, the mention is received but no agent run starts. See
`agents/openclaw/openclaw.json`.
