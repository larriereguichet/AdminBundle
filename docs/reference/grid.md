# Reference — Grid options

`LAG\AdminBundle\Metadata\Attribute\Grid`, target: class, repeatable. Can also be declared with
`LAGAdminBuilder::addGrid()`.

| Option | Type | Default | Description |
|---|---|---|---|
| `name` | `?string` | `null` | Grid name, referenced by a collection operation's `grid` option. Required. Overridden by the name given to `addGrid()`. |
| `title` | `string\|false\|null` | `null` | Grid title. `false` renders none. Max 255 characters. |
| `type` | `?string` | `table` | Resolved to a template through `lag_admin.grid_templates`. |
| `template` | `?string` | from the type | Explicit template, bypassing `type`. |
| `component` | `?string` | `lag_admin:table_grid` | Twig component rendering the grid. Set to `null` to render through `template` instead. |
| `properties` | `array<string>` | `[]` | Ordered names of the properties rendered. Empty renders them all. |
| `attributes` | `array` | `[]` | HTML attributes of the grid element. |
| `rowAttributes` | `array` | `[]` | HTML attributes of the rows. |
| `headerRowAttributes` | `array` | `[]` | HTML attributes of the header row. |
| `headerAttributes` | `array` | `[]` | HTML attributes of the header cells. |
| `titleAttributes` | `array` | `[]` | HTML attributes of the title. |
| `options` | `array` | `[]` | Free-form options available to the template. |
| `form` | `?string` | `FormType::class` | Form wrapping the grid. |
| `formOptions` | `array` | `[]` | Options passed to it. |
| `emptyMessage` | `?string` | `lag_admin.ui.no_record` | Shown when the collection is empty. |
| `useHeaders` | `?bool` | `true` | Render the header row. |
| `sortable` | `bool` | `false` | Enable sorting links on sortable properties. |
| `sortParameter` | `string` | `sort` | Query parameter carrying the sorted property. |
| `orderParameter` | `string` | `order` | Query parameter carrying the direction. |

## Built-in grid types

| Type | Template |
|---|---|
| `table` | `@LAGAdmin/components/table_grid.html.twig` |
| `card` | `@LAGAdmin/components/card_grid.html.twig` |

Register more in `lag_admin.grid_templates`.

## The view model

`GridBuilder` turns the metadata and the data into a view model consumed by Twig:

**`GridView`** — `name`, `type`, `rows`, `attributes`, `headers`, `title`, `template`,
`options`, `context`, `emptyMessage`, `translationDomain`, `batchEnabled`, `batchIdentifier`.

**`RowView`** — the cells of one record, its attributes and its data.

**`CellView`** — `name`, `attributes`, `property`, `template`, `component`, `label`, `data`,
`context`.

**`HeaderView`** — one header cell, with its sorting information.

**`TitleView`** — the grid title.

None of them touch the container or the database: everything is resolved while the grid is
built.
