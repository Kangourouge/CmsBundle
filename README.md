# SeoBundle

AppKernel
---------

```
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

```
# app/config/config.yml
framework:
    # ...
    serializer: { enable_annotations: true }

...

krg_seo:
    seo_class: AppBundle\Entity\Seo
    
```

Routing
-------

```
krg_seo_route_loader:
    resource: .
    type: seo
```

Entity
------

```
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

```

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

```
<html>
<head>
    ...
    {{ seo_head() }}
    ...
</head>
...
```

Améliorations possibles
-----------------------

- SeoAdmin : JS - copier d'un clic les variables dans le presse papier
- SeoAdmin : empêcher l'utilisation de variable dans l'url si le paramètre est déjà renseigné
