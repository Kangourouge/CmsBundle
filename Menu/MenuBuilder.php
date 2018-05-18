<?php

namespace KRG\CmsBundle\Menu;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use KRG\CmsBundle\Annotation\Menu as Annotation;
use KRG\CmsBundle\Entity\MenuInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

class MenuBuilder implements MenuBuilderInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Request */
    protected $request;

    /** @var RouterInterface */
    protected $router;

    /** @var AnnotationReader */
    protected $annotationReader;

    /** @var Annotation */
    protected $annotation;

    /** @var FilesystemAdapter */
    private $filesystemAdapter;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, RouterInterface $router, AnnotationReader $annotationReader, string $dataCacheDir)
    {
        $this->entityManager = $entityManager;
        $this->request = $requestStack->getMasterRequest();
        $this->router = $router;
        $this->annotationReader = $annotationReader;
        $this->annotation = $this->getAnnotation();
        $this->filesystemAdapter = new FilesystemAdapter('menu', 0, $dataCacheDir);
    }

    public function getNodeTree($key)
    {
        $item = $this->filesystemAdapter->getItem(sprintf('%s_%s', $this->request->getLocale(), $key));
        if ($item->isHit()) {
            return $item->get();
        }

        /* @var $repository NestedTreeRepository */
        $repository = $this->entityManager->getRepository(MenuInterface::class);

        /* Get root nodes ordered by position */
        $menu = $repository->findOneByKey($key);
        if ($menu === null) {
            return [];
        }

        /* Build nodes hierarchy */
        $nodes = $this->build($menu);
        $item->set($nodes);
        $this->filesystemAdapter->save($item);

        return $nodes;
    }

    public function getNodes($key)
    {
        $nodes = $this->getNodeTree($key);
        $this->activeNodes($nodes);

        return $nodes;
    }

    private function getAnnotation()
    {
        if ($this->request === null || !$this->request->get('_controller')) {
            return null;
        }

        try {
            $annotation = $this->annotationReader->getMethodAnnotation(new \ReflectionMethod($this->request->get('_controller')),
                Annotation::class);
            if ($annotation) {
                $propertyAccessor = PropertyAccess::createPropertyAccessor();
                $attributes = $this->request->attributes->all();
                $params = $annotation->getParams();
                foreach ($params as $key => &$value) {
                    $value = $this->populate($propertyAccessor, $attributes, $value);
                }
                unset($value);

                $annotation->setParams($params);
                $annotation->setName($this->populate($propertyAccessor, $attributes, $annotation->getName()));

                return $annotation;
            }
        } catch (\ReflectionException $exception) {
        }

        return null;
    }

    public function getActiveNodes($key)
    {
        $nodes = $this->getNodeTree($key);
        $activeNodes = $this->activeNodes($nodes);

        if ($this->annotation) {
            array_push($activeNodes,
                [
                    'name'     => $this->annotation->getName(),
                    'title'    => $this->annotation->getName(),
                    'url'      => null,
                    'route'    => null,
                    'children' => [],
                    'roles'    => [],
                    'active'   => true,
                ]);
        }

        return $activeNodes;
    }

    private function activeNodes(array &$nodes)
    {
        if (($key = key($nodes)) === null) {
            return [];
        }

        $nodes[$key]['active'] = false;
        next($nodes);

        $children = [];
        if ((isset($nodes[$key]['children']) && $children = $this->activeNodes($nodes[$key]['children'])) || $this->isActive($nodes[$key])) {
            $nodes[$key]['active'] = true;

            return array_merge([$nodes[$key]], $children);
        }

        return $this->activeNodes($nodes);
    }

    public function isActive(array $node)
    {
        if ($this->request === null || ($node['url'] === null && count($node['route']) === 0)) {
            return false;
        }

        $nodeRoute = $node['route'];
        $requestRoute = [
            'name'   => $this->annotation ? $this->annotation->getRoute() : $this->request->get('_route'),
            'params' => $this->annotation ? $this->annotation->getParams() : $this->request->get('_route_params'),
        ];

        if ($requestRoute['name'] === 'krg_page_show' && ($_seo = $this->request->get('_seo')) instanceof SeoInterface) {
            return $nodeRoute['name'] === $_seo->getUid();
        }

        if ($nodeRoute['name'] !== $requestRoute['name']) {
            return false;
        }

        $params = $requestRoute['params'];
        foreach ($nodeRoute['params'] as $key => $value) {
            if (strlen($value) > 0 && (!isset($params[$key]) || $params[$key] !== $value)) {
                return false;
            }
        }

        return true;
    }

    public function build(MenuInterface $menu)
    {
        return $this->_build($menu->getChildren()->toArray());
    }

    protected function _build(array $menus)
    {
        if (count($menus) === 0) {
            return [];
        }

        /* @var $menu MenuInterface */
        $menu = array_shift($menus);

        if (!$menu->isEnabled()) {
            return $this->_build($menus);
        }

        $url = $menu->getUrl();
        if ($menu->getRouteName() !== null) {
            try {
                $url = $this->router->generate($menu->getRouteName(), $menu->getRouteParams());
            } catch (\Exception $exception) {
                return $this->_build($menus);
            }
        }

        $node = [
            'url'      => $url,
            'route'    => $menu->getRoute(),
            'name'     => $menu->getName(),
            'title'    => $menu->getTitle(),
            'children' => $this->_build($menu->getChildren()->toArray()),
            'roles'    => $menu->getRoles(),
            'active'   => false,
        ];

        return array_merge([$node], $this->_build($menus));
    }

    private function populate(PropertyAccessor $propertyAccessor, array $attributes, $value)
    {
        if (preg_match_all('`\{(([^\.]+)\.([^\}]+))\}`', $value, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $_value = $propertyAccessor->getValue($attributes, sprintf('[%s].%s', $match[2], $match[3]));
                $value = preg_replace(sprintf('`%s`', preg_quote($match[0])), $_value, $value);
            }
        }

        return $value;
    }

    protected function addItem($item, &$nodes, $position = 0)
    {
        array_splice($nodes, $position, null, [$item]);

        return $nodes;
    }
}
