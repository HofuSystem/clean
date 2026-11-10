---
name: cleanstation-context
description: Navigate the CleanStation Laravel repository using its maintained project map, identify the correct mobile API, admin or B2B entry point, and update relevant context after changes. Use when investigating or modifying CleanStation, or resuming work there without rereading the whole repository. Not for unrelated projects.
---

# CleanStation context

Use the checked-out repository's `AGENTS.md` and `docs/agent-context/INDEX.md` as the starting point. The initial workstation location is `D:/programming/projects/Hofu/CleanStation`; prefer the current checkout rather than assuming that path or branch is always active.

1. Read the short index and `git status --short`. Select only the relevant topic reference.
2. Compare the reference's recorded revision with HEAD using `git diff --name-only <revision> HEAD`, and inspect uncommitted changes in the affected area. If Git cannot resolve the revision, check the relevant source instead of relying on the snapshot.
3. Follow the mapped entry point into the current source. For order bugs, distinguish customer API, B2B web, admin and scheduled requests; a screenshot of the admin display does not identify the creation path.
4. Apply the specialist skills relevant to the task. The context map is navigation, not a substitute for validating behavior. Do not load all domain references or scan the whole repository by default.
5. When a change alters a documented route, contract, data source or verification setup, update that reference with the observed source revision/date and verification scope. Keep the index concise. Preserve unknowns instead of promoting assumptions to facts.

Topic references are in the repository, relative to its root:
- `docs/agent-context/ORDERS.md`: request origins, order notes, output fields and related flows.
- `docs/agent-context/WEBSITE.md`: CMS, pricing/coverage pages, SEO and social metadata.
- `docs/agent-context/VERIFICATION.md`: test isolation, builds and deployment evidence boundaries.
- `docs/agent-context/SKILLS.md`: installed skills, provenance and usage scope.

If the map is missing in another checkout, create a compact source-backed index for the requested area after checking its manifests and entry points. Do not infer facts from the CleanStation name alone. Keep credentials and customer records out of context documents.

The portable source for this skill is `docs/agent-context/cleanstation-context/SKILL.md`; the account-level installed copy points to the tracked context files. Synchronize the installed skill only when the skill instructions themselves change. Updating the topic map does not require reinstalling it.
