## AppKernel.php

in registerBundles(), add :

```php
            new ForestAdmin\ForestBundle\ForestAdminForestBundle();
```

in app/routing.yml, add :

```yaml
forestadmin_forest:
    resource: '@ForestBundle/Controller'
    prefix:    /forest
```