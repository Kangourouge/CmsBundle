<?php

namespace KRG\SeoBundle\Menu;

interface MenuBuilderInterface
{
    public function build(array $nodes, string $key = null);

    public function handleKey(array $nodes, string $key = null);
}
