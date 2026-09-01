# Security

The bundle layers permission checks on top of Symfony Security. It does **not** configure a
firewall — that stays your responsibility.

## Access to an operation

Declare the roles allowed to reach an operation:

```php
new Resource(
    shortName: 'order',
    permissions: ['ROLE_ADMIN', 'ROLE_SELLER'],   // default for every operation
    operations: [
        new Index(),
        new Delete(permissions: ['ROLE_ADMIN']),  // stricter
    ],
)
```

Semantics:

* the user needs **at least one** of the listed roles;
* an operation with no `permissions` inherits the resource's;
* an **empty** list means everybody is allowed.

`AccessListener` runs on every request carrying an operation and throws `AccessDeniedException`
when the check fails, before the controller is reached. The decision is made by
`OperationVoter` under the `resource_access` attribute, so any Symfony voter or role hierarchy
you configure participates.

Check it yourself in a template:

```twig
{% if lag_admin_operation_allowed('admin.order.delete') %}
    …
{% endif %}
```

## Access to a property

A property can be hidden from users who lack a role:

```php
new Property(
    name: 'margin',
    propertyPath: true,
    template: 'admin/orders/grid/margin.html.twig',
    permissions: ['ROLE_ADMIN'],
)
```

`SecurityCellBuilder` drops the cell while building the grid, so the value never reaches the
template.

For anything more subtle than a role list, use `condition`:

```php
new Property(
    name: 'price',
    propertyPath: true,
    template: 'shop/products/grid/price.html.twig',
    condition: 'is_granted("ROLE_CUSTOMER") or is_granted("ROLE_SELLER")',
)
```

The same applies to links, which is how you hide a row action from users who may not perform
it:

```php
new Link(
    operation: 'cancel',
    text: 'order.cancel',
    condition: 'is_granted("ROLE_ADMIN") and workflow.can(this, "cancel")',
    workflow: 'order',
)
```

## Firewall

A typical configuration:

```yaml
# config/packages/security.yaml
security:
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'

    providers:
        app_user_provider:
            entity: { class: App\Entity\User, property: email }

    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: login
                check_path: login
                default_target_path: admin.article.index
            logout:
                path: logout

    access_control:
        - { path: ^/login, roles: PUBLIC_ACCESS }
        - { path: ^/administration, roles: ROLE_ADMIN }
```

The bundle ships a login controller and template if you want them:

```php
// config/routes.php
use LAG\AdminBundle\Controller\Security\Login;

$routing->add('login', '/login')->controller(Login::class);
$routing->add('logout', '/logout');
```

`Login` renders `@LAGAdmin/security/login.html.twig` with the last authentication error and
username — override that template to restyle it.

## User entities

Two small interfaces help when the administered resource *is* the user:

| Interface | Effect |
|---|---|
| `Security\PasswordAuthenticatedResourceInterface` | plain passwords submitted through a form are hashed by `GeneratePasswordListener` before the processor runs |
| `Security\RolesOwnerInterface` | the object exposes roles, checked by `PropertyPermissionChecker` |

## What is not covered

* **Row-level security.** Restricting *which* records a user sees is a provider concern: filter
  the query builder in a custom provider, or decorate `ProviderInterface`.
* **CSRF.** Handled by Symfony Forms as usual; the generated filter form disables it since it
  is a `GET` form.

## Next

[Menus](menus.md).
