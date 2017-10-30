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
            KRG\SeoBundle\Entity\PageInterface: AppBundle\Entity\Page
            KRG\SeoBundle\Entity\MenuInterface: AppBundle\Entity\Menu
            KRG\SeoBundle\Entity\BlockInterface: AppBundle\Entity\Block
            KRG\SeoBundle\Entity\BlockFormInterface: AppBundle\Entity\BlockForm
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

Form tags
---------

In order to be able to create a custom form, you need to tag your form:

```yaml
services:
    AppBundle\Form\ExampleType:
        tags:
            - { name: 'form.type', alias: 'form_alias' }
            - { name: 'seo.form', handler: 'AppBundle\Form\Handler\TestHandler', template: '@App/Form/test.html.twig', alias: 'Form test' }
```

```php
<?php

namespace AppBundle\Form\Handler;

class TestHandler implements FormHandlerInterface
{
    public function handle(Request $request, FormInterface $form)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            return $form->handleRequest($request);
        }

        return null;
    }
}

```

Entity
------

Create 5 entities:

class Seo extends \KRG\SeoBundle\Entity\Seo;
class Page extends \KRG\SeoBundle\Entity\Page;
class Menu extends \KRG\SeoBundle\Entity\Menu;
class Block extends \KRG\SeoBundle\Entity\Block;
class BlockForm extends \KRG\SeoBundle\Entity\BlockForm;

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
