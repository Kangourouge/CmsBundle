# CMSBundle

Dependencies:

https://github.com/Kangourouge/EasyAdminExtensionBundle
https://github.com/Kangourouge/DoctrineExtensionBundle

AppKernel
---------

```php
<?php

public function registerBundles()
{
    $bundles = array(
        new KRG\CmsBundle\KRGCmsBundle(),
        new KRG\EasyAdminExtensionBundle\KRGEasyAdminExtensionBundle(),
        new KRG\DoctrineExtensionBundle\KRGDoctrineExtensionBundle(),
    );
}
```


Configuration
-------------

Create 5 entities:

class Seo extends \KRG\CmsBundle\Entity\Seo;
class Page extends \KRG\CmsBundle\Entity\Page;
class Menu extends \KRG\CmsBundle\Entity\Menu;
class Block extends \KRG\CmsBundle\Entity\Block;
class Filter extends \KRG\CmsBundle\Entity\Filter;


```yaml
# app/config/config.yml

framework:
    # ...
    serializer: { enable_annotations: true }

...

doctrine:
    orm:
        mappings:
            loggable:
                type: annotation
                prefix: Gedmo\Loggable\Entity
                dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Loggable/Entity"
                is_bundle: false
        resolve_target_entities:
            KRG\CmsBundle\Entity\SeoInterface: AppBundle\Entity\Seo
            KRG\CmsBundle\Entity\PageInterface: AppBundle\Entity\Page
            KRG\CmsBundle\Entity\MenuInterface: AppBundle\Entity\Menu
            KRG\CmsBundle\Entity\BlockInterface: AppBundle\Entity\Block
            KRG\CmsBundle\Entity\FilterInterface: AppBundle\Entity\Filter
            
...

twig:
    form_themes:
        - 'KRGCmsBundle:Form:route.html.twig'
        - 'KRGCmsBundle:Form:filter.html.twig'
        - 'KRGCmsBundle:Form:content.html.twig'
```

Routing
-------

```yaml
# app/config/routing.yml

krg_seo:
    resource: .
    type: seo
    
krg_cms:
    resource: "@KRGCmsBundle/Controller/"
    type:     annotation
    
krg_easyadmin_bundle:
    resource: "@KRGEasyAdminExtensionBundle/Controller/AdminController.php"
    type:     annotation
```


Admin
-----

EasyAdmin configuration:

```yaml
# app/config/admin.yml

imports:
    - { resource: '@KRGEasyAdminExtensionBundle/Resources/config/easyadmin.yml' }
    - { resource: '@KRGDoctrineExtensionBundle/Resources/config/easyadmin.yml' }    
    - { resource: '@KRGCmsBundle/Resources/config/easyadmin.yml.yml' }
    
easy_admin:
    design:
        css:
            - '/bundles/krgcms/easyadmin/style.css'
            - '/bundles/krgeasyadminextension/css/style.css'

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


Override service Menu Builder
-----------------------------

https://symfony.com/doc/current/service_container/autowiring.html#working-with-interfaces

```yaml
services:
    KRG\CmsBundle\Menu\MenuBuilderInterface: '@AppBundle\Menu\MenuBuilder'
```

Assets
------

bin/console assets:install

# Blocks
## Filters

Form tags
---------

In order to be able to create a custom form, you need to tag your form like this:

- handler (optional): usefull if you have special form processing. Extends KRG\CmsBundle\Form\Handler\AbstractFormHandler
- template (optional): twig rendered file
- alias (optional): displayed name in admin

```yaml
services:
    AppBundle\Form\ExampleType:
        tags:
            - { name: 'krg.cms.form', handler: 'AppBundle\Form\Handler\TestHandler', template: '@App/Form/test.html.twig', alias: 'Form test' }
```

## Files

```yaml
# config.yml

krg_cms:
    blocks_path:
        - '%kernel.project_dir%/app/config/cms/blocks/'
        - '%kernel.project_dir%/app/config/cms/h2.yml'
```

```yaml
h2:
    label: Title H2
    template: 'blocks/h2.html.twig'
    thumbnail: '/blocks/thumb_h2.png'
```

h2.html.twig
```twig 
    <h2>Title</h2>
```

To be able to edit an invisible area from the admin, add class "cms-hidden-area" and the data attribute "data-parent-label"
```
<div class="cms-hidden-area" data-parent-label="Action button">
</div> 
```
