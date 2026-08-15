# Rule — Coding standards

Enforced mechanically by `make cs`, `vendor/bin/phpstan analyse` and `make rector`.
Write code that already passes them instead of relying on the fixer.

## PHP

- PHP **8.4** minimum. Symfony **7.0** minimum. Use modern syntax: constructor property
  promotion, readonly properties, enums, first-class callables, `match`, named arguments
  for optional metadata arguments.
- `declare(strict_types=1);` on every PHP file (enforced by `declare_strict_types`).
- Style: `@Symfony` + `@Symfony:risky` rulesets, with these project deviations:
  - trailing comma required in multiline calls/arrays, forbidden in single line
  - no Yoda conditions
  - `elseif`, never `else if`
  - multiline `throw` allowed
  - one `use` statement per trait insert
- PHPStan level **6** on `src/` and `config/`: annotate array shapes and generics
  (`array<string, PropertyInterface>`, `Collection<int, Book>`), never widen a type
  to silence the analyser.
- No `dump()`, `dd()`, `var_dump()`, `print_r()` left behind — `make var-dump-checker`
  scans `src/` and `tests/`.

## Design conventions of this bundle

- Program against interfaces (`ResourceInterface`, `OperationInterface`,
  `PropertyInterface`, `ProviderInterface`, `ProcessorInterface`); type-hint the
  interface, not the implementation.
- Metadata objects are attributes in `src/Metadata/Attribute/` and are immutable —
  build variants with `with*()`-style methods rather than mutating.
- New behaviour is added by decorating a provider/processor or by listening to a
  `ResourceControllerEvent`, not by overriding a controller.
- Optional third-party integrations go under `src/Bridge/<Vendor>/` and must degrade
  gracefully when the dependency is absent.
- Services are declared in the PHP config files under `config/services/`, one file per
  concern. Prefer explicit wiring over autowiring magic for public bundle services.
- Exception messages are English, actionable, and name the resource/operation involved.

## Templates & assets

- Twig templates live in `templates/`, Twig components under `templates/components/`
  with their PHP class in `src/Twig/Component/`.
- Keep templates overridable: never inline markup that a user would need to override —
  extract it into its own template.
- Front-end sources are in `assets/`, built with Webpack Encore through Docker
  (`make assets`). Never edit `public/build/` by hand.

## Tests

- A behaviour change comes with a test. Unit tests mirror the `src/` tree under
  `tests/unit/`.
- Functional tests boot `LAG\AdminBundle\Tests\Application\TestKernel` and need the
  MySQL/MariaDB container.
- Fixtures use `zenstruck/foundry` factories in `tests/fixtures/`; final classes are
  mockable through `dg/bypass-finals`.
