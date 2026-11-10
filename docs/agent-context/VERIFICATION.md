# Verification and environment map

Source inspected: 2026-11-10 at `5ef4bfa33270d936d9a6550428cb8452ac047174`.

## Tests

- `phpunit.xml` defines Unit, Feature and Audit suites. Default test configuration sets `APP_ENV=testing`, SQLite `database/testing.sqlite`, and array/sync drivers.
- `tests/TestCase.php` checks the testing environment and guards against using the main MySQL database.
- `tests/Support/IsolatedAuditTestCase.php` uses `.env.audit-tests`, an in-memory SQLite database and faked HTTP, queue, mail and notification facades. It avoids the cached application config. Read the test's setup before assuming all side effects are isolated.
- `tests/Feature/Audit/PaymentCallbackTest.php` is an example of focused schema setup and fake gateway behavior. Do not copy financial test assumptions into unrelated tests without inspection.

Use the smallest relevant command after checking its environment, for example:

```text
php vendor/phpunit/phpunit/phpunit tests/Feature/Audit/PaymentCallbackTest.php --no-progress
php -l path/to/changed.php
```

Syntax checks do not prove a bug is fixed. Broaden testing when affected shared behavior warrants it. Do not run migrations, seeders or queue workers against an unknown application database as routine verification.

## Frontend

`npm run build` runs `scripts/build-tailwind-content.mjs` then Vite. The script scans source templates into `storage/framework/tailwind-content.html`; generated assets go through Vite. It is useful for asset changes, not proof that a PHP request transforms notes correctly.

## Deployment evidence

The inspected `.github/workflows/audit-checks.yml` runs audits, PHPUnit and a frontend build for PRs/manual runs; it does not define production deployment. This does not rule out deployment outside GitHub Actions. The user has reported pushing changes and pulling them on the server.

Keep separate: local working tree, Git commit, server checkout, active server process and the exact request that reproduced a problem. Do not tell the user a change was not deployed merely because the symptom persists. Inspect available evidence or state the missing evidence precisely.

Existing `docs/notifications/` deployment reports concern a specific notification rollout and contain historical environment snapshots. Load them for that subsystem; do not treat their old flags or revision numbers as current production facts.

## Local tools observed

Python available on this workstation at `C:/laragon/bin/python/python-3.13/python.exe`.
The WindowsApps `python.exe` alias failed during skill installation. Prefer an actual interpreter path if the alias fails again. This machine-specific note is not a server path.
