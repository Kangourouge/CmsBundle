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

    /**
     * @param array $nodes
     * @param string|null $key
     * @return array|null
     */
    public function build(array $nodes, string $key = null)
    {
        if (count($nodes) === 0) {
            return [];
        }

        if ($keyed = $this->handleKey($nodes, $key)) {
            return $keyed;
        }

        /* @var $menu MenuInterface */
        $menu = array_shift($nodes);

        $url = $menu['url'];
        if ($url === null) {
            try {
                $url = $this->router->generate($menu['route']['name'], $menu['route']['params']);
            } catch (\Exception $exception) {
                return $this->build($nodes);
            }
        }

        return array_merge([[
            'name'     => $menu['name'],
            'title'    => $menu['title'],
            'url'      => $url,
            'children' => $this->build($menu['__children'], $menu['key']),
        ],], $this->build($nodes));
    }


    /**
     * @param array $nodes
     * @param string|null $key
     * @return null
     */
    public function handleKey(array $nodes, string $key = null)
    {
        return null;
    }
}
