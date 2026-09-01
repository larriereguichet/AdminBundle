# Twig components

Grids are rendered with Symfony UX Twig components. Each level of the view model has one.

| Component | Class | Default template |
|---|---|---|
| `lag_admin:grid` | `Twig\Component\Grid` | `@LAGAdmin/components/grid.html.twig` |
| `lag_admin:table_grid` | `Twig\Component\TableGrid` | `@LAGAdmin/components/table_grid.html.twig` |
| `lag_admin:row` | `Twig\Component\Row` | `@LAGAdmin/components/row.html.twig` |
| `lag_admin:cell` | `Twig\Component\Cell\Cell` | `@LAGAdmin/components/cell.html.twig` |
| `lag_admin:table_header` | `Twig\Component\GridHeader` | `@LAGAdmin/components/table_header.html.twig` |
| `lag_admin:links` | `Twig\Component\Links` | `@LAGAdmin/components/links.html.twig` |
| `lag_admin:link` | `Twig\Component\Cell\Link` | `@LAGAdmin/components/cells/link.html.twig` |
| `lag_admin:text` | `Twig\Component\Text` | `@LAGAdmin/components/cells/text.html.twig` |
| `lag_admin:map` | `Twig\Component\Cell\MapComponent` | `@LAGAdmin/components/cells/map.html.twig` |
| `lag_admin:image` | `Twig\Component\Cell\ImageComponent` | `@LAGAdmin/components/cells/image.html.twig` |
| `lag_admin:form` | `Twig\Component\Cell\FormComponent` | `@LAGAdmin/components/cells/form.html.twig` |

Rendering a grid is one tag:

```twig
<twig:lag_admin:grid grid="{{ grid }}" data="{{ data }}" />
```

## Dynamic templates

Two of the bundle's interfaces make a component's template and attributes come from metadata
rather than from the `AsTwigComponent` attribute:

**`TemplateComponentInterface`** — the component decides its own template at render time. A
`PreRenderEvent` listener swaps it in. This is how a grid renders through its `template` option
and how a cell renders through its property's `template`:

```php
final class Grid implements TemplateComponentInterface
{
    public View\GridView $grid;

    public function getTemplate(): ?string
    {
        return $this->grid->template;   // whatever the grid metadata said
    }
}
```

**`AttributeComponentInterface`** — the component contributes default HTML attributes, merged
under whatever the caller passed:

```php
public function getAttributes(): array
{
    return $this->property?->getAttributes() ?? [];
}
```

The practical consequence: to change the markup of a grid or a cell you rarely need a new
component. Set `template` on the grid or on the property and the existing component renders it.

## Template versus component on a property

A property can point at either:

```php
new Property(name: 'price', template: 'admin/grid/price.html.twig')   // a template
new Link(name: 'title', component: 'lag_admin:link')                  // a component
```

Use a **template** for markup: it is simpler, it receives `data`, `property`, `attributes`,
`cell` and `context`, and it needs no PHP class.

Use a **component** when the cell needs logic — computing a URL, resolving a label, holding
state. Cell components receive the `CellView` in `mount()`.

## Writing a cell component

```php
namespace App\Twig\Component;

use LAG\AdminBundle\Grid\View\CellView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'app:rating',
    template: 'admin/components/cells/rating.html.twig',
)]
final class RatingComponent
{
    public int $stars = 0;
    public ?string $label = null;

    public function mount(CellView $cell): void
    {
        $this->stars = (int) $cell->data;
        $this->label = $cell->label;
    }
}
```

```twig
{# admin/components/cells/rating.html.twig #}
<span class="rating">
    {% for i in 1..5 %}
        <i class="bi bi-star{{ i <= stars ? '-fill' : '' }}"></i>
    {% endfor %}
</span>
```

```php
new Property(name: 'rating', component: 'app:rating')
```

## Overriding a bundle component's template

You do not have to replace the class. Put your own file at the same path under
`templates/bundles/LAGAdminBundle/` — for instance
`templates/bundles/LAGAdminBundle/components/cells/link.html.twig` — and the component keeps
working with your markup.

## Live components

`symfony/ux-live-component` is a dependency, so a component of yours can be a live component
when a cell needs to react without a page reload.

## Next

[Custom properties](custom-properties.md).
