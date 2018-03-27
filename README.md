[![Build Status](https://travis-ci.org/larriereguichet/AdminBundle.svg?branch=master)](https://travis-ci.org/larriereguichet/AdminBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/build-status/master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0/mini.png)](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0)


# AdminBundle

The AdminBundle let you creates a **flexible** and **robust backoffice** on any Symfony application, with simple `yml` configuration.

It eases the handling of CRUD views by configuring `Admin` objects on top of your Doctrine entities. Each `Admin` has one or many `Action`.
By default, the four actions of a classical CRUD are available (`create`, `edit`, `delete` and `list`) and the creation of new actions is easy.

If you require *more flexibility*, you can easily override any part of the process (data providers, controllers, views...).
The purpose of the bundle is to provide an Admin interface with default configuration, and allows the user to add his
specific need where he wants, and allow to implements any specific needs without any hassles.

Current version **v1.0**

## Features

Version 0.4 :
* Dynamic CRUD for your entities (no code generation)
* Simple configuration in yml (look alike symfony1 generators.yml syntax)
* List with pagination, sorting and batch remove (filters are coming)
* Full translated
* Main and left menu integration
* Fully customizable (use your own controllers, data providers or templates)
* Bootstrap 3 integration (can be disabled or override)


## Getting Started

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require lag/adminbundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new LAG\AdminBundle\LAGAdminBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
        );

        // ...
    }
    // ...
}
```

If you use Symfony 4, you can skip this step.

AdminBundle rely on KnpMenuBundle to handle menus and on WhiteOctoberPagerfantaBundle to handle list pagination. If you
want to use those features, both bundles should be enabled in addition to AdminBundle.


### Step 3 : Configuring an entity


```yml
    #app/config/config.yml

    lag_admin:
        application:
            title: My Little TaunTaun application
        admins:
            planet:
                entity: UniverseBundle\Entity\Planet
                form: UniverseBundle\Form\Type\PlanetType
            
```

And now you could go to `http://127.0.0.1:8000/app_dev.php/admin/planet/list` to see a list of your entities. Yan can go
to `http://127.0.0.1:8000/app_dev.php/admin/` the see an homepage of your admin interface

## Going further

1. Main concept
  a. Admins and Actions
  b. Events
  c. Data Providers
  d. Filters
  e. Views

2. Customization

3. Reference
  a. Application configuration
  b. Admin configuration

