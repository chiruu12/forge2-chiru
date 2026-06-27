# Submission checklist -- Forge 2 / Edition 1 (PulseDesk)

Tick each and point to the in-repo path. Everything is committed in THIS repo.

- [x] Repo is public, named `forge2-chiru` -> github.com/chiruu12/forge2-chiru
- [x] README has run steps; `php artisan migrate --seed` after I5 merges -> `README.md`, `backend/README.md`
- [x] Backend = Laravel 11 + MySQL ; Frontend = React 19 + Vite + Tailwind
- [x] Multi-tenancy: Org A cannot see Org B data (tenant from auth session) -> `backend/app/Models/Concerns/BelongsToOrganization.php`, `backend/app/Http/Middleware/SetTenantContext.php`, `backend/tests/Feature/TenantIsolationTest.php` (cross-org -> 404)
- [x] Hermes config committed -> `agents/hermes/hermes-config.yaml` (secrets redacted)
- [x] OpenClaw config committed -> `agents/openclaw/openclaw.json` (secrets redacted)
- [x] agent-log.md shows the real human->Hermes->OpenClaw loop -> `agent-log.md`
- [x] sprints/ has >= 2 sprint docs -> `sprints/sprint-01.md`, `sprints/sprint-02.md`
- [~] Slack proof -> `evidence/screenshots/` (channel screenshots in place; full Slack export pending)
- [x] App / agents-running / CI screenshots -> `evidence/screenshots/*.png`
- [x] `.github/workflows/ci.yml` present (auto-posts results to #ci_cd) -> CI runs on each PR
- [x] PRs merged by the HUMAN; commit authors are the agents -> PRs #1-#5 merged, #6 (I5) open
- [x] All model calls went through EastRouter (`https://api.eastrouter.com/v1`)
- Models used: **z-ai/glm-5.1** (Hermes -- PO/planner) · **moonshotai/kimi-k2.7-code** (OpenClaw -- coder). Sprints run: Sprint 1 (I1-I5) complete; Sprint 2 (React UI) in progress.

## What the loop actually does (genuine agent-to-agent)
Human posts an issue in `#agent_coder` mentioning Hermes -> **Hermes** (glm-5.1) loads its skills, writes a full work order, and @mentions the coder -> **OpenClaw** (kimi-k2.7-code) auto-dispatches on that mention, builds the feature on a branch, runs Pest, opens a PR (never merges), posts a build report to `#agent_log`, and pings Hermes back -> the **human** reviews and merges. Two separate Slack apps give the agents distinct identities so they can genuinely trigger each other. See `agent-log.md`.
