---
name: pr-triage
description: Reviewer agent's triage of an opened PulseDesk PR — assess scope, tenant-safety, correctness, and tests, then post a verdict to Slack and comment on the PR. Routed via EastRouter. Never merges.
---

# PR Triage (reviewer agent)

Triage an opened PR against PulseDesk standards. Routed via EastRouter (`z-ai/glm-5.1`). Produce a
short verdict the human can act on in seconds. RECOMMEND only — never merge.

## What to check (in order)
1. **Scope** — does the PR match its issue? Flag unrelated/oversized changes.
2. **Tenant safety (security-gated)** — every new query/model must be org-scoped, and the tenant must
   come from the auth session, never the request body/param. Flag any path that could touch another
   org's data.
3. **Correctness** — does it do what the issue asked? Obvious bugs or missing cases?
4. **Tests** — feature tests present, incl. a cross-tenant **404** where relevant? Do they actually assert?
5. **Conventions** — Laravel layering (routes→controller→policy→model), naming, no secrets committed.

## Output format (post to #agent-coder + as a PR comment)
**🔎 Triage `<PR #>` — `<APPROVE | CHANGES | BLOCK>`**
- **Risk:** low / med / high
- **Tenant-safety:** ✅ / ⚠️ `<what>`
- **Tests:** ✅ / ⚠️ `<what's missing>`
- **Top issues:** 1–3 concrete, actionable items (file:line where possible)
- **Reason:** one line

## Rules
- Specific and short — no essays.
- BLOCK only on real problems (tenant leak, failing tests, wrong feature). Else CHANGES or APPROVE.
- Recommend; the human decides and merges from #human-review.
