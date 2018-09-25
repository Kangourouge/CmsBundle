<?php

namespace KRG\CmsBundle\Menu;

use KRG\CmsBundle\Entity\MenuInterface;

interface MenuBuilderInterface
{
    public function build(MenuInterface $menu, string $locale);
}
