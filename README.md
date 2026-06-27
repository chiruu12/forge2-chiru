# PulseDesk -- Forge 2 / Edition 1  (repo: forge2-chiru)

A multi-tenant support-desk SaaS, BUILT BY ORCHESTRATING Hermes + OpenClaw over Slack.
This is a STARTER SKELETON -- structure only, zero features. Features are built by the agent loop.

## Stack (required)
Laravel 11 . PHP 8.2 . MySQL 8 . Laravel Sanctum . React 19 . Vite . Tailwind

## EastRouter models I used
- Hermes (planning / product owner): `deepseek/deepseek-v4-pro`
- OpenClaw (coding): `z-ai/glm-5.1`
- Fallback (only if EastRouter is down, see note below): Ollama `qwen2.5-coder:7b`

> All model calls go through EastRouter (`https://api.eastrouter.com/v1`). The Ollama fallback is wired
> in the agent configs solely for an EastRouter outage and is documented here per the rules.

## How to run  (EXACT -- a judge will run these from a fresh clone)
### Backend (Laravel + MySQL)
    cd backend
    cp .env.example .env          # set DB_* for your MySQL
    composer install
    php artisan key:generate
    php artisan migrate --seed
    php artisan serve             # http://127.0.0.1:8000
### Frontend (React + Vite)
    cd frontend
    cp .env.example .env          # set VITE_API_URL=http://127.0.0.1:8000
    npm install
    npm run dev                   # http://127.0.0.1:5173

## Demo logins (from the seeder)  -- fill in after the seeder is built
- admin@acme.test / password
- agent@acme.test / password
- customer@acme.test / password

## Live URL
runs locally per the steps above

## Where my evidence lives (everything is in THIS repo -- no Drive, no video)
- agents/        -- real Hermes + OpenClaw configs (secrets redacted)
- agent-log.md   -- the human->Hermes->OpenClaw loop
- sprints/       -- one doc per sprint
- slack-export/  -- Slack export, or per-channel screenshots
- evidence/screenshots/ -- app, agents-running, CI screenshots
