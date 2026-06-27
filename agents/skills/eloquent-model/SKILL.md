---
name: eloquent-model
description: Recipe for a new Eloquent model in PulseDesk — model + migration + factory + seeder + tenant trait. Use when introducing a domain entity.
---

# Eloquent Model (recipe)

## Files
1. `php artisan make:model <Name> -mf` (model + migration + factory)
2. Migration: columns incl. `organization_id` (FK, indexed) for tenant models; enums as string columns.
3. Model: `use BelongsToOrganization;` (tenant models), `$fillable`, `$casts`, relationships.
4. Factory: realistic data; relate to org/user via factories.
5. Seeder: wire into DatabaseSeeder per spec (1 org, admin, 2 agents, 2 customers, ~12 tickets).

## Conventions
- Define relationships both ways (Ticket hasMany Comment; Comment belongsTo Ticket).
- Cast booleans / datetimes; keep `status` / `priority` as string columns with app-level validation.
- Index `organization_id` and common filters (status, priority, assignee_id).
