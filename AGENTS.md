# CleanStation working context

For work in this repository, start with `docs/agent-context/INDEX.md` and
`git status --short`. Load only the topic reference relevant to the task.
Use the `cleanstation-context` skill when available; its portable source is
`docs/agent-context/cleanstation-context/SKILL.md`.

## Keep context small and current

- The context index is a navigation aid, not proof that current code or production behaves as described.
- Compare its recorded source revision with HEAD using `git diff --name-only <revision> HEAD`; also inspect uncommitted changes in the affected area. If the revision is unavailable, inspect the relevant source directly.
- Follow the affected route, request validation, controller, service, model and output view/resource. Read related observers when persistence or side effects matter.
- Search within the relevant package first. Expand when evidence crosses a package boundary. Avoid broad reads of vendor, node_modules, generated API documentation, storage or historical audit reports.
- After changing a documented flow, update only its relevant context reference. Record the source revision/date and distinguish source inspection, local verification and production verification. Keep temporary investigation notes out of the permanent map.

## Project-specific working rules

- Application code lives largely in `packages/core/*/src`, not only in `app/`.
- Establish whether a reported action comes from the mobile API, B2B web portal, admin dashboard or scheduled job before selecting a controller. An admin screenshot identifies the display surface, not necessarily the creation route.
- Preserve mobile API field names and types unless the task explicitly changes the contract. Do not infer which field Flutter consumes without app source or request/response evidence.
- For Arabic files, read/write UTF-8. Confirm apparent mojibake in the actual file before changing text; Windows terminal decoding can misrepresent valid Arabic.
- Skills provide task guidance. Adapt their examples to this repository's Laravel 11, PHPUnit, Blade and package structure. Do not introduce Livewire, Horizon, Pest, React, new dependencies or blanket coverage targets solely because a skill template uses them.
- Validate the affected behavior with focused checks. Before database tests, inspect the test base and isolation configuration. Keep real payments, notifications and live customer records out of reproductions.
- Report code changes, local verification and deployment status separately. The user has deployed changes by pushing Git and pulling on the server; respect that evidence. A continuing failure does not establish a cache or deployment problem.

## Skill routing

| Task | Skill |
| --- | --- |
| Project navigation and maintaining context | `cleanstation-context` |
| Laravel/Eloquent/API/queue implementation | `laravel-specialist` |
| Unexpected behavior and repeated failed fixes | `debugging-wizard` |
| Checking completion claims against evidence | `verification-before-completion` |
| UI, Arabic/RTL layout and interaction work | Existing `ui-ux-pro-max`; use other design skills as relevant |
| Finding a capability not already covered | `find-skills` |

See `docs/agent-context/SKILLS.md` for installation scope and provenance.
