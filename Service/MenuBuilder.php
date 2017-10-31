<?php

namespace KRG\SeoBundle\Service;

use KRG\SeoBundle\Entity\MenuInterface;

class MenuBuilder implements MenuBuilderInterface
{
    public function build(array $nodes)
    {
        if (count($nodes) === 0) {
            return [];
        }

        /* @var $menu MenuInterface */
        $menu = array_shift($nodes);

        return array_merge([
            'name'     => $menu['name'],
            'title'    => $menu['title'],
            'url'      => $menu['url'],
            'children' => $menu['__children'],
        ], $this->build($nodes));
    }
}
