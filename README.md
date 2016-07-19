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
$ composer require forestadmin/forest-symfony
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Allow CORS Queries from ForestAdmin
-------------------------------------------

To allow Forest to communicate successfully with your application, you will
need to authorize it to do Cross-Origin Resource Sharing (CORS) Queries.
If you do not know how it works, follow these instructions :

First, install a CORS bundle, fore example NelmioCorsBundle :

```
$ composer require nelmio/cors-bundle
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

Step 3: Enable the Bundle
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

Step 4: Configure Routes
------------------------

Add the Forest controllers to your routes by appending the following lines
to `app/config/routing.yml`:

```yaml
forestadmin_forest:
    resource: '@ForestBundle/Controller/'
    type:      annotation
```

Step 5: Configure Secret and Auth Key
-------------------------------------

Generate a secret key for your application on http://forestadmin.com, 
then edit `app/config/config.yml`:

```yaml
forest:
    secret_key: "Your Secret Key"
```

**Important notice**: Your secret key depends on your environment by
putting it in `app/config/config_(env).yml`. However, you need to set up
your environment appropriately.
If you wish to use Forest with the dev environment of your application,
you need to make changes in your `web/.htaccess` file and replace all
instances of `app.php` by `app_dev.php`. Actually, Forest does not accept
in its configuration to specify a path after your server domain name and
port.

Also, add your pass phrase to the Forest config :

```yaml
forest:
    secret_key: "Your Secret Key"
    auth_key: "PassPhrase to use as your Authorization Key"
```

Step 6: Regenerate the cache
----------------------------

This can be easily done by running the following console command:

```
$ php app/console cache:clear
```

The cache warmup triggers the analysis of your database structure based
on the Doctrine metadata and sends the map that the Forest API will use.

Anytime, you can inform Forest of changes made in your database schema.
The following command will send the map of your database structure to
ForestAdmin. Every time you need to update the structure, you will need
to run this command again.

```
$ php app/console forest:postmap
```
