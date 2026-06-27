---
name: react-feature
description: Build a React 19 + Tailwind feature from a mock, feature-first, wired to the Laravel JSON API. The mock is look-only; adapt structure/state/validation to the stack.
---

# React Feature (mock → working UI)

## Structure (feature-first, mirrors the API)
```
src/features/<name>/
  api.js         — fetch wrappers (Bearer token from the auth store)
  use<Name>.js   — data hook (loading / error / data)
  <Name>.jsx     — page + components, Tailwind styled
```

## From a mock
- Treat the mock as **intent, not pixels**. Map it to React + Tailwind; adapt layout/state to the API shape.
- Status/priority pills use the agreed colors: open=blue, pending=amber, resolved=green, closed=slate;
  urgent=red, high=orange, medium=blue, low=slate.

## Conventions
- All API calls go through `features/<name>/api.js` — never fetch inline in components.
- Handle loading + error + empty states.
- Read `VITE_API_URL` from env; attach the Sanctum token.

## Done = renders against the live API and matches the mock's intent.
