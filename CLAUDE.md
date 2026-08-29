# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Is

**LAG AdminBundle** (`lag/adminbundle`) is a Symfony 7.0+ bundle (PHP 8.4+) that generates administration interfaces from PHP attributes. Resources are declared with `#[Resource]` on entity classes; the bundle generates routes, controllers, grids, forms, and menus from that metadata at boot time.

## Project rules

These are binding — read them before writing code or touching Git:

@.claude/rules/coding-standards.md
@.claude/rules/commits-and-merge-requests.md

Short version: **everything written into the repository is in English** (code, comments,
commit messages, MR descriptions), commits follow Conventional Commits, and `make tests`
must be green before a commit is proposed.

Use the `/review` skill to review a change before committing it.

## Commands

```bash
# Run the full quality suite (phpunit + phpstan + rector + var-dump-check + cs)
make tests

# Run tests only (with coverage)
XDEBUG_MODE=coverage vendor/bin/phpunit

# Stop on first failure
vendor/bin/phpunit --stop-on-failure

# Run a single test file or method
vendor/bin/phpunit tests/unit/path/to/FooTest.php
vendor/bin/phpunit --filter testMethodName

# Unit suite only — fast, no database required
vendor/bin/phpunit --testsuite "AdminBundle Test Suite"

# Functional suite only — requires the database container to be up
docker compose up -d database
vendor/bin/phpunit --testsuite "AdminBundle Functional Test Suite"

# Static analysis
vendor/bin/phpstan analyse

# Code style check / fix
make cs          # dry-run
make cs.fix      # apply fixes

# Rector (modernization) check / apply
make rector
make rector.fix

# Backward compatibility check
make bc.check

# Check for forgotten debug output
make var-dump-checker

# Assets (requires Docker)
make assets          # install + production build
make assets.watch    # dev watch mode
```

Functional tests require a MySQL database on `127.0.0.1:3366` (non-standard port) with credentials `admin_test:admin_test`, database `admin_test`. It is provided by the `database` service in `compose.yaml` (MariaDB 11) — start it with `docker compose up -d database`. The test app kernel is `LAG\AdminBundle\Tests\Application\TestKernel`.

### Quality gates

| Gate | Command | Scope |
|---|---|---|
| Tests | `vendor/bin/phpunit` | `tests/unit` + `tests/functional` |
| Static analysis | `vendor/bin/phpstan analyse` | `src`, `config` — level 6 |
| Code style | `make cs` | `config`, `src`, `tests/unit` — `@Symfony` + risky |
| Modernization | `make rector` | `config`, `src`, `tests` (except `tests/app`) — PHP 8.3 sets |
| Debug leftovers | `make var-dump-checker` | `src`, `tests` |
| Backward compatibility | `make bc.check` | public API vs last release |

CI (`.github/workflows/ci.yaml`) runs the unit and functional suites, php-cs-fixer, phpstan,
var-dump-check and Rector on PHP 8.4 for every push. `make bc.check` is the only local-only gate.

`composer.lock` is not versioned, so CI resolves dependencies afresh on every run: a new release of
php-cs-fixer, phpstan or Rector can turn a green branch red without a single line of code changing.
When a gate fails in CI but passes locally, compare the tool version before looking for the culprit
in the diff.

## Architecture

### Core Metadata Model

Everything flows from three metadata objects, all defined as PHP attributes in `src/Metadata/Attribute/`:

- **`Resource`** — top-level descriptor for an administered entity. Holds `shortName`, `application`, `resourceClass`, `operations`, `properties`, `provider`, `processor`, `identifiers`, `permissions`.
- **`OperationInterface`** — one CRUD action on a resource (Index, Show, Create, Update, Delete, or custom). Each operation carries its own route, form, provider/processor overrides, and permissions.
- **`PropertyInterface`** — how a single entity field is displayed/processed (Text, Image, Date, RichText, Boolean, Collection, Enum, …).

Resource names are namespaced as `{application}.{shortName}` (e.g. `admin.article`). Routes follow the pattern `{application}.{resource}.{operation}` by default.

### Provider / Processor Pattern

Inspired by API Platform:

- **Provider** (`src/State/Provider/ProviderInterface`) — supplies data for a given operation (typically `ORMProvider` via Doctrine).
- **Processor** (`src/State/Processor/ProcessorInterface`) — persists/processes data after form submission (typically `ORMProcessor`).

Both can be overridden per-resource or per-operation in the attribute arguments.

The pattern uses **Composite + Decorator composition**: `CompositeProvider` / `CompositeProcessor` route to the concrete implementation by operation class name, while additional providers/processors (e.g. `FilterProvider`, `ValidationProcessor`, `WorkflowProcessor`, `FlashMessageProcessor`) wrap them as decorators.

### Request Lifecycle

```
HTTP Request
  → Routing (dynamically generated from Resource metadata at boot)
  → Operation resolved from `_lag_operation` request attribute
  → Value Resolvers inject Resource/Operation/Grid metadata into controller arguments
  → Controller (IndexResources / ShowResource / ProcessResource)
  → Context built (filters, identifiers, URI variables, pagination, sorting)
  → Provider::provide() → data
  → Form handling (if applicable)
  → Processor::process() (on POST)
  → Response handlers select output strategy (Template / JSON / Redirect / Form errors)
  → Grid/View building
  → Twig template rendered
```

### Value Resolvers

Symfony's argument resolver system is used to auto-inject resolved metadata into controllers. Value resolvers in `src/Request/` resolve `ResourceMetadataInterface`, `OperationMetadataInterface`, and `GridMetadataInterface` from the `_lag_operation` request attribute, eliminating manual lookups in controllers.

### Event System

`ResourceEventDispatcher` (`src/EventDispatcher/`) dispatches `ResourceControllerEvent` objects at key lifecycle points (before/after provide, before/after process). Event listeners in `src/EventListener/` react to these — e.g. `DefineResourceContextListener` sets the operation context on the request. Custom application logic hooks into these events rather than overriding controllers.

### Grid System

Grids render collections. The `GridInterface` metadata drives `GridFactory`, which creates a `Grid` view model (Grid → Rows → Cells) rendered via Twig. Data transformers (`src/Grid/DataTransformer/`) convert raw values into display-ready output (e.g. `EnumDataTransformer`, `CountDataTransformer`).

The view-builder pattern in `src/Grid/ViewBuilder/` orchestrates this: `GridBuilder` → `RowBuilder` → specialised `CellBuilder` variants (`DataCellBuilder`, `CompoundCellBuilder`, `CollectionCellBuilder`, `ConditionCellBuilder`, `SecurityCellBuilder`).

### Response Handlers

`src/Response/Handler/` contains handlers that select and compose the HTTP response based on request type and operation outcome: `TemplateResponseHandler` (Twig render), `JsonResponseHandler`, `RedirectResponseHandler`, and `FormErrorResponseHandler`. Controllers delegate to these rather than building responses directly.

### Form System

Custom form types live in `src/Form/Type/` (e.g. `ImageType`, `AutoCompleteType`, `ResourceCollectionType`). A metadata-driven form guesser (`src/Form/Guesser/`) derives field types and options from `PropertyInterface` metadata, so most forms are generated without explicit form class definitions.

### Bridge Pattern

External integrations live under `src/Bridge/` and are optional:

| Bridge | Purpose |
|---|---|
| `Doctrine/ORM` | Default Provider/Processor, metadata introspection, filter applicators |
| `KnpMenu` | Dynamic sidebar/contextual menus built from resource metadata |
| `Flysystem` | File/media upload storage |
| `LiipImagine` | Image thumbnails |
| `QuillJs` | Rich-text (WYSIWYG) field support |

### DI Configuration

Services are wired in PHP config files under `config/services/` (one file per concern: `grids.php`, `metadata.php`, `view.php`, etc.), loaded by `config/services.php`. The bundle extension is `src/DependencyInjection/LAGAdminExtension.php`; configuration schema is in `src/DependencyInjection/Configuration.php`.

Key `lag_admin` config keys: `mapping.paths` (where to scan for `#[Resource]` attributes, default `src/Entity`), `applications` (routing/translation patterns per app), `uploads`, `request_parameter`, `cache`.

### Test Layout

- `tests/unit/` — isolated unit tests, mirror the `src/` structure
- `tests/functional/` — full request/response cycle against the test app
- `tests/app/src/Entity/` — test entities (User, Book, Author) used by functional tests
- `tests/app/config/admin/` — test resource and grid definitions (PHP config format)
- `tests/fixtures/` — Foundry factories and fixture data

Tests use `zenstruck/foundry` for entity factories and `dg/bypass-finals` to mock final classes.

## Active Work

Development happens on `feat/v2.0`, branched off `master`. It restructures view building
around the builder pattern (`GridBuilder` → `RowBuilder` → `CellBuilder`) and moves the
metadata attributes into `src/Metadata/Attribute/`.

`TODO.md` tracks the remaining design decisions: consolidating `ResourceInterface`,
replacing "actions" with "links", adding a `LinkGroup` concept, unifying the workflow
interfaces, grouping resource menu items, and caching ORM metadata via Symfony Cache.

Since this is a 2.0 line, breaking changes are acceptable — but they must be intentional,
called out in the commit footer (`BREAKING CHANGE:`), and documented in `HISTORY.md`.
