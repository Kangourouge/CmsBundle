<?php

namespace KRG\SeoBundle\Menu;

use KRG\SeoBundle\Entity\MenuInterface;
use Symfony\Component\Routing\RouterInterface;

class MenuBuilder implements MenuBuilderInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * MenuBuilder constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function build(array $nodes)
    {
        if (count($nodes) === 0) {
            return [];
        }

        /* @var $menu MenuInterface */
        $menu = array_shift($nodes);

        $url = $menu['url'];

        if ($url === null) {
            try {
                $url = $this->router->generate($menu['route']['name'], $menu['route']['params']);
            } catch(\Exception $exception) {
                return $this->build($nodes);
            }
        }

        return array_merge([[
            'name'     => $menu['name'],
            'title'    => $menu['title'],
            'url'      => $url,
            'children' => $this->build($menu['__children']),
        ]], $this->build($nodes));
    }
}
