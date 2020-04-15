### Installation without flex

```
    If you use Symfony Flex, you can skip this step.
    
    AdminBundle rely on WhiteOctoberPagerfantaBundle to handle list pagination. If you
    want to use those features, both bundles should be enabled in addition to AdminBundle.    
```

Without flex, you have to enable the Bundle after the installation 

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
