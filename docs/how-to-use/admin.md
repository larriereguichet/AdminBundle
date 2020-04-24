# Admin
An `Admin` is an object responsible to load the required data for the current action. When an `Admin` url is called, the 
`Admin factory` create an Admin according the route configuration and the Admin configuration.

Then the `Admin` handle the request, dispatch some events and execute several actions to gather data to display the wanted
view.

## Resources path
The `AdminBundle` configuration works like the `Serializer` component : the configuration files are located in a special
directory (which can be changed using the `AdminBundle` configuration). By default, it will read all `.yaml` files in 
the **`config/admin/resources`** directory of your project. 

Most times, you do not require to change this directory. But you change it using the following configuration :

```yaml
# config/packages/lag_admin.yaml
lag_admin:
    application:
        resources_path: my-custom-directory
```

## Basic configuration
The basic configuration for an Admin is the following :

```yaml
# config/admin/resources/article.yaml
article:
    entity: App\Entity\Article 
    actions:
        create: ~
        edit: ~
        list: ~
        delete: ~
```

This will create CRUD views for the entity `App\Entity\Article` with the default configuration. This default 
configuration is useful for most needs. However, sometimes more customization is required. See the 
[Customization](../customization/index.md) 
documentation to learn more about it. 

While you can add several Admin in one file, it is recommended to use one file per Admin, for readability reasons.

```yaml
config
    |
    --- admin
        |
        --- resources
                     |
                     --- article.yaml       
                     --- category.yaml
                     --- comment.yaml
```

## Under the hood
Admins are created by the `LAG\AdminBundle\Factory\AdminFactory` when the controller 
`LAG\AdminBundle\Controller\AdminAction` is called by the routing.

## Full configuration
The full configuration reference for an Admin is available 
[here](../reference/admin-reference.md).

Now it's time to configure each actions.

Previous: [Basics](basics.md)

Next: [Actions](action.md)
