# Rule — Commits & Merge Requests

## Language: English only

**All Git and forge content MUST be written in English**, regardless of the language
used in the conversation:

- commit messages (subject, body, footers)
- branch names
- merge request / pull request titles and descriptions
- review comments posted on the forge
- `CHANGELOG.md` / `HISTORY.md` entries
- code, identifiers, PHPDoc, inline comments and exception messages

Talking with the user in French is fine. Writing French into the repository is not.
If the user describes the change in French, translate it before writing the commit
or the MR description.

## Commit message format

[Conventional Commits](https://www.conventionalcommits.org/) — matching the existing
history of this repository:

```
<type>(<scope>): <subject>

<optional body: why, not what>

<optional footers: BREAKING CHANGE / Refs #123>
```

Rules:

- `type` is one of: `feat`, `fix`, `refactor`, `chore`, `test`, `docs`, `ci`, `styles`, `perf`.
- `scope` is optional and lowercase; use the subsystem, not the file path
  (`grid`, `metadata`, `view`, `routing`, `twig`, `bridge`, `event`, `services`, `deps`).
- `subject` is lowercase, imperative mood, no trailing period, max ~72 chars.
- One logical change per commit. Do not mix a refactor with a feature.
- A backward-incompatible change MUST carry a `BREAKING CHANGE:` footer explaining
  the migration path.
- **Never add a `Co-Authored-By` trailer**, and never add "Generated with Claude Code"
  or any similar attribution to commits.

Examples taken from the actual history:

```
feat(metadata): move PHP attributes to Attribute namespace and add factory chain
refactor(grid): restructure view builders with builder pattern
chore(services): update DI service configuration
fix: fix wrong event dispatcher called in the event state processor
```

## Branches

- Never commit directly on `master`. Branch first.
- Naming: `<type>/<short-english-slug>` — e.g. `feat/view-builders`, `fix/grid-actions`,
  `chore/composer`.

## Merge requests

- Title: same Conventional Commit format as a commit subject.
- Description, in English, with these sections:
  - **What** — the change, in one short paragraph.
  - **Why** — the problem it solves.
  - **Breaking changes** — explicit `none` if there are none.
  - **How to test** — the commands or the manual scenario.
- Do not append a generated-by footer.

## Before committing or pushing

- Ask the user for confirmation before `git commit` and before `git push` unless they
  already told you to go ahead in this session.
- The quality gates in `CLAUDE.md` (`make tests`) must pass, or the failures must be
  explicitly acknowledged by the user.
