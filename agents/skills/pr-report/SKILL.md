---
name: pr-report
description: OpenClaw's structured status report for the #agent-log Slack channel after each task or PR. Trigger whenever handing work back to the human/Hermes.
---

# PR / Task Report — post to #agent-log

After finishing (or pausing) a task, post ONE structured report to `#agent-log` so the human and
Hermes can read state at a glance. Always use the three sections below.

## Format
**Task:** `<issue-id>` <one-line title>  ·  **Branch:** `<branch>`  ·  **PR:** `<#num | opening>`

**✅ What I Did**
- concrete changes: files touched, endpoints/migrations/components added
- commands run + result — e.g. `php artisan test` → 12 passed

**⏳ What's Left**
- remaining steps for this issue (or "none — this issue is complete")

**❓ What Needs Your Call**
- decisions / blockers for the human: schema choice, ambiguous spec, failing dependency
- if nothing: "Nothing — ready to merge once CI is green"

## Rules
- One report per hand-back. Keep it skimmable.
- Never claim done if tests/CI aren't green — say so. Honest recovery scores better than pretending.
- Link the PR and the CI run.
- **Do not merge.** The human merges from `#human-review`.
