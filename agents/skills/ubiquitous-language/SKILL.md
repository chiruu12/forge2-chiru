---
name: ubiquitous-language
description: The shared PulseDesk vocabulary used identically across backend, React, and Slack, so agents and humans mean the same thing.
---

# Ubiquitous Language (PulseDesk)

Use these exact terms everywhere — code, API, UI, Slack.

- **Organization** (the tenant) — never "company/account" in code.
- **User** with **role**: admin | agent | customer.
- **Ticket** — subject, description, status, priority, requester, assignee.
- **status**: open | pending | resolved | closed. **priority**: low | medium | high | urgent.
- **Comment** — a reply on a ticket. **internal note** = Comment with `is_internal=true` (agents only);
  **public reply** = `is_internal=false` (customer-visible).
- **Requester** = the user who opened the ticket. **Assignee** = the agent handling it.
- **Claim** = an agent assigning an unassigned ticket to themselves.
- **SLA breach** = response/resolution past the SlaPolicy window.

## Rule
If a new term appears, add it here first so backend / frontend / Slack stay consistent.
