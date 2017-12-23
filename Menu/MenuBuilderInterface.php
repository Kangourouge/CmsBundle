<?php

namespace KRG\SeoBundle\Menu;

use KRG\SeoBundle\Entity\MenuInterface;

interface MenuBuilderInterface
{
    public function build(MenuInterface $menu);
}
