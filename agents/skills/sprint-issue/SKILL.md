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

## Rules
- One issue = one PR. If it needs more than ~5 files, split it.
- Always include the tenant-safety acceptance criterion for anything touching data.
- Don't assign the next issue until the current PR is merged by the human.
