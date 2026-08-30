---
name: docs-coverage
description: Check that every feature of the bundle is documented, and that the documentation keeps the shape a Symfony bundle is expected to have — install, getting started, configuration reference, one page per feature, and copy-pastable examples for the extension points and template overrides. Use before a release or a merge into master, when the user asks whether the docs are complete or up to date, or after adding an attribute, a configuration key, a Twig component or a bridge.
---

# Check the documentation coverage of LAG AdminBundle

This is an **audit**, not a writing session. Build the inventory from the code, compare it
with the documentation, report the gaps ranked by how much they hurt a newcomer. Write
documentation only if the user asks for it after seeing the report.

The code is the source of truth. A feature that exists in `src/` and is absent from `docs/`
is a gap; a page describing something no longer in `src/` is rot, and counts too.

## Step 0 — Find the documentation

The documentation is not always on the branch you are standing on. Check before concluding
that everything is missing:

```bash
git ls-files docs/                        # docs on this branch
git branch -a --list '*docs*'             # a dedicated docs branch may hold the current set
git worktree list                         # it may be checked out in a sibling worktree
```

State clearly in the report which tree you audited. Auditing `feat/v2.0` and reporting
"nothing is documented" while the pages sit unmerged on `docs/v2` is a false alarm.

## Step 1 — Expected shape

The layout follows what popular Symfony bundles do, and what a reader expects to find in
this order. Report a missing or misplaced section as a finding of its own.

| Section | File | Answers |
|---|---|---|
| Installation | `docs/installation.md` | composer require, bundle registration, assets, the database |
| Getting started | `docs/getting-started.md` | one entity to a working admin, in one readable page |
| Configuration reference | `docs/configuration.md` | every `lag_admin` key, its default and its effect |
| Concepts | `docs/concepts/*.md` | one page per feature, what it is and how it behaves |
| Reference | `docs/reference/*.md` | every argument of each attribute, exhaustively |
| Customization | `docs/customization/*.md` | how to replace, decorate or override — with code |

Two rules that matter more than the file list:

- **Getting started must stay short.** If it documents every option it stops being a start.
- **Reference must be exhaustive, concepts must not be.** A concept page explains and links
  to the reference; a reference page lists every argument with its type and default.

## Step 2 — Build the inventory from the code

Run these and keep the output. Each line is something a user can write, so each line needs
a home in the documentation.

```bash
# Declarative features: the attributes a user puts on an entity
ls src/Metadata/Attribute/*.php | xargs -n1 basename | sed 's/.php//'

# Configuration keys
grep -oE "(arrayNode|scalarNode|booleanNode|integerNode)\('[a-z_]+'\)" \
  src/DependencyInjection/Configuration.php | grep -oE "'[a-z_]+'" | tr -d "'" | sort -u

# Twig components, overridable by template
grep -oE "'key' => '[a-z_:]+'" config/services/twig.php | sort -u

# Twig functions and filters
grep -rhoE "new Twig(Function|Filter)\('[a-z_]+'" src/Twig/Extension/ | sort -u

# Optional integrations
ls -d src/Bridge/*/ | xargs -n1 basename

# Events a user can listen to
grep -rhoE "const string [A-Z_]+ = '[a-z_.]+'" src/Event/

# Extension interfaces a user can implement
ls src/State/Provider/*Interface.php src/State/Processor/*Interface.php \
   src/Grid/DataTransformer/*Interface.php src/Condition/Matcher/*Interface.php \
   2>/dev/null | xargs -n1 basename | sed 's/.php//'

# Overridable templates
find templates -name '*.html.twig' | sort
```

## Step 3 — Match inventory against documentation

For each inventory line, search the documentation and classify:

```bash
grep -rl "<name>" docs/                   # mentioned at all?
grep -rn "<name>" docs/reference/         # documented exhaustively?
```

- **Documented** — named in a concept or reference page, with its arguments and an example.
- **Mentioned** — the name appears, but nothing says what it does or how to use it.
- **Absent** — no occurrence.

Treat "mentioned" as a gap. A reader who greps the docs and finds only a name in a list is
no better off than one who finds nothing.

## Step 4 — Check the examples, not just the presence

A page that describes a feature without showing it is half a page. For each of these,
verify a **copy-pastable** code block exists:

- Every attribute in `src/Metadata/Attribute/` — a PHP snippet showing it on an entity.
- Every extension interface — a class implementing it, plus its service declaration, since
  this bundle prefers explicit wiring over autowiring.
- Every bridge — what to install, and what changes once it is there.
- **Template overrides** — the path a user must create. The bundle is a standard Symfony
  bundle with `getPath()` returning its root, so an application overrides
  `@LAGAdmin/components/table_grid.html.twig` by creating
  `templates/bundles/LAGAdminBundle/components/table_grid.html.twig`. That path must appear
  in the documentation, spelled out; it is the single most asked question about a bundle
  that renders HTML.
- **Twig components** — how to swap one, and what a replacement must expose.

Check the examples still compile against the current code. An example that passes an
argument an attribute no longer accepts is worse than no example.

## Step 5 — Report

One table, gaps first, most damaging at the top:

```
| Feature | Kind | Status | Where it should live |
|---|---|---|---|
| Compound | attribute | absent | docs/reference/properties.md + example |
| grid_templates | config key | mentioned | docs/configuration.md |
```

Close with the three counts — documented, mentioned, absent — and name the single gap that
would cost a newcomer the most time. Do not pad the report: a short list of real gaps is
worth more than an exhaustive one that mixes them with cosmetic remarks.

## Rules

- Report before writing. The user decides what gets documented and in what order.
- Never invent behaviour to fill a page. If the code is the only description of a feature,
  read it, and say in the report that the behaviour was derived from the code.
- Documentation is written in English, like everything else in this repository.
- A feature added without documentation is a finding on the pull request that added it, not
  a backlog entry.
