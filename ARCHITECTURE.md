# Architecture -- PulseDesk

> Multi-tenant support-desk. Laravel 11 REST API + MySQL 8, React 19 SPA. Tenant isolation is the
> security-gated criterion (12 pts) -- it is enforced server-side, never trusted from the client.

## Multi-tenancy approach
- Every tenant-owned row carries `organization_id`.
- Tenant is derived **only** from the authenticated user: `auth()->user()->organization_id`. The client
  never sends an org id; any org id in a request body/param is ignored.
- A `BelongsToOrganization` trait adds a **global Eloquent scope** that auto-filters every query by the
  current user's org, and auto-fills `organization_id` on create. So a forgotten `where` cannot leak data.
- `SetTenantContext` middleware binds the org for the request lifecycle (after Sanctum auth).
- Policies (`TicketPolicy`, `CommentPolicy`) enforce **role + same-org** on every action.
- Adversarial test: a User in Org B requesting Org A's ticket id gets `404` (not `403` -- don't reveal existence).

## Data model
| Model | Key fields | Notes |
| --- | --- | --- |
| Organization | id, name, slug | the tenant |
| User | id, organization_id, name, email, password, role | role: `admin` \| `agent` \| `customer` |
| Ticket | id, organization_id, subject, description, status, priority, requester_id, assignee_id, timestamps | status: `open\|pending\|resolved\|closed`; priority: `low\|medium\|high\|urgent` |
| Comment | id, ticket_id, author_id, body, is_internal | `is_internal=true` => agents/admins only |
| Tag + ticket_tag | id, organization_id, name / (ticket_id, tag_id) | filterable labels |
| SlaPolicy | id, organization_id, priority, response_minutes, resolution_minutes | Should-tier; drives breach timers |
| ActivityLog | id, ticket_id, actor_id, action, meta(json), created_at | Should-tier; audit trail |

## API routes (routes/api.php)
| Method | Path | Auth | Notes |
| --- | --- | --- | --- |
| POST | /api/register | - | creates org + admin (or joins via invite) |
| POST | /api/login | - | returns Sanctum token |
| POST | /api/logout | token | |
| GET  | /api/me | token | current user + org + role |
| GET  | /api/tickets | agent/admin | tenant-scoped; filter by status/priority/assignee; `?q=` search subject/body |
| POST | /api/tickets | any | customer creates; requester = self |
| GET  | /api/tickets/{id} | tenant | 404 if cross-org |
| PUT  | /api/tickets/{id} | agent/admin | status/priority/assignee |
| POST | /api/tickets/{id}/claim | agent | assign to self (queue workflow) |
| POST | /api/tickets/{id}/comments | tenant | `is_internal` flag => public reply vs internal note |
| GET  | /api/tickets/{id}/activity | agent/admin | audit trail (Should) |
| GET  | /api/dashboard/metrics | agent/admin | open counts, avg first-response, SLA breach rate (Should) |

## Key decisions (log them live as you build)
- (Hermes/you append decisions here per sprint)
