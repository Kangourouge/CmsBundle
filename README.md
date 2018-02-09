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


Installation & configuration
----------------------------

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
        - 'KRGCmsBundle:Form:form.html.twig'
        - 'KRGCmsBundle:CKEditor:addblock_widget.html.twig'
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

Form tags
---------

In order to be able to create a custom form, you need to tag your form:

```yaml
services:
    AppBundle\Form\ExampleType:
        tags:
            - { name: 'form.type', alias: 'form_alias' }
            - { name: 'krg.cms.form', handler: 'AppBundle\Form\Handler\TestHandler', template: '@App/Form/test.html.twig', alias: 'Form test' }
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

Override service Menu Builder
-----------------------------

https://symfony.com/doc/current/service_container/autowiring.html#working-with-interfaces

```yaml
services:
    KRG\CmsBundle\Menu\MenuBuilderInterface: '@AppBundle\Menu\MenuBuilder'
```

CKEditor Plugin
---------------

assets:install

```yaml
ivory_ck_editor:
    default_config: "default"
    configs:
        default:
            filebrowserBrowseRoute: elfinder
            filebrowserBrowseRouteParameters: []
            toolbar:         "standard"
            extraPlugins:    "addblock"
            allowedContent:  true
    toolbars:
        items:
            standard.insert: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'AddBlock']
    plugins:
        addblock:
            path:     "/bundles/krgcms/ckeditor/plugins/addblock/"
            filename: "plugin.js"
```
