# Custom properties

Three levels, from the cheapest to the most reusable.

## Level 1 — a generic property with a template

For a one-off column, no PHP class is needed:

```php
new Property(
    name: 'price',
    propertyPath: true,
    template: 'admin/articles/grid/price.html.twig',
    attributes: ['class' => 'text-end'],
)
```

```twig
{# admin/articles/grid/price.html.twig #}
<span {{ attributes }}>{{ (data.priceInCents / 100)|format_currency('EUR') }}</span>
```

`propertyPath: true` hands the whole record to the template. With a real path
(`propertyPath: 'author.name'`), `data` is the resolved value instead.

The template also gets `property`, so options travel with the metadata:

```php
new Property(name: 'price', propertyPath: true, template: '…', attributes: ['data-currency' => 'EUR'])
```

## Level 2 — a data transformer

When the *value* needs converting but the markup does not:

```php
namespace App\Grid\DataTransformer;

use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;

final readonly class MoneyDataTransformer implements DataTransformerInterface
{
    public function transform(PropertyInterface $property, mixed $data): mixed
    {
        return $data === null ? null : $data / 100;
    }
}
```

```php
new Text(name: 'price', dataTransformer: MoneyDataTransformer::class, suffix: ' €')
```

`DataTransformerInterface` is autoconfigured — no tag needed. The transformer runs in
`DataCellBuilder`, after the property path is resolved and before the template renders.

Bundled transformers: `EnumDataTransformer` (enum → backing value, pairs with `Map`),
`CountDataTransformer` (collection → size), `FormDataTransformer`, and `ImageDataTransformer`
from the LiipImagine bridge.

## Level 3 — a property class

When the same display style is used across resources, give it a name. Extend `Property` (or a
closer built-in) and set your own defaults:

```php
namespace App\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Property;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final class Money extends Property
{
    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $rowAttributes
     * @param array<string, mixed> $headerAttributes
     * @param array<string>|null $permissions
     */
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = 'admin/grids/properties/money.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,
        ?string $component = null,
        ?string $translationDomain = null,

        #[Assert\Currency]
        private string $currency = 'EUR',
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
            translatable: $translatable,
            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
            component: $component,
            translationDomain: $translationDomain,
        );
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function withCurrency(string $currency): self
    {
        $self = clone $this;
        $self->currency = $currency;

        return $self;
    }
}
```

```twig
{# templates/admin/grids/properties/money.html.twig #}
<span {{ attributes }}>{{ data|format_currency(property.currency) }}</span>
```

```php
#[App\Metadata\Attribute\Money(currency: 'CHF')]
private int $price = 0;
```

Three conventions to follow, all enforced elsewhere in the bundle:

* **Repeat the parent constructor arguments** and forward them by name. Property attributes are
  positional-argument-friendly only through named arguments, and skipping an argument silently
  drops the option.
* **Stay immutable.** Add a `with*()` method per new option; never a setter.
* **Validate with constraints.** Metadata objects are validated as objects — the bundle enables
  auto-mapping on its own metadata classes, and yours can carry `Assert\*` attributes too.

## Making it form-aware

The form guesser matches on the property class. A custom class the guesser does not know about
produces no form field. Either declare the field in your own form type, or decorate the guesser:

```php
$services->set(App\Form\Guesser\MoneyFormGuesser::class)
    ->decorate(LAG\AdminBundle\Form\Guesser\FormGuesserInterface::class)
    ->arg('$formGuesser', service('.inner'))
;
```

```php
final readonly class MoneyFormGuesser implements FormGuesserInterface
{
    public function __construct(private FormGuesserInterface $formGuesser) {}

    public function guessFormType(OperationInterface $operation, PropertyInterface $property): ?string
    {
        if ($property instanceof Money) {
            return MoneyType::class;
        }

        return $this->formGuesser->guessFormType($operation, $property);
    }

    public function guessFormOptions(OperationInterface $operation, PropertyInterface $property): array
    {
        if ($property instanceof Money) {
            return ['currency' => $property->getCurrency(), 'divisor' => 100];
        }

        return $this->formGuesser->guessFormOptions($operation, $property);
    }
}
```

## Next

[Custom state](state.md).
