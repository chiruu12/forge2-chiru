---
name: sanctum-auth
description: Install and wire Laravel Sanctum token auth for PulseDesk — install:api, register/login/logout/me, roles, and role middleware. The auth foundation.
---

# Sanctum Auth (recipe)

## Setup
- `php artisan install:api` (adds Sanctum + api routes + personal_access_tokens migration)
- User model: `use HasApiTokens;`, add `role` (admin|agent|customer) + `organization_id`.

## Endpoints
- POST `/api/register` → create org + admin (or join), return token
- POST `/api/login` → validate, return `$user->createToken()->plainTextToken`
- POST `/api/logout` → revoke current token
- GET `/api/me` → current user + org + role

## Roles
- Middleware `EnsureRole:agent,admin` (or policies) for agent/admin-only routes.
- customer: create tickets + comment on own; agent/admin: manage all within the org.

## Test
- register → login → token → `/me`; unauthenticated → 401; wrong role → 403.
