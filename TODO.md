## AppKernel.php

In registerBundles(), add:

```php
            new ForestAdmin\ForestBundle\ForestAdminForestBundle();
```

## app/config/routing.yml

Add:

```yaml
forestadmin_forest:
    resource: '@ForestBundle/Controller/'
    prefix:    /forest
    type:      annotation
```

## app/config/parameters.yml

Add:

```yaml
    forestadmin.secret_key: "Your Secret Key"
```