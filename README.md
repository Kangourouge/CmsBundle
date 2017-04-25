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

Twig
----

```
<html>
<head>
    ...
    {{ seoHead() }}
    ...
</head>
...
```

Améliorations possibles
-----------------------

- Vider le cache après la mise à jour d'une URL SEO
- SeoAdmin : JS - copier d'un clic les variables dans le presse papier
- SeoAdmin : empêcher l'utilisation de variable dans l'url si le paramètre est déjà renseigné
