# AdminBundle

AdminBundle allows you to create flexible and robust management application, using a simple configuration,
for your Symfony application.

Development and Documentation are in progress


# Installation
```
php composer.phar require bluebear/adminbundle
```

# Getting started

## Configuring your application

AdminBundle configuration is made in config.yml file. However, if you require a lot of configuration, it is maybe better
to store it in a separate file.

__1 : create app/config/admins.yml file__

__2 : add your admins.yml file into app/config/config.yml__
```yml

    # config.yml
    imports:               
        - { resource: admins.yml }
        ...
```

__3 : configure your application parameters__
```yml

    # config.yml
    blue_bear_admin:
        application:            
            title: MyLittleTauntaun
            description: My little tauntaun is so nice !!!                       
```

__4 : Configuring your CRUD views__
```yml
    blue_bear_admin:
        admins:
            my_entity:
                entity: MyNamespace\To\MyEntity
                form: my_form_type
```


