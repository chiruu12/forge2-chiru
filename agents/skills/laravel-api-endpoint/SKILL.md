---
name: laravel-api-endpoint
description: Recipe for adding a REST endpoint to PulseDesk — route, controller, FormRequest, policy, JsonResource, and feature test. Use for every new API action.
---

# Laravel API Endpoint (recipe)

## Files to touch
1. `routes/api.php` — add the route under `auth:sanctum`
2. `app/Http/Controllers/Api/<X>Controller.php` — thin; delegates
3. `app/Http/Requests/<Action>Request.php` — validation + `authorize()`
4. `app/Policies/<X>Policy.php` — role + same-org checks; register it
5. `app/Http/Resources/<X>Resource.php` — response shape
6. `tests/Feature/<X>Test.php` — happy path + auth + cross-tenant 404

## Conventions
- Return JsonResource / ResourceCollection, not raw models.
- 422 on validation, 403 on role denial, 404 on cross-tenant/missing.
- Tenant scoping is automatic via the model's global scope (see tenant-scope) — don't re-filter by hand,
  but DO assert it in tests.

## Done = `php artisan test` green locally before opening the PR.
