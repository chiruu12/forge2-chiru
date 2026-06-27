---
name: sprint-issue
description: Hermes's format for handing ONE scoped, testable issue to OpenClaw in #agent-coder. Use for every task handoff.
---

# Sprint Issue Handoff (Hermes → OpenClaw)

Hand ONE issue at a time to `#agent-coder`. Scope it to a single PR.

## Format
**Issue `<id>` — `<title>`**
- **Goal:** one sentence, user-visible outcome
- **Context:** files / models / routes involved (point to ARCHITECTURE.md)
- **Do:** numbered, concrete steps
- **Acceptance criteria:** checkable bullets (endpoint returns X; test Y passes; cross-tenant 404)
- **Tests required:** the feature tests that must pass
- **Out of scope:** what NOT to touch
- **Branch:** `feat/<id>-<slug>`

## Standing Rules (include at the bottom of every work order)
1. `git checkout main && git pull origin main` — start from latest
2. Create branch `feat/<issue-id>-<slug>`
3. Implement per spec; run `php artisan test` — everything green
4. Open PR against main — **do NOT merge**
5. Post build report (test output summary, files changed) to `#agent-log`
6. Reply in `#agent-coder` mentioning the human when done
7. **Dependency guard:** if prior issues in the sprint are NOT yet merged to main, stop and report back — do not branch off an unmerged main.

## Status checking
See `references/status-check.md` for the GitHub inspection technique (PRs, branches, commit history).

## Post-Handoff Lifecycle (what the brain does after sending the work order)
After delivering the work order, you (the brain) own the follow-through:
1. **Nudge** — if no response within a reasonable window, send a brief `nudge — status?` to `#agent-coder`. Don't re-post the full work order.
2. **Status check** — when asked or proactively, inspect the repo directly (see `references/status-check.md` for technique). Report honestly — if nothing exists, say "not started."
3. **Review** — once a PR is open, review it against the acceptance criteria before the human merges.
4. **Advance** — only assign the next issue after the current PR is merged by the human.

## Rules
- One issue = one PR. If it needs more than ~5 files, split it.
- Always include the tenant-safety acceptance criterion for anything touching data.
- Don't assign the next issue until the current PR is merged by the human.
