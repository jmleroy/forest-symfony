# INSTALL

## Composer

From the terminal, go to the root directory of your Symfony project, and type:

```
$ composer require forestadmin/forest-symfony
```

Temporarily, you will also have to add the following repository reference to composer.json:

```json
    "repositories": [
        {
            "url": "https://github.com/forestadmin/forest-symfony",
            "type": "git"
        },{
            "url": "https://github.com/jmleroy/forest-php",
            "type": "git"
        }
    ],
```

## AppKernel.php

In registerBundles(), add:

```php
            new ForestAdmin\ForestBundle\ForestBundle();
```

## app/config/routing.yml

Add:

```yaml
forestadmin_forest:
    resource: '@ForestBundle/Controller/'
    prefix:    /forest
    type:      annotation
```

## app/config/config.yml

Add:

```yaml
    forestadmin.forest.secret_key: "Your Secret Key"
```