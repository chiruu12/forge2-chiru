# Checking Coder Agent Status via GitHub

When the user asks "check status?" or you need to verify the hands agent's progress,
inspect the repo directly. Never fabricate — report what actually exists.

## Steps

1. **Find the repo** — check memory for the repo name. If unknown, search GitHub:
   ```
   gh search repos <name-fragment> --limit 10
   gh repo list --limit 50 | grep -i <fragment>
   ```

2. **Check PRs (the primary progress signal):**
   ```
   gh pr list -R <org/repo> --state all --limit 20
   gh pr list -R <org/repo> --state merged --limit 10 --json number,title,mergedAt
   ```
   - MERGED PRs = completed issues
   - OPEN PRs = work in progress, ready for review
   - No PR for an issue = not started or not far enough along

3. **Check branches** (detects work that hasn't become a PR yet):
   ```
   gh api repos/<org/repo>/branches -q '.[].name'
   ```

4. **Check commit history** (optional, for detail on what landed):
   ```
   gh api repos/<org/repo>/git/trees/main?recursive=1 -q '.tree[].path'
   git log --oneline --all | head -20
   ```

## Reporting format
- Status table: Issue → Branch → PR → Status (merged / open / not started)
- Note what's blocked and what's unblocked
- If the branch doesn't exist and no reply from coder → state "not started" honestly

## Pitfalls
- Don't assume the repo name — PulseDesk-style projects may have a different repo name (e.g. `forge2-chiru`).
- `gh repo list` only shows your own repos/orgs — the project repo might be under a different org.
- If you can't find the repo, ask the user rather than guessing.
