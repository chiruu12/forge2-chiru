---
name: domain-model
description: Define PulseDesk's domain entities and relationships before assigning model work, so the data model stays coherent. Source of truth is ARCHITECTURE.md.
---

# Domain Model (PulseDesk)

## Entities & relationships
- Organization 1—* User ; Organization 1—* Ticket
- User (role: admin|agent|customer) ; User 1—* Ticket (as requester) ; User 1—* Ticket (as assignee, nullable)
- Ticket 1—* Comment ; Comment.is_internal (bool)
- Ticket *—* Tag (via ticket_tag) ; Organization 1—* SlaPolicy ; Ticket 1—* ActivityLog

## Invariants
- Every tenant-owned row has `organization_id`, set from the authenticated user — never the client.
- status: open | pending | resolved | closed ; priority: low | medium | high | urgent
- A Ticket's requester, assignee, comments, and tags MUST belong to the same org.

## When extending
- New tenant-owned entity? add `organization_id` + the `BelongsToOrganization` trait (see tenant-scope).
