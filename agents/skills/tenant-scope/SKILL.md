---
name: tenant-scope
description: The PulseDesk multi-tenancy pattern — BelongsToOrganization trait, global scope, SetTenantContext middleware, and the cross-tenant 404. Apply to every tenant-owned model. This is the security-gated criterion.
---

# Tenant Scope (multi-tenant isolation)

Get this right on EVERY tenant model — it's the security-gated criterion.

## Trait + global scope
- `app/Models/Concerns/BelongsToOrganization.php`: a trait that
  (a) adds a global scope filtering `where('organization_id', <auth user's org>)`,
  (b) on `creating`, sets `organization_id` from the auth user if unset.
- `use BelongsToOrganization;` on Ticket, Comment, Tag, SlaPolicy, ActivityLog.

## Middleware
- `SetTenantContext` (after Sanctum auth) binds the current org for the request lifecycle.

## Policies
- Each policy checks role AND same-org.

## The 404 rule
- A user requesting another org's resource id MUST get **404** (route-model binding + global scope make
  the record "not found"), never 403 — don't reveal existence.

## Test (always)
- Feature test: a user in Org B requests Org A's ticket → `assertStatus(404)`. (see pest-feature-test)

## Never
- Never read `organization_id` from the request body / query / header. Session only.
