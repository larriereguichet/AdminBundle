# TODO

## Design

- [ ] ResourceInterface
- [ ] Remove actions and use links
- [ ] Use Symfony cache for ORM MetadataHelper
- [ ] Use only one workflow interface
- [ ] Use resource group to group resource menu item
- [ ] Add LinkGroup to group links into a single button

## Bugs found while writing the documentation

Ordered by severity. Each one was checked against the code on `feat/v2.0`; the first two were
reproduced by booting the test kernel and dumping the router.

- [x] **Item operations lose their identifier in the generated path.**
      `OperationsMetadataFactory` builds the path from `$operation->getIdentifiers()`, which is
      still `null` at that point — the resource identifiers are only applied further down, by
      `withIdentifiers()`. Result: `/authors/update` instead of `/authors/{id}/update`, so a
      resource declared with `#[Resource]` alone has no usable show/update/delete route.
      *Fixed: the effective identifiers are resolved before the path is generated, with the
      precedence `operation.identifiers > resource.identifiers`.*
- [x] **`OperationsMetadataFactory`: `if (!$resource instanceof Create)` should test
      `$operation`.** `$resource` is a `Resource` and can never be a `Create`, so the branch is
      always taken. Same method as the bug above. *Fixed together with the bug above.*
- [x] **`SortingContextBuilder` reads the wrong query parameter for the direction:**
      `$context['order'] = $request->query->get($sortParameter)` should use `$orderParameter`.
      The direction ends up being the column name, so sorting never orders correctly.
- [x] **The container does not compile when `symfony/object-mapper` is installed without an
      `ObjectMapperInterface` service.** `config/services/state.php` guards on
      `interface_exists()` and then aliases a service the framework does not register:
      `The service "MappingProvider" has a dependency on a non-existent service
      "Symfony\Component\ObjectMapper\ObjectMapperInterface"`. *Fixed: `ObjectMapperCompilerPass`
      removes the mapping services when no object mapper service is registered, and
      `Bridge\ObjectMapper\SymfonyObjectMapper` adapts the Symfony mapper to
      `Mapper\ObjectMapperInterface` — the alias could never have satisfied that type hint.
      `symfony/object-mapper` must be a non-dev dependency for input/output mapping to be wired.*
- [x] **`components/grid.html.twig` iterates the rows twice** — nested
      `{% for row in grid.rows %}` — so every row is rendered once per row.
- [x] **`resources/operation.html.twig` renders `product.description`** in the description
      block; it should be `operation.description`.
- [x] **Unregistered Twig components are referenced:** `components/links.html.twig` uses
      `<twig:lag_admin:grid_cell>` and `components/grid.html.twig` uses
      `<twig:lag_admin:grid_title>`; the registered keys are `lag_admin:cell` and
      `lag_admin:grid`. The `Grid` metadata also defaults to `component: 'lag:table_grid'`,
      which is not registered either. *Fixed: `grid_cell` renamed to `lag_admin:cell`, the title
      extracted into `components/grid_title.html.twig` (there is no title component to register),
      and the `Grid` default `component` set to `null`.*
- [x] **`ResourcePropertiesMetadataFactory` leaks `$label` and `$sortingPath` across loop
      iterations.** A non-sortable property inherits the previous property's sorting path.
      Declare both inside the loop.
- [x] **Sorting context key mismatch.** `SortingProvider` reads `$context['order_by']` while
      call sites use `orderBy` (fermetriomphe does). *Decided: `order_by` is the only supported
      key, for consistency with the other context keys (`sort`, `order`, `row_data`,
      `translation_domain`). No bundle change; the three `orderBy` call sites in fermetriomphe
      (`config/resources/admin/customers.php`, `shop/orders.php`, `shop/products.php`) have to be
      renamed.*
- [x] **`applications.translation_domain` has no default** and is dereferenced unconditionally
      in `ApplicationMetadataFactory`. *Fixed: defaults to `messages`, matching the `Application`
      attribute.*
- [ ] **Filters are not rendered.** The `filters` block of `resources/index.html.twig` is
      commented out with a `TODO`, so declared filters build a form nobody displays. Deliberately
      left for last: the goal is not only to uncomment the block but to make the whole feature
      work again — filters declared as PHP attributes, covering the common cases one wants to
      filter a grid on, and by default every displayed property of a grid should be filterable
      whenever the PHP class allows the filter and its form type to be guessed. Start by checking
      what still works in the existing `FilterType` / filter applicator chain.

## Test coverage

Measured on 2026-08-09 on `feat/v2.0`, unit suite only (492 tests):

```
php -d pcov.enabled=1 -d xdebug.mode=off vendor/bin/phpunit \
    --testsuite "AdminBundle Test Suite" --coverage-text=php://stdout
```

```
Classes : 53.25 % (123/231)
Methods : 72.48 % (619/854)
Lines   : 76.10 % (2614/3435)
```

The line figure is misleading: `Metadata/Attribute` (1242 statements, 36 % of the total) and
`Metadata/Factory` (424) sit at ~98 % and are mostly getters and constructors. Excluding them,
the rest of the bundle is at **55 %**, and **99 files out of 231 have no coverage at all**. The
functional suite does not make up for it — it holds only two tests (`AuthorResourceTest`,
`BookResourceTest`).

Priorities, highest value first:

- [ ] **P1 — `Grid/ViewBuilder` (48.9 %, 72 uncovered statements).** This is the code being
      refactored on `feat/v2.0`, so it carries the most risk. Five builders out of eleven are at
      0 %: `RowBuilder` (25), `CollectionCellBuilder` (15), `LinkBuilder` (15), `CellBuilder`
      (10), `ConditionCellBuilder` (7). The untested branches are the ones holding the logic:
      the `null` returned by `LinkBuilder::buildLink()` and filtered by `RowBuilder`, the
      exception thrown by `CollectionCellBuilder` on non-iterable data, the empty cell returned
      by `ConditionCellBuilder`. `DataCellBuilder`, `CompoundCellBuilder`, `SecurityCellBuilder`,
      `HeaderBuilder` and `GridBuilder` are already tested, so the chain is only half verified —
      the worst case.
- [ ] **P2 — `Bridge/Doctrine/ORM` providers (51.4 %, 124 uncovered statements).**
      `SortingProvider` (27 statements, 0 %) is arguably the most branch-heavy class of the
      bundle: `isSortable()`, `getSortingPath()`, and above all the `leftJoin` loop handling
      nested sorts (`author.name`) with its `*_entity` alias generation. Then
      `CollectionOutputProvider` (21), `ORMProvider` (16), `CriteriaProvider` (13),
      `ResultProvider` (12), `PropertyCollectionMetadataFactory` (19).
- [ ] **P3 — Routing, 0 % across the whole chain (~55 statements).** `ResourceRoutingLoader`
      (25), `LinkUrlGenerator` (14), `RouteNameGenerator` (7), `OperationUrlGenerator` /
      `UrlGenerator` (10). These build the routes at boot, so a regression breaks everything,
      and they are cheap to test (no heavy mocking). Best effort/value ratio of the list — and
      the stray `;;` listed under *Cleanups* survived precisely because nothing tests this class.
- [ ] **P4 — `State/Processor` and `State/Provider` (54 statements).** `MappingProvider` (19),
      `NormalizationProcessor` (12), `MappingProcessor` (7), `WorkflowProcessor` (5),
      `PartialAjaxFormProcessor` (4). Decorators, so the tests are short.
- [ ] **P5 — `Twig/Component` (49 statements, 0 %) and `Twig/Runtime` (28, 0 %).** Better
      covered by functional/rendering tests than by unit tests; low value in isolation.

Deliberately not a priority: `Form/Type` (7.9 %, 129 uncovered statements) is mostly
`buildForm()` / `configureOptions()` configuration. `DeleteTypeTest` shows the pattern if we
ever want them, but the return is poor next to P1–P3.

Structural issues, independent of the percentage:

- [ ] **The test tree no longer mirrors `src/`**, which `.claude/rules/coding-standards.md`
      requires: `tests/unit/Menu/Builder` → `src/Bridge/KnpMenu/Builder`,
      `tests/unit/Request/Resolver` → `src/Request/ValueResolver`, `tests/unit/Grid/Render` →
      `src/View/Render`, `tests/unit/Grid/ViewFactory` → `src/Grid/ViewBuilder`. Direct
      consequence: `AttributeBuilder` has two near-identical tests
      (`Grid/ViewBuilder/AttributeBuilderTest` and `Grid/ViewFactory/AttributeViewFactoryTest`),
      and a class can look tested when it is not.
- [ ] **28 PHPUnit notices**: `createMock()` used where `createStub()` is the right call
      (`ContextualMenuBuilderTest`, `FilterProviderTest`, `TemplateResponseHandlerTest`,
      `ValidationProcessorTest`). Cosmetic, but the suite exits with "OK, but there were issues".
- [ ] **No coverage threshold** in `phpunit.xml.dist` nor in CI, so nothing stops the coverage
      from dropping. A `--coverage-clover` run plus a minimum threshold in
      `.github/workflows/ci.yaml` would lock in whatever we gain.

Suggested order: P1 → P3 → P2 → P4, doing the test-tree rename and de-duplication alongside P1,
in separate commits (`test:` for the new tests, `refactor(tests):` for the reorganisation).

## Findings left from the v2 code review

Raised by the review of the merged view-builder work on 2026-08-15. The HIGH ones and the two that
were reachable as a 500 from the browser were fixed on the spot; these are the remainder.

- [ ] **Properties have no translation domain.** `templates/grids/properties/boolean.html.twig`
      (same for `map`, `link`, `action`) does `{% trans_default_domain property.translationDomain %}`,
      but nothing ever populates it — `ResourcePropertiesMetadataFactory` sets label, property path
      and sorting path only. With a null domain the translator falls back to `messages` while
      `lag_admin.ui.yes` / `lag_admin.ui.no` live in `translations/admin.*.yaml`, so booleans render
      their raw keys. Default the property domain to the resource's in the factory.
- [ ] **`ResourceMenuBuilder` links to the wrong operation.** `array_pop($operations)` takes the
      *last* collection operation where the previous code took the first. A resource declaring
      `#[Index]` plus another collection operation (export, archive…) gets a sidebar entry pointing
      at the second one. Use `reset()`.
- [ ] **`embedded` operations are half-removed.** The embedded branch of `ResourceRoutingLoader`
      went away with `PathGenerator`, but `Operation::isEmbedded()`, `TemplateResponseHandler` and
      `PartialContextBuilder::EMBEDDED_REQUEST_ATTRIBUTE` all remain. Nothing sets that attribute
      any more, so `$context['partial']` is permanently false and `embedded: true` operations get no
      route. Decide: finish the removal, or restore the route.
- [ ] **`ImageUploader` does not check `fopen()`.** The handle goes straight to `writeStream()`, so
      a permission or ulimit failure raises a `TypeError` instead of the intended actionable
      exception.
- [ ] **`MediaStorageCompilerPass` may resolve an alias to an alias.** `resolveServiceId()` can
      return an id that is itself an alias, which is then passed to `getDefinition()` and throws at
      compile time. Use `findDefinition()`, which walks the chain.
- [ ] **`ResourceContext::setResource()` / `setOperation()` on an empty stack.**
      `array_key_last([])` returns null, so PHP silently creates an entry under the key `""` that no
      `pop()` will ever balance. Throw, or push explicitly.
- [ ] **Dead templates.** `grids/table/captions.html.twig` contains nested output tags
      (`{{ {{ … }} }}`), a Twig syntax error that only survives because nothing includes the file.
      `grids/properties/resource_link.html.twig` passes an `options` variable that no longer exists
      in the cell render context, and `grids/table/header.html.twig` is unreferenced. Delete them or
      bring them back into use.

## Cleanups

- [ ] **Dead configuration.** `cache`, `date_localization`, `filter_events` and the root
      `date_format` / `time_format` nodes are never read by the extension; the per-application
      `date_format` / `time_format` are stored but never used (the `Date` property carries its
      own). Either wire them or drop them from `Configuration`.
- [ ] **Translation catalogues are out of sync with the code.** `translations/admin.en.yaml` is
      empty, and `admin.fr.yaml` defines `lag_admin.actions.*` / `lag_admin.index.no_record`
      while the code emits `lag_admin.ui.*`, `lag_admin.ui.no_record`, `lag_admin.menu.*` and
      `lag_admin.batch.select_all`.
- [ ] **`flashMessage` versus `successMessage`.** `Create`/`Update`/`Delete`/`Index` expose
      `flashMessage`, `Show` exposes `successMessage`, and the parent `Operation` stores
      `successMessage`. Unify the name.
- [ ] **`FilterProvider` is both a decorator of `ProviderInterface` and tagged
      `lag_admin.state_provider`**, which makes it selectable as an operation provider. Probably
      unintended.
- [ ] **Default index path.** A collection operation defaults to `/articles/index`; the
      collection root `/articles` would be the conventional choice.
- [ ] `ResourceRoutingLoader`: stray double semicolon after the `$path` assignment.
