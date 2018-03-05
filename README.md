# SeoBundle

AppKernel
---------

```php
<?php

public function registerBundles()
{
    $bundles = array(
        new KRG\CmsBundle\KRGCmsBundle()
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
        - 'KRGCmsBundle:Form:content_tools.html.twig'
```

Routing
-------

```yaml
# app/config/routing.yml

krg_seo_route_loader:
    resource: .
    type: seo
    
seo:
    resource: "@KRGCmsBundle/Controller/"
    type:     annotation
```


Admin
-----

EasyAdmin configuration:

```yaml
# app/config/admin.yml

parameters:
    krg_cms.seo.class: AppBundle\Entity\Seo
    krg_cms.page.class: AppBundle\Entity\Page
    krg_cms.menu.class: AppBundle\Entity\Menu
    krg_cms.block.class: AppBundle\Entity\Block
    krg_cms.filter.class: AppBundle\Entity\Filter

imports:
    - { resource: '@KRGCmsBundle/Resources/config/easyadmin/*.yml' }
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

CKEditor Plugin
---------------

bin/console assets:install
bin/console ckeditor:install  

```yaml
ivory_ck_editor:
    default_config: "default"
    configs:
        default:
            filebrowserBrowseRoute: elfinder
            filebrowserBrowseRouteParameters: []
            toolbar:         "standard"
            allowedContent:  true
    toolbars:
        items:
            standard.insert: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'AddBlock']
```

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

```yaml
# config.yml

krg_cms:
    blocks:
        - '%kernel.project_dir%/app/config/cms/blocks/block1.yml'
```

```yaml
# block1.yml

block1:
    template: 'KRGCmsBundle:Sample:block1.html.twig'
    fields:
        - { property: 'title', type: 'text' }
        - { property: 'textarea', type: 'textarea' }
```
