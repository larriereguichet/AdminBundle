[![Build Status](https://travis-ci.org/larriereguichet/AdminBundle.svg?branch=master)](https://travis-ci.org/larriereguichet/AdminBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0/big.png)](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0)


# AdminBundle
AdminBundle allows you to create flexible and robust backoffice application, using a simple yml configuration,
for your Symfony application.

## Installation
```
composer require lag/adminbundle
```

## Features
Version 0.4 :

* Dynamic CRUD for your entities (no code generation)
* Simple configuration in yml (look alike symfony1 generators.yml syntax)
* List with pagination, sorting and batch remove (filters are coming)
* Full translated
* Main and left menu integration
* Fully customizable (use your own controllers, data providers or templates)
* Bootstrap integration (can be disabled or override)

## Configuration Reference
```yml
    lag_admin:
        application:            
            title: My Little TaunTaun application
            description: My Little TaunTaun application using Admin Bundle
            locale: en
            # Use the css framework Bootstrap integration (default: true) 
            bootstrap: true
            # Your base template (default: LAGAdminBundle::admin.layout.html.twig)
            base_template: 'MyLittleTaunTaunBundle::layout.html.twig'
            # Form block template
            block_template: 'MyLittleTaunTaunBundle:Form:fields.html.twig'
            # Admins routing configuration
            routing:
                name_pattern: 'tauntaun.{admin}.{action}'
                url_pattern: 'tauntaun/{admin}/{action}'
            # Use extra configuration helper (default: true)
            enable_extra_configuration: true
            # Global date format (can be override for each admin, or field)
            date_format: 'd/M/Y'
            # In list view, strings will be truncated after 200 characters and will be suffixed by ...
            string_length: 200
            string_length_truncate: '...'
            # Translation configuration
            translation:
                # Default: true
                enabled: true
                pattern: app.{key}
            # In list view, only 25 items per page
            max_per_page: 25
            fields_mapping:
                # You can override or create new field (it should be declared in services.yml, see the dedicated chapter)
                my_custom_field: MyLittleTaunTaunBundle\Fields\MyCustomField
                my_custom_string: MyLittleTaunTaunBundle\Fields\MyString
        admins:
            planet:
                # Generic action
                create: ~
                edit: ~
                delete: ~
                list:
                    fields:
                        id: ~
                        name:
                            type: link
                            options:
                                length: 40
                                # According to global routing pattern
                                route: tauntaun.planet.edit
                                parameters: {id: ~}
                        category: ~
                        galaxy: ~
                        publicationStatus: ~
                        publicationDate: {type: date, options: {format: d/m/Y}}
                        updatedAt: {type: date, options: {format: '\L\e d/m/Y à h:i:s'}}
                # Your Doctrine entity (required)
                entity: MyLittleTaunTaunBundle\Entity\Planet
                # Your Symfony form type (required; used in create and edit action)
                form: MyLittleTaunTaunBundle\Form\PlanetType
                actions:
                    # Custom actions
                    death_star:                        
                        title: Destroy a planet
                        fields:
                        permissions: [ROLE_DARK_SITH]
                        # Planets will be retrieved sorted by size and by population
                        order:
                            size: getSize
                            population: ~
                        route: app.planets.destroy
                        route_parameters: {id: ~}
                        icon: fa fa-planet
                        load_strategy: unique
                        # Allowed options are pagerfanta and false
                        pager: false
                        criteria: {id: ~}
                        menu:
                            top:
                                items:
                                    destroy_another:
                                        title: Destroy an other planet
                                        route: destroy.again
                # Used batch action in list view
                batch: true
                # Global routing override
                routing_url_pattern: custom/planet/{admin}/{action}
                routing_name_pattern: tauntaun.{admin}.{action}
                # Your custom controller (can extends CRUDController to ease Admin management)
                controller: MyLittleTaunTaunBundle:MyController
                max_per_page: 5
                # Should implements DataProviderInterface
                data_provider: 'my.custom.data_provider.service'
                # Translations pattern override
                translation_pattern: {key}
            # Short configuration reference            
            tauntaun:
                entity: MyLittleTaunTaunBundle\Entity\TaunTaun
                form: MyLittleTaunTaunBundle\Entity\TaunTaunType
                actions: ~
        
```




# app/config/config.yml
knp_menu:
    # use "twig: false" to disable the Twig extension and the TwigRenderer
    twig:
        template: 'LAGAdminBundle:Menu:bootstrap_menu.html.twig'
    #  if true, enables the helper for PHP templates
    templating: false
    # the renderer to use, list is also available by default
    default_renderer: twig


lag_admin:
    menus:
        main:
            items:
                map_menu:
                    url: '#'
                    text: Map
                    icon: fa fa-gamepad
                    items:
                        map:
                            admin: map
                            action: list
                        layer:
                            admin: layer
                            action: list
                        pencil_set:
                            admin: pencil_set
                            action: list
                fireman:
                    url: '#'
                    text: Fire 3242
                    icon: fa fa-fire-extinguisher
                    items:
                        fireman:
                            admin: fireman
                            action: list
                logout:
                    route: logout
                    icon: fa fa-sign-out
                    text: Logout









#### Getting Started

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
    # LAG AdminBundle
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
            name_pattern: 'lag.admin.{admin}.{action}'
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

## Documentation

Full documentation can be found [here](https://github.com/larriereguichet/AdminBundle/tree/master/Resources/docs/summary.md)
