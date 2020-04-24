# Basics

The Admin Bundle is an event based library. When an  admin route is called, an Admin objects based on the yaml defined 
configuration is build. The Admin object is responsible to dispatch events to handle forms, entities, persistence...

## How To Use
First, you need to create entities. The easiest way is to use Doctrine ORM entities. If you do not know how to use
Doctrine to create entities, read the [Symfony documentation](https://symfony.com/doc/current/doctrine.html). 

For the example, we  use a simple Article entity.
```php

namespace App\Entity;

class Article {
    private $id;
    private $title;
    private $content;
    private $date;
    private $isPublished;
    
    // Getters and setters...
}
```

To have a CRUD to manage our articles, we have to configure an admin.

Next: [Admin](admin.md)
