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
        if ($keyed = $this->handleKey($nodes, $key)) {
            return $keyed;
        }

        if (count($nodes) === 0) {
            return [];
        }

        /* @var $menu MenuInterface */
        $menu = array_shift($nodes);

        $url = $menu->getUrl();
        if ($url === null) {
            try {
                $url = $this->router->generate($menu->getRouteName(), $menu->getRouteParams());
            } catch (\Exception $exception) {
                return $this->build($nodes);
            }
        }

        return array_merge([[
            'name'     => $menu->getName(),
            'title'    => $menu->getTitle(),
            'url'      => $url,
            'children' => $this->build($menu->getChildren()->toArray(), $menu->getKey()),
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
