# Skill selection and provenance

Installed for the current developer's Codex account on 2026-11-10, under
`C:/Users/Pc/.codex/skills/`. These installations are separate from application dependencies and the production deployment.

| Skill | Purpose | Source |
| --- | --- | --- |
| `find-skills` | Discover additional capabilities when a task needs one | https://github.com/vercel-labs/skills/tree/main/skills/find-skills |
| `laravel-specialist` | Laravel, Eloquent, API resources, queues and focused framework guidance | https://github.com/Jeffallan/claude-skills/tree/main/skills/laravel-specialist |
| `debugging-wizard` | Reproduce, trace the actual route, compare hypotheses and verify the fix | https://github.com/Jeffallan/claude-skills/tree/main/skills/debugging-wizard |
| `verification-before-completion` | Match completion claims to fresh verification evidence | https://github.com/obra/superpowers/tree/main/skills/verification-before-completion |
| `cleanstation-context` | Navigate and maintain this repository's compact context map | [Portable skill source](cleanstation-context/SKILL.md) |

Selection evidence, observed on 2026-11-10: skills.sh reported approximately 23.8K installs for laravel-specialist, 5.3K for debugging-wizard and 270.3K for verification-before-completion; their repositories were listed at approximately 12.3K and 315.1K stars respectively. These are discovery signals, not correctness guarantees. Entrypoint instructions were reviewed before installation. Third-party reference examples still require review when used.

Discovery pages:
- https://skills.sh/jeffallan/claude-skills/laravel-specialist
- https://skills.sh/jeffallan/claude-skills/debugging-wizard
- https://skills.sh/obra/superpowers/verification-before-completion

Existing project skills in `.agents/skills/` cover UI/UX, styling, brand, design systems, banners and slides. Select those for corresponding tasks; no duplicate design package was added. `.agents/` is ignored by this repository's Git configuration, so do not assume a fresh clone contains those installed skills.

Context maintenance is handled through tracked `AGENTS.md` and these topic references. The global context skill points back to them; it does not contain a second copy of the project map. No background synchronization or perpetual memory service is installed. Agents update the relevant reference during their work and use Git to detect stale knowledge at the next session.

External skills are installed with the existing skill-installer helper, for example:

```text
python <skill-installer>/scripts/install-skill-from-github.py --repo Jeffallan/claude-skills --path skills/laravel-specialist skills/debugging-wizard
python <skill-installer>/scripts/install-skill-from-github.py --repo obra/superpowers --path skills/verification-before-completion
```

Installation alone does not apply every skill to every task. Use the routing table in `AGENTS.md`. Keep this project's stack and targeted verification scope when reading generic framework templates.
