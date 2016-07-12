Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require forestadmin/forest-symfony "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

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

            new ForestAdmin\ForestBundle\ForestBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Configure Routes
------------------------

Add the route prefix to your routes by editing `app/config/routing.yml`:

```yaml
forestadmin_forest:
    resource: '@ForestBundle/Controller/'
    prefix:    /forest
    type:      annotation
```

Step 4: Configure Secret Key
----------------------------

Generate a secret key for your application on http://forestadmin.com, then edit `app/config/config.yml`:

```yaml
    forestadmin.forest.secret_key: "Your Secret Key"
```

Step 5: Regenerate the cache and initialize Forest
--------------------------------------------------

This can be easily done by running the following console command:

```
$ php app/console cache:clear
```

By reinitializing the cache, you'll warmup the cache by analyzing your database structure based on the Doctrine metadata, and post the resulting map to Forest.
