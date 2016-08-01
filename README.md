[![Build Status](https://travis-ci.org/larriereguichet/AdminBundle.svg?branch=master)](https://travis-ci.org/larriereguichet/AdminBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0/mini.png)](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0)


# AdminBundle

The AdminBundle let you creates a flexible and robust backoffice on any Symfony application, with simple yml configuration.

Ease the handling of CRUD views by configuring Admin objects on top of your Doctrine entities. Each Admin has one or many Action.
By default, the four actions of a classical CRUD are available (create, edit, delete and list) and the creation of new actions is painless.

If you require more flexibility, you can easily override any part of the process (repositories, controllers, views...).
The purpose of the bundle is to provide an Admin interface with default configuration, and allows the user to add his
specific need where he wants, and allow to implements any specific needs without any hassles.


## Features

Version 0.4 :
* Dynamic CRUD for your entities (no code generation)
* Simple configuration in yml (look alike symfony1 generators.yml syntax)
* List with pagination, sorting and batch remove (filters are coming)
* Full translated
* Main and left menu integration
* Fully customizable (use your own controllers, data providers or templates)
* Bootstrap integration (can be disabled or override)

## Installation
```
    composer require lag/adminbundle
```

AdminBundle rely on KnpMenuBundle to handle menus and on WhiteOctoberPagerfantaBundle to handle list pagination. If you
want to use those features, both bundles should be enabled in addition to AdminBundle.

```php

    class AppKernel extends Kernel {
    ...

    new LAG\AdminBundle\LAGAdminBundle(),
    new Knp\Bundle\MenuBundle\KnpMenuBundle(),
    new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
    
    ...
    
```

## Configuration

As the configuration can be huge depending on your needs, we recommend to put the AdminBundle's configuration in a 
separate file.

```yml
    app/config/config.yml    
        
    imports:       
        - { resource: admin.yml }
        
```

```yml
    app/config/admin.yml
        
    lag_admin:
        application:
            title: My Little TaunTaun application
    knp_menu:        
        twig:
            template: 'LAGAdminBundle:Menu:bootstrap_menu.html.twig'
        
```


## Admin configuration

An admin is based on an Doctrine entity and a Symfony form class (for create and edit actions). Both should be provided 
to enable an Admin.
 
```yml
    app/config/admin.yml
   
    lag_admin:
        admins:
            planet:
                entity: UniverseBundle\Entity\Planet
                form: UniverseBundle\Form\Type\PlanetType
   
   
```

AdminBundle use a data provider to retrieve and save entities. If you do not provide one, the default one will be used.
It assumes that you have Doctrine repository implementing the LAG\AdminBundle\Repository\RepositoryInterface. It will 
add the save and delete method to your repository.

Fortunately, the AdminBundle provide the LAG\AdminBundle\Repository\DoctrineRepository abstract repository class which
implements those methods for you. All you have to do is to extend this class with your repository

```php

    namespace UniverseBundle\Repository;

    use LAG\AdminBundle\Repository\DoctrineRepository;

    class PlanetRepository extends DoctrineRepository {
    
    ...

```

Your Admin is now ready, but as new routes will be generated, you should clear the Symfony's cache. 



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
