---
name: decompose-to-issues
description: Turn a feature or sprint goal into an ordered backlog of small, testable issues with dependencies. Hermes uses this to write sprints/sprint-0N.md.
---

# Decompose Feature → Issues

## Steps
1. Restate the feature's user-visible outcome.
2. List the slices: data (model/migration) → API (endpoint + policy + test) → UI (React) → polish.
3. Each issue: independently testable, ≤ ~5 files, one PR. Name `<id> <title>`.
4. Order by dependency; mark what blocks what.
5. Give each an acceptance criterion + required test.
6. Write the result to `sprints/sprint-0N.md` (goal, issues, outcome-to-fill).

## Heuristics
- If an issue can't be tested on its own, split or merge it.
- Front-load the tenancy + auth foundation — everything depends on it.
- Keep a sprint to ~4–6 issues so the loop visibly completes ≥ 2 sprints.

## Sprint doc maintenance
Sprint docs are living documents — update them throughout the sprint, not just at decomposition:
- As each issue's PR merges, check off its line in the Issues list.
- When the sprint closes, fill in the Outcome section (shipped, slipped, PR numbers).
- The next sprint's doc stays as a placeholder until the current sprint's final issue merges.
- Commit sprint doc updates directly to main (they're docs, not code — no PR needed).
