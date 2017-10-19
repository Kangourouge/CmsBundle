# SeoBundle

AppKernel
---------

```php
<?php

public function registerBundles()
{
    $bundles = array(
        // ...
        new KRG\SeoBundle\KRGSeoBundle()
        // ...
    );
}
```

Configuration
-------------

```yaml
# app/config/config.yml

framework:
    # ...
    serializer: { enable_annotations: true }

...

doctrine:
    orm:
        resolve_target_entities:
            KRG\SeoBundle\Entity\SeoInterface: AppBundle\Entity\Seo
            KRG\SeoBundle\Entity\SeoPageInterface: AppBundle\Entity\SeoPage
            
```

Routing
-------

```yaml
# app/config/routing.yml

krg_seo_route_loader:
    resource: .
    type: seo
    
seo:
    resource: "@KRGSeoBundle/Controller/"
    type:     annotation
```

Entity
------

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use KRG\SeoBundle\Entity\Seo as BaseSeo;

/**
 * @ORM\Entity
 * @ORM\Table(name="seo")
 */
class Seo extends BaseSeo
{
}
```

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use KRG\SeoBundle\Entity\SeoPage as BaseSeoPage;

/**
 * SeoPage
 *
 * @ORM\Entity
 * @ORM\Table(name="seo_page")
 */
class SeoPage extends BaseSeoPage
{
}
```

Twig
----

```twig
<html>
<head>
    ...
    {{ seo_head() }}
    ...
</head>
```

Récupérer l'url d'une SeoPage depuis sa key :
```twig
{{ seo_url('cgu') }}
```

Améliorations possibles
-----------------------
