[![Latest Stable Version](https://poser.pugx.org/lag/adminbundle/v/stable)](https://packagist.org/packages/lag/adminbundle)
[![Build Status](https://travis-ci.org/larriereguichet/AdminBundle.svg?branch=master)](https://travis-ci.org/larriereguichet/AdminBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/larriereguichet/AdminBundle/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0/mini.png)](https://insight.sensiolabs.com/projects/c8e28654-44c7-46f3-9450-497e37bda3d0)
[![Total Downloads](https://poser.pugx.org/lag/adminbundle/downloads)](https://packagist.org/packages/lag/adminbundle)

# AdminBundle
The AdminBundle helps you to create **flexible** and **robust** administration application.

It provides PHP attributes or yaml configuration to build views to show, create; update and delete resources. It comes
with a native Doctrine ORM integration, and can also be used with any architecture.

The AdminBundle is highly customizable using configuration or with events to allow you tu build dynamic administration 
interfaces.

## Features
* Easy to use PHP attributes or yaml configuration
* Highly customizable
* Doctrine ORM integration
* Views with pagination, sorting and filtering
* Dynamic menus
* Bootstrap 5 integration

## Install the Bundle

```bash
composer require lag/adminbundle
```

If you do not use Symfony Flex, follow [those extra steps](docs/install/install-without-flex.md).













### Step 1: Install the Bundle
Open a command console, execute the
following command in your project directory to install the latest stable version of the bundle:

```bash
composer require lag/adminbundle
```

> If you do not use flex, read the [extra steps to install the bundle](docs/install/install-without-flex.md) 


### Step 2: Configure the routing
Import the routing configuration to have the admin generated routes :

```yml
    # config/routes.yaml        
    lag_admin:
        resource: '@LAGAdminBundle/Resources/config/routing/routing.yaml'
        prefix: /admin
```

### Step 3 : Configure an entity

```yaml
# config/packages/lag_admin.yaml
lag_admin:
    application:
        title: My Little TaunTaun application       
```

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

> As new routes are dynamically created, the cache clearing is required (`symfony cache:clear`)

Now you could visit `http://127.0.0.1:8000/admin/article/list` to see a list of your entities. 

Yan can visit `http://127.0.0.1:8000/app_dev.php/admin/` to see the homepage of your admin interface

## Documentation
1. [How to use](docs/how-to-use/basics.md)   
    * a. [Basics](docs/how-to-use/basics.md)
    * b. [Admins](docs/how-to-use/admin.md)
    * c. [Actions](docs/how-to-use/action.md)
    * d. [Fields](docs/how-to-use/field.md)
    * e. [Events](docs/how-to-use/events.md)
    * f. Data Providers
    * g. Filters
    * h. Views
    * i. Security    
2. Customization
    * a. Custom actions
    * b. Custom render
    * c. Custom data
    * d. Custom routes and urls
3. Reference
    * a. Application configuration
    * b. Admin configuration
4. FAQ
5. [Configuration reference](docs/reference/index.md)


## Testing
To run the admin test suite, run the following :
```shell
make tests
```

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
