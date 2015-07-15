# AdminBundle

AdminBundle allows you to create flexible and robust management application, using a simple configuration,
for your Symfony application.

Development and Documentation are in progress

## Features

Current version: 0.2

* Provides dynamics CRUD for your entities (not generated)
* Simple configuration in yml (look alike symfony1 generators.yml files)
* Basic permissions
* Built-in pager (using PagerFanta)
* List export in multiple formats : html, pdf, xls, csv, json (using EE/DataExporter)
* Fully customizable (use your own controllers, managers or templates)


# Installation
```
php composer.phar require bluebear/adminbundle
```

# Getting started

## Configuring your application

AdminBundle configuration is made in config.yml file. However, if you require a lot of configuration, it is maybe better
to store it in a separate file.

__1) Create app/config/admins.yml file__

To manage easily your admin configuration, you can put it in a separate configuration file (for example, app/config/admins.yml)
This is optional, you can write your configuration directly in your config.yml. If you choose to have a separate file, you have to
import it in your config.yml (@see [Configuration Organization](http://symfony.com/doc/current/cookbook/configuration/configuration_organization.html#advanced-techniques)).

__2) Add your admins.yml file into app/config/config.yml__

```yml
    # app/config/config.yml
    imports:               
        - { resource: admins.yml }
        ...
        
```

__3) Configure your application parameters__

AdminBundle comes with a built-in main layout to handle page parameters (like title...). You have to configure at least your
application title, which is the content title markup by default for all your pages.

```yml
    # config.yml
    blue_bear_admin:
        application:            
            title: MyLittleTauntaun Admin
            description: My little tauntaun is so nice !!!
                                   
```

__4) Configuring your CRUD views__

Now you have to configure the entities you want to have an admin. To achieve this, you have to add an entry in the admins
configuration.
"my_entity" is just a key. It has to be unique for each entities you have configured.
"MyBundle\Entity\MyEntity" is the namespace path to the entity
"my_form_type" is the form you want to be displayed on edition and creation for your entity. In future version, you will
be allow to set this parameter to null to let AdminBundle generates a form for you. For now you should expose your form
type as a service, and "my_form_type" is the service id. 

To expose form type as service, rendez-vous [here](http://symfony.com/doc/current/cookbook/form/create_custom_field_type.html#creating-your-field-type-as-a-service) 

```yml
    blue_bear_admin:
        admins:
            my_entity:
                entity: MyBundle\Entity\MyEntity
                form: my_form_type
                
```

__5) Import AdminBundle routing__
   
You have to import AdminBundle routing (or using yours) in your app/config/routing.yml file.

```yml
    # app/config/routing.yml
    # BlueBear AdminBundle
    blue_bear_admin:
        resource: .
        type: extra
        # optional prefix
        prefix: /admin
        
```

That's it. You should have admin application and you should be able to manage your entities.



__Configuration Reference__

```yml
blue_bear_admin:
    application:
        # application configuration
        title: MyLittleTaunTaun Admin
        description:  My little tauntaun is so nice !!!
        # main layout
        layout: MyBundle::layout.html.twig
        # form block fields template
        block_template: MyBundle:Form:fields.html.twig
        # number of entities per page before displaying a pager (for all admin by default)
        max_per_page: 25
        # use default bootstrap integration (add bootstrap css and js to the layout)
        bootstrap: true
        # routing options
        routing:
            # admin routing name pattern
            name_pattern: 'bluebear.admin.{admin}.{action}'
            # admin url pattern (admin is the unique key that you have configured in admins configuration section) 
            url_pattern: '{admin}/{action}'
    # list of managed admins
    admins:
        # entities (pencil is an example)
        tauntaun:
            # orm entity class
            entity: MyBundle\Entity\TaunTaun
            # associated form type
            form: tauntaun_type
            # if you want full customization, use your controller to handle actions
            controller: MyBundle:MyController
            # entity manager call for CRUD actions
            manager:
                # service id of your custom manager
                name: my_bundle.manager.tauntaun
                # only save action is supported right now. More are coming. myCustomSaveMethod should be a method of
                # your manager @my_bundle.manager.tauntaun
                save: myCustomSaveMethod
            # number of entities per page before displaying a pager (just for current admin)
            max_per_page: 25
            # available actions (for current admin). Actions should always be list, create, edit and delete. More
            # customizations will be available in future versions
            actions:
                list:
                    # title of the list page
                    title: Pencils list
                    # roles allowed to the list action
                    permissions: [ROLE_ADMIN, ROLE_USER]
                    # available export for list action
                    export: [html, pdf, xls, csv, json]
                    # this fields will be displayed in the list table
                    fields:
                        # your entity should have those fields accessible (public property, getters...)
                        # for example, getId(). @see Symfony\Component\PropertyAccess\PropertyAccessor
                        id: ~                        
                        name: ~
                        label: ~
                        # displayed only 50 first characters in list action for this field (more options are coming to
                        # customize field display
                        description: {length: 50}
                        # in this example, riders is related object to current entity (via ManyToOne, a rider can have
                        # many tauntaun in this case, your entity rider should have an accessible property label) 
                        rider: ~
                # default configuration for those actions
                create: ~
                edit: ~
                delete: ~
        # short configuration for this entity
        rider:
            entity: MyOtherBundle\ORM\Rider
            form: rider_type

```


