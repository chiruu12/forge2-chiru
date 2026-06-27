---
name: git-flow
description: Git discipline for the agent loop — branch per issue, small incremental commits, open a PR, never merge. Keeps the timeline genuine and reviewable.
---

# Git Flow (agent loop)

## Per issue
1. Branch: `feat/<issue-id>-<slug>` off `main`.
2. Commit incrementally — small, meaningful messages; show build phases (no single giant commit).
3. Run tests green locally.
4. Open a PR with `gh pr create`; summarize what + why in the body.
5. Post the pr-report to `#agent-log`.

## Rules
- Commit author = the agent (OpenClaw). The HUMAN merges from `#human-review`. NEVER merge yourself.
- Commit messages describe WHAT changed. No secrets in commits.
- One issue = one branch = one PR.
