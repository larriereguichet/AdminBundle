---
name: review
description: Review the current change on this branch before committing it — diff against the target branch, intent, security, coding standards, tests, backward compatibility — then propose a Conventional Commit and a push. Use when the user asks to review, proofread, validate, or check a change, or asks "is this ready to commit / to open an MR?".
---

# Review a change in LAG AdminBundle

A review is a **gate**, not a rewrite. Run the seven steps below in order, collect
findings, report them, and only then propose a commit. Do not silently fix what you
find: report first, then apply fixes if the user asks.

## Step 0 — Scope the review

Determine what is under review and against what:

```bash
git status --porcelain                 # uncommitted work
git branch --show-current
git fetch origin --quiet
```

- **Target branch**: `master` unless the user says otherwise (`origin/master` is the
  integration branch; `feat/v2.0` is the current long-lived development branch — if the
  branch under review was cut from `feat/v2.0`, target that instead).
- **Review scope** = commits on this branch since the merge base **plus** uncommitted
  changes. Never review only the staged part unless the user explicitly asks.

```bash
BASE=$(git merge-base HEAD origin/<target>)
git diff --stat "$BASE"...HEAD         # committed on this branch
git diff --stat                        # unstaged
git diff --cached --stat               # staged
```

If the scope is empty, say so and stop.

Ask the user **what the change is supposed to achieve** if it is not obvious from the
conversation, the branch name, the commit subjects, or `TODO.md`. Everything in step 2
depends on knowing the intent.

## Step 1 — Read the actual diff

Read the full diff, not just the stat. For each touched file, understand the change in
the context of the surrounding code — open the file when the hunk is not self-explanatory.

```bash
git diff "$BASE"...HEAD -- <path>
git diff -- <path>
```

Build a short inventory: which subsystems are touched (`src/Metadata`, `src/Grid`,
`src/State`, `src/Twig`, `templates/`, `config/services/`, `tests/`), and which public
interfaces appear in the diff.

## Step 2 — Relevance to the goal

For each change, answer: *does this serve the stated goal?*

Flag:

- **Out of scope** — changes unrelated to the goal (drive-by refactors, reformatting,
  unrelated renames). They belong in a separate commit.
- **Incomplete** — the goal requires a change that is missing: a service not registered
  in `config/services/`, a metadata attribute added without its factory/guesser support,
  a new builder not wired into the composite, a new property type with no data
  transformer, a template not overridable, a translation key with no entry in
  `translations/`.
- **Wrong altitude** — logic placed in a controller instead of a provider/processor,
  behaviour hardcoded instead of decorated, a bridge concern leaking into `src/` core.
- **Leftovers** — commented-out code, `TODO` without context, debug output, dead
  private methods, unused imports, `wip` naming.

## Step 3 — Security

Check the diff against the attack surface of this bundle specifically:

- **Authorization** — does a new or modified operation declare its `permissions`? Is a
  new link/action rendered without a security check (`SecurityCellBuilder`, `is_granted`)?
  Never widen a permission implicitly.
- **IDOR** — do providers scope the query to the current user/tenant where the previous
  code did? Are `identifiers` and URI variables validated before hitting the ORM?
- **SQL injection** — in `src/Bridge/Doctrine/ORM/` filter applicators, sort handling and
  criteria: any request-derived value must go through a bound parameter, and any
  request-derived *field or direction* must be validated against a whitelist of known
  properties, never concatenated into DQL.
- **Expression language** — `src/Condition/` evaluates expressions. Confirm no
  user-supplied string reaches the expression compiler.
- **Mass assignment** — new form types / guesser changes must not expose fields the
  operation did not intend (identifiers, roles, password hashes, ownership columns).
- **Uploads** — `src/Upload/` + Flysystem: filename sanitization, path traversal in
  generated paths, MIME/extension validation, no user control over the storage prefix.
- **Output escaping** — in `templates/`: any new `|raw`, `autoescape false`, or
  attribute injection. RichText/QuillJs output must be sanitized before being rendered
  as HTML.
- **Data exposure** — `JsonResponseHandler` and grid/cell views must not serialize an
  entity wholesale where a subset was returned before.
- **CSRF** — state-changing operations (delete in particular) must keep their token
  protection.
- **Secrets** — no credentials, tokens or absolute local paths in code, config or tests.

Report each issue with the file, the line, and a concrete exploit scenario. Do not
report theoretical concerns with no reachable path.

If the change is large or security-sensitive, also run the built-in `security-review`
skill and merge its confirmed findings into this report.

## Step 4 — Coding standards

Read `.claude/rules/coding-standards.md`, then run the mechanical gates:

```bash
make cs                     # php-cs-fixer dry-run
vendor/bin/phpstan analyse
make rector                 # dry-run
make var-dump-checker
```

Then check by eye what the tools cannot see:

- `declare(strict_types=1);` present on new files
- interface-first type hints, immutable metadata objects
- naming consistent with the surrounding namespace (`*Builder`, `*Factory`,
  `*Provider`, `*Processor`, `*Listener`, `*Interface`)
- new services declared in the right `config/services/*.php` file
- English-only identifiers, comments, PHPDoc and exception messages
- comment density and idiom matching the neighbouring code — no narration of obvious code

Report tool failures verbatim (file:line + message). Do not run `make cs.fix` or
`make rector.fix` without asking — they rewrite files outside the reviewed scope.

## Step 5 — Tests

```bash
vendor/bin/phpunit --testsuite "AdminBundle Test Suite"          # unit, fast, no DB
docker compose up -d database
vendor/bin/phpunit --testsuite "AdminBundle Functional Test Suite"
```

Requirements:

- Both suites green. If a test fails, report the failure output as-is — never describe a
  failing suite as passing, and never skip a suite silently. If the database is
  unavailable, say explicitly that the functional suite was not run.
- Every behaviour change in the diff has a test covering it. A diff that touches `src/`
  with no matching change under `tests/unit/` is a finding unless the change is pure
  wiring.
- New unit tests mirror the `src/` path, use Foundry factories for entities, and assert
  behaviour rather than implementation details.

## Step 6 — Non-regression and backward compatibility

```bash
make bc.check
```

Then reason about the public API surface, which `bc.check` only partially covers:

- signature or contract changes on `ResourceInterface`, `OperationInterface`,
  `PropertyInterface`, `ProviderInterface`, `ProcessorInterface`, and the grid view
  builders — these are extension points users implement.
- renamed or removed **attribute arguments** (`#[Resource]`, `#[Property]`, …) — they are
  written in user application code.
- renamed or removed **service ids**, **route naming patterns**, **DI configuration keys**
  under `lag_admin`, **translation keys**, and **template paths** — all are override points.
- changed default behaviour that a user's existing configuration silently depends on.

For each break found, decide with the user whether it is **intended**:

- **Intended** — it must be declared: `BREAKING CHANGE:` footer in the commit, entry in
  `HISTORY.md`, and an upgrade note when a migration path exists. This is a 2.0 line, so
  intended breaks are acceptable; undeclared ones are not.
- **Unintended** — it is a Blocker. Propose keeping the old signature, deprecating it,
  or restoring the removed member.

## Step 7 — Report

Report findings grouped by severity, most severe first. Be specific and short.

- **Blocker** — must be fixed before commit: failing gate, security issue, unintended
  break, missing wiring that makes the feature non-functional.
- **Major** — should be fixed now: missing test, wrong layer, incomplete implementation.
- **Minor** — worth fixing: naming, dead code, missing type annotation.
- **Nit** — optional.

Format each finding as:

```
[Blocker] src/Grid/ViewBuilder/CellBuilder.php:42 — <one-sentence defect>
  Why it matters: <consequence>
  Fix: <concrete change>
```

Close with a verdict on one line: **Ready to commit** / **Ready with minor findings** /
**Not ready — N blockers**, plus the status of each gate (cs, phpstan, rector,
var-dump-check, unit, functional, bc).

## Step 8 — Propose commit and push

Only when there is no Blocker, or the user has explicitly accepted them.

Follow `.claude/rules/commits-and-merge-requests.md`. Propose, in English:

1. A commit split if the diff contains several logical changes — one Conventional Commit
   per change, with the files each one covers.
2. The exact commit message(s), in a fenced block, for approval:

```
<type>(<scope>): <subject>

<body: why>

BREAKING CHANGE: <only when applicable>
```

3. Then ask for confirmation before running `git add` / `git commit`.
4. After committing, ask before `git push`. If the branch has no upstream, propose
   `git push -u origin <branch>`. Never force-push without an explicit request.
5. If the user wants an MR, draft the title and description in English with the
   **What / Why / Breaking changes / How to test** sections.

Never add a `Co-Authored-By` trailer or any generated-by attribution.

## Hard rules

- Never report a gate as passing without having run it.
- Never commit or push without confirmation in this session.
- Never run a `*.fix` command, `git checkout --`, `git reset` or `git stash` on the user's
  working tree as part of a review.
- Findings must be reachable and concrete. An empty report is a valid outcome — say the
  change is clean rather than manufacturing findings.
