About
=====

The ForestBundle allows you to use the ForestAdmin application to manage your database entities. 
If you don't know what ForestAdmin is, you can [follow this link](http://www.forestadmin.com)

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
    type:      annotation
```

Step 4: Configure Secret Key
----------------------------

Generate a secret key for your application on http://forestadmin.com, 
then edit `app/config/config.yml`:

```yaml
forest:
    secret_key: "Your Secret Key"
```

Step 5: Allow CORS Queries from ForestAdmin
-------------------------------------------

To allow Forest to test your installation successfully, you'll also need 
to authorize it to do a Cross-Origin Resource Sharing (CORS) Query.
If you don't know how it works, follow these instructions :

First, install a CORS bundle, fore example NelmioCorsBundle :

```
$ composer install nelmio/cors-bundle
```

Then, edit your `app/config/config.yml` by adding the following lines:

```yaml
nelmio_cors:
    paths:
        '^/forest':
            allow_origin: ["http://app.forestadmin.com", "https://app.forestadmin.com"]
            allow_headers: ["*"]
            allow_methods: ['POST', 'PUT', 'GET', 'DELETE']
```

Finally, add your pass phrase to the Forest config :

```yaml
forest:
    secret_key: "Your Secret Key"
    auth_key: "PassPhrase to use as your Authorization Key"
```

(WiP)


Step 6: Regenerate the cache
----------------------------

This can be easily done by running the following console command:

```
$ php app/console cache:clear
```

The cache warmup triggers the analysis of your database structure based
on the Doctrine metadata and sends the map that the Forest API will use.

Anytime, you can inform Forest of changes made in your database schema.
The following command will transmit the map of your database structure 
to ForestAdmin. Every time you need to update the structure,
you will need to run this command again.

```
$ php app/console forest:postmap
```

(WiP: should be triggered every time you do a doctrine:schema:update)