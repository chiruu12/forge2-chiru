---
name: architecture-guardrails
description: The non-negotiable architecture rules Hermes enforces when assigning and reviewing work — Laravel layering, tenancy, auth, and the human-merge gate.
---

# Architecture Guardrails (enforce on every issue)

## Layering (Laravel)
routes/api.php → Controller (`Api/`) → Policy (authorization) → Model. Validation lives in FormRequests.
Shape responses with JsonResource. No business logic in routes.

## Tenancy (security-gated)
- `organization_id` on every tenant model; a global scope auto-filters by the auth user's org.
- Tenant derived from the auth session ONLY. Reject/ignore any client-supplied org id.
- Cross-org access returns **404** (not 403 — don't leak existence).

## Auth
- Sanctum tokens. Roles admin|agent|customer enforced via policies + middleware.

## Process
- Agents are commit authors; the HUMAN merges. No bot auto-merge.
- One issue = one PR = incremental commits (no single giant commit).
- All model calls go through EastRouter.
