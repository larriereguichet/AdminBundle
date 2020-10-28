# Actions 

Each `Admin` comes with CRUD `Actions` built-in:
* Create: The `create` action display a form to create a new entity.
* Read: The `list` action appears as a menu entry by default. This action is responsible for displaying the list of entities of your Admin.
* Update: The `edit` action display the same form as create action, but for an existing entity.
* Delete: The `delete` action display a form to remove an existing entity.

Each action can be disabled. You can also new custom `Actions`.

## Basic configuration 
The basic configuration for an `Action` is the following :

```yaml
# config/admin/resources/article.yaml
article:                 
    entity: App\Entity\Article
    # The default CRUD Actions are create, edit, remove and list. You can remove the action you do not want.
    # You can also add your custom Actions. This is the default configuration values. This override the Admin 
    # configuration and the Application configuration
    actions:
        create: ~
        edit: ~
        list: ~
        delete: ~
```

If you want to disable an action, just remove it from the actions list :

```yaml
# config/admin/resources/article.yaml
article:                 
    entity: App\Entity\Article    
    actions:
        create: ~
        edit: ~
        list: ~
        #delete: ~ The delete action is not available anymore
```

You can configure parameters in the Admin. Those parameters will defined for all `Actions` in the `Admin`. If a 
parameter is defined in the `Action`, it will override the one configured in the `Admin`.

## Create action

This action is used to display the `create` form of the configured entity in the parent Admin.
You can define the following options for the `create` `action` :

```yaml
# config/admin/resources/article.yaml
article:
    entity:
        actions:
            create:
                title: lag.admin.article.create
                icon: null      
                template: ~                    
                form: null
                fields: []
                permissions: ROLE_ADMIN
                route: ~
                route_parameters: []
                path: ~
                route_requirements: []
                route_defaults':
                    _controller: LAG\AdminBundle\Admin\Admin
                class: LAG\AdminBundle\Admin\Action
                load_strategy: none
```

> All parameters are optional; this is the default configuration 

### Use a custom form
By default, the form uses all the fields of the entity, and the primary key is mandatory. But you may use a custom 
FormType with the following configuration:. You may want to use your custom form class. Use the following configuration :

```yaml
# config/admin/resources/article.yaml
article:
    entity:
        actions:
            create:                                    
                form: My\Custom\FormType                
```

### Parameters

| Options  | Defaults | Notes |
| ------------- | ------------- | ------------- |
| title  | `lag.admin.article.create`  | This is the title use in the view (html title and h1 title)  |
| template | `null` | The twig template used to display the action view |
| form | `null` | The form class used to create the new entity  |
| icon | `null` | The icon display in the h1 title |
| fields | `[]` | An array of fields mapping. The key is the name of the field and the value is the field configuration. You can learn more about fields in the [Fields](field.md) section. Note that this mapping has no effect if a form class is provided|
| permissions | `ROLE_ADMIN` | The required role to granted for this action |
| route | `null` | The route used for this action |
| route_parameters | `[]` | The route parameters used for this action |
| path | `[]` | The route path used for this action |
| route_defaults | `[]` | The route defaults used for this action |
| icon | `null` | The icon display in the h1 title |
| load_strategy | `none` | This option should be to none in the create action, as no entity should be loaded from the database |
| class  | `LAG\AdminBundle\Admin\Action` | The action class which will be instantiated. In most of case; you do not need to change this option  |

 
## Update action

This action is used to display the update form of the configured entity in the parent `Admin`.
You can define the following options for the `update` actions :

```yaml
lag_admin:
    admins:
        article:
            entity:
            actions:
                create:
                    title: lag.admin.article.create
                    icon: null      
                    template: ~                    
                    form: null
                    fields: []
                    permissions: ROLE_ADMIN
                    route: ~
                    route_parameters: []
                    path: ~
                    route_requirements: []
                    route_defaults':
                        _controller: LAG\AdminBundle\Admin\Admin
                    class: LAG\AdminBundle\Admin\Action
                    load_strategy: none
```

> All parameters are optional; this is the default configuration 

### Use a custom form
By default, the form is built using the fields of the entity, and the primary key is not modifiable. You may want to
use your custom form class. Use the following configuration :

```yaml
# config/admin/resources/article.yaml
article:
    entity:
        actions:
            update:                                    
                form: My\Custom\FormType                
```

### Parameters

| Options  | Defaults | Notes |
| ------------- | ------------- | ------------- |
| title  | `lag.admin.article.create`  | This is the title use in the view (html title and h1 title)  |
| template | `null` | The twig template used to display the action view |
| form | `null` | The form class used to create the new entity  |
| icon | `null` | The icon display in the h1 title |
| fields | `[]` | An array of fields mapping. The key is the name of the field and the value is the field configuration. You can learn more about fields in the [Fields](field.md) section. Note that this mapping has no effect if a form class is provided|
| permissions | `ROLE_ADMIN` | The required role to granted for this action |
| route | `null` | The route used for this action |
| route_parameters | `[]` | The route parameters used for this action |
| path | `[]` | The route path used for this action |
| route_defaults | `[]` | The route defaults used for this action |
| icon | `null` | The icon display in the h1 title |
| load_strategy | `none` | This option should be to none in the create action, as no entity should be loaded from the database |
| class  | `LAG\AdminBundle\Admin\Action` | The action class which will be instantiated. In most of case; you do not need to change this option  |

## List Action

The `list` action is used to display a list of entity. This list can be paginated.

### Parameters

| Options  | Defaults | Notes |
| ------------- | ------------- | ------------- |
| title  | `lag.admin.article.create`  | This is the title use in the view (html title and h1 title)  |
| template | `null` | The twig template used to display the action view |
| form | `null` | The form class used to create the new entity  |
| icon | `null` | The icon display in the h1 title |
| fields | `[]` | An array of fields mapping. The key is the name of the field and the value is the field configuration. You can learn more about fields in the [Fields](field.md) section. Note that this mapping has no effect if a form class is provided|
| permissions | `ROLE_ADMIN` | The required role to granted for this action |
| route | `null` | The route used for this action |
| route_parameters | `[]` | The route parameters used for this action |
| path | `[]` | The route path used for this action |
| route_defaults | `[]` | The route defaults used for this action |
| icon | `null` | The icon display in the h1 title |
| load_strategy | `none` | This option should be to none in the create action, as no entity should be loaded from the database |
| class  | `LAG\AdminBundle\Admin\Action` | The action class which will be instantiated. In most of case; you do not need to change this option  |

### Define list fields

You can define which fields will be displayed in the list. Use the following configuration :

```yaml
# config/admin/resources/article.yaml
article:
    entity:
        actions:
            list:                                    
                fields:
                    name: ~
                    title: ~ 
```

To learn the available fields types, see the 
[Fields](field.md) documentation.

### Use a custom template for a field rendering

The most common use cases in admins interfaces is to customize fields rendering. However, you may not want to customize all
fields, but just once. Use the following configuration :

```yaml
# config/admin/resources/article.yaml
article:
    entity:
        actions:
            list:                                    
                fields:
                    name:
                        template: my-custom-template.html.twig
                    title: ~ 
```

The value parameter represents the value of the field in the template. For instance, in a boolean field :

```twig
{# The value is true or false #}
{% if value %}
    <i class="fa fa-check text-success" title="{{ 'lag.admin.yes' | trans }}"></i>
{% else %}
    <i class="fa fa-times text-danger" title="{{ 'lag.admin.no' | trans }}"></i>
{% endif %}
```

### Use a custom template for the whole list action

You may also want to customize the whole list, not just a field. Use the following configuration :

```yaml
# config/admin/resources/article.yaml
article:
    entity:
        actions:
            list:                                    
                template: my-custom-list.html.twig
                fields:
                    name: ~
                    title: ~ 
```

You can extend the original list :

```twig
{# my-custom-list.html.twig #}
{% extends '@LAGAdmin/CRUD/list.html.twig' %}

{# Add your custom code or blocks #}
{# ... #}

```


## Under the hood
Actions are created when the `AdminEvents::HANDLE_REQUEST` event is dispatched, according to the configuration of the current
found admin. Each action defines fields and parameters to build a view. The action and admin name are defined in the
routing parameters (`_route` and `_admin` parameters).

The actions are created using `LAG\AdminBundle\Factory\ActionFactory` factory class.

When creating a form, the `Events::ADMIN_CREATE_FORM` event is dispatched, allowing you to override the form. The form
is created using the `LAG\AdminBundle\Factory\FormFactory` factory class.

When creating a view, the `Events::ADMIN_VIEW` event is dispatched, allowing you to override the view. The form
is created using the `LAG\AdminBundle\Factory\ViewFactory` factory class.

You can learn more about events in the 
[Events](events.md) documentation.

Previous: [Admin](admin.md)

Next: [Field](field.md)
