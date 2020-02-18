[![Build Status](https://travis-ci.org/larriereguichet/AdminBundle.svg?branch=master)](https://travis-ci.org/larriereguichet/AdminBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0/mini.png)](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0)
[![Latest Stable Version](https://poser.pugx.org/lag/adminbundle/v/stable)](https://packagist.org/packages/lag/adminbundle)
[![Total Downloads](https://poser.pugx.org/lag/adminbundle/downloads)](https://packagist.org/packages/lag/adminbundle)

# AdminBundle
The AdminBundle let you creates a **flexible** and **robust backoffice** on any Symfony application, with simple `yaml` configuration.

It eases the handling of CRUD views by configuring `Admin` objects on top of your Doctrine entities. Each `Admin` has one or many `Action`.
By default, the four actions of a classical CRUD are available (`create`, `edit`, `delete` and `list`) and the creation of custom actions is easy.

If you require more flexibility, you can easily override any part of the process (data providers, controllers, views...).
The purpose of the bundle is to provide an Admin interface with default configuration, and allows the user to add his
specific need where he wants, and allow to implements any specific needs without any hassles.

## Features
* Dynamic CRUD for your entities (no code generation)
* Simple yaml configuration
* List with pagination, sorting and filters
* Menus integrations
* Fully customizable (use your own controllers, data providers or templates)
* Bootstrap 4 integration (can be disabled or override)

## Installation
### Step 1: Install the Bundle
Open a command console, execute the
following command in your project directory to install the latest stable version of the bundle:

```console
$ composer require lag/adminbundle
```

> If you does not use flex, read the extra steps to install the bundle [here](https://github.com/larriereguichet/AdminBundle/tree/master/docs/install/install-without-flex.md) 


### Step 2: Configure the routing
Import the routing configuration to have the admin generated routes :

```yml
    # config/routes.yaml        
    lag_admin:
        resource: .
        type: extra
```

### Step 4 : Configure an entity
```yml
    # config/packages/lag_admin.yaml

    lag_admin:
        application:
            title: My Little TaunTaun application
        admins:
            planet:
                entity: UniverseBundle\Entity\Planet
                form: UniverseBundle\Form\Type\PlanetType            
```

And now you could go to `http://127.0.0.1:8000/admin/planet/list` to see a list of your entities. 

Yan can go to `http://127.0.0.1:8000/app_dev.php/admin/` to see the homepage of your admin interface

## Documentation
1. [How to use](https://github.com/larriereguichet/AdminBundle/tree/master/src/Resources/docs/1.how-to-use.md)    
    1. [How To Use](https://github.com/larriereguichet/AdminBundle/tree/master/src/Resources/docs/1.how-to-use.md#how-to-use)
    2. [Admins](https://github.com/larriereguichet/AdminBundle/tree/master/src/Resources/docs/1.how-to-use.md#admin)
    3. [Actions](https://github.com/larriereguichet/AdminBundle/tree/master/src/Resources/docs/1.how-to-use.md#actions)
    4. Fields
    4. Events
    5. Data Providers
    6. Filters
    7. Views
2. Customization
  a. Custom actions
  b. Custom rendering
  c. Custom data
3. Reference
  a. Application configuration
  b. Admin configuration
4. FAQ
5. [Configuration reference](https://github.com/larriereguichet/AdminBundle/tree/master/src/Resources/docs/5.configuration-reference.md)


## Road map

### v1.1
- Add dynamic id column (instead of required "id" column) to improve generic and handle multiple ids columns 

### v1.0
- Add more testing

## History
Version 0.4 :
* Dynamic CRUD for your entities (no code generation)
* Simple configuration in yml (look alike symfony1 generators.yml syntax)
* List with pagination, sorting and batch remove (filters are coming)
* Full translated
* Main and left menu integration
* Fully customizable (use your own controllers, data providers or templates)
* Bootstrap 3 integration (can be disabled or override)
