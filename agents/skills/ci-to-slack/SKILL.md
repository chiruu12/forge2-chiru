---
name: ci-to-slack
description: Post GitHub Actions CI results to the #ci-cd Slack channel so the loop's build/test status is visible to the human and Hermes.
---

# CI → Slack (#ci-cd)

## Goal
Every CI run (on PR + push to main) posts pass/fail to `#ci-cd` so status is visible in Slack.

## How
- Add a final step to `.github/workflows/ci.yml` posting to a Slack incoming webhook
  (`secrets.SLACK_WEBHOOK_URL`): repo, PR/branch, job result, run URL.
- Run it with `if: always()` so failures also report.

## Message
`CI <✅ pass | ❌ fail> · <branch/PR> · <commit> · <run url>`

## Note
The webhook URL is a GitHub repo secret. If the org blocks webhooks, fall back to committing CI
screenshots to `evidence/screenshots/` per channel.
