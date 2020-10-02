# CMSBundle

abandoned!

## Setup

### Installation

#### Step 1: Download the bundle

```sh
$ composer require kangourouge/cms-bundle
```

#### Step 2: Enable the bundle and install assets
```php
<?php

public function registerBundles()
{
    $bundles = array(
        new KRG\CmsBundle\KRGCmsBundle(),
        new KRG\IntlBundle\KRGIntlBundle(),
        new KRG\DoctrineExtensionBundle\KRGDoctrineExtensionBundle(),
        new KRG\EasyAdminExtensionBundle\KRGEasyAdminExtensionBundle(),
    );
}
```

```sh
$ bin/console assets:install
```

#### Step 3: Extend entities

- class Seo extends \KRG\CmsBundle\Entity\Seo;
- class Page extends \KRG\CmsBundle\Entity\Page;
- class Menu extends \KRG\CmsBundle\Entity\Menu;
- class Block extends \KRG\CmsBundle\Entity\Block;
- class Filter extends \KRG\CmsBundle\Entity\Filter;

#### Step 4: Configuration

```yaml
# app/config/config.yml

framework:
    serializer: { enable_annotations: true }
    router:
        type: 'krg.routing.loader'
        
doctrine:
    orm:
        mappings:
            translatable:
                type: annotation
                alias: Gedmo
                prefix: Gedmo\Translatable\Entity
                dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity"
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
```

```yaml
# app/config/routing.yml

krg_user:
    resource: "@KRGUserBundle/Controller/"
    type:     annotation
    prefix:   /

krg_easyadmin_bundle:
    resource: "@KRGEasyAdminExtensionBundle/Controller/"
    type:     annotation
    prefix:   /admin

krg_doctrine_bundle:
    resource: "@KRGDoctrineExtensionBundle/Controller/"
    type:     annotation

krg_cms:
    resource: "@KRGCmsBundle/Controller/"
    type:     annotation
```

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

## Usage

### Twig functions

#### Title & metas: seo_head()

```twig
<html>
<head>
    ...
    {{ seo_head() }}
    ...
</head>
```

#### Page link: seo_url()

```twig
{{ seo_url('cgu') }}
```

#### Blocks: krg_block()

```twig
{{ krg_block('example') }}
```

### Blocks usage

#### Filters

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

#### Files

```yaml
# app/config/config.yml

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
```html
<h2>Title</h2>
```

To be able to edit an invisible area from the admin, add class "cms-hidden-area" and the data attribute "data-parent-label" to your block source.
```html
<div class="cms-hidden-area" data-parent-label="Action button">
</div> 
```

## Override

### MenuBuilder

```yaml
services:
    KRG\CmsBundle\Menu\MenuBuilderInterface: '@AppBundle\Menu\MenuBuilder'
```


