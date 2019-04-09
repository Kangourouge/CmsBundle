<?php

namespace KRG\CmsBundle\Menu;

use Doctrine\Common\Annotations\AnnotationReader;
use Gedmo\Exception\RuntimeException;
use Gedmo\Translatable\Entity\Translation;
use KRG\CmsBundle\Annotation\Menu;
use KRG\CmsBundle\Entity\MenuInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Util\Helper;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

class MenuBuilder implements MenuBuilderInterface
{
    /** @var Request */
    protected $request;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var RouterInterface */
    protected $router;

    /** @var AnnotationReader */
    protected $annotationReader;

    /** @var array */
    protected $annotations;

    /** @var FilesystemAdapter */
    protected $filesystemAdapter;

    /** @var string */
    protected $defaultLocale;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        AnnotationReader $annotationReader,
        string $dataCacheDir,
        string $defaultLocale
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->annotationReader = $annotationReader;
        $this->annotations = [];
        $this->filesystemAdapter = new FilesystemAdapter('menu', 0, $dataCacheDir);
        $this->defaultLocale = $defaultLocale;
    }

    public function getNodes($key)
    {
        $nodes = $this->getNodeTree($key);
        $this->activeNodes($nodes);

        return $nodes;
    }

    public function getNodeTree($key)
    {
        $locale = $this->request ? $this->request->getLocale() : $this->defaultLocale;
        $item = $this->filesystemAdapter->getItem(sprintf('%s_%s', $locale, $key));

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
        $nodes = $this->build($menu, $locale);

        if (isset($item)) {
            $item->set($nodes);
            $this->filesystemAdapter->save($item);
        }

        return $nodes;
    }

    public function getActiveNodes(string $key = null)
    {
        if ($key === null) {
            $activeNodes = [];
            foreach ($this->findRootMenus() as $menu) {
                if (count($activeNodes = $this->getActiveNodes($menu->getKey())) > 0) {
                    break;
                }
            }

            return $activeNodes;
        }

        $nodes = $this->getNodeTree($key);
        $activeNodes = $this->activeNodes($nodes);
        foreach ((array)$this->getAnnotations() as $annotation) {
            $exists = array_filter($activeNodes, function($node) use ($annotation) {
                return $node['url'] === $annotation->getUrl();
            });

            if (count($exists) === 0 || null === $annotation->getUrl()) {
                array_push($activeNodes, [
                    'name'     => $annotation->getName(),
                    'title'    => $annotation->getName(),
                    'url'      => $annotation->getUrl(),
                    'route'    => $annotation->getRoute(),
                    'children' => [],
                    'roles'    => [],
                    'active'   => true,
                ]);
            }
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
        if (null === $this->request || (null === $node['url'] && count($node['route']) === 0)) {
            return false;
        }

        $nodeRoute = $node['route'];
        $requestRoute = [
            'name'   => count($this->getAnnotations()) > 0 ? $this->annotations[0]->getRoute() : $this->request->get('_route'),
            'params' => count($this->getAnnotations()) > 0 ? $this->annotations[0]->getParams() : $this->request->get('_route_params'),
        ];

        if (null === $requestRoute['name']) {
            return false;
        }

        if (($requestRoute['name'] === 'krg_page_show' || $requestRoute['name'] === 'krg_cms_filter_show')
            && ($_seo = $this->request->get('_seo')) instanceof SeoInterface) {
            return $node['url'] === $_seo->getUrl();
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

    public function build(MenuInterface $menu, string $locale)
    {
        return $this->_build($menu->getChildren()->toArray(), $locale);
    }

    protected function _build(array $menus, string $locale)
    {
        if (count($menus) === 0) {
            return [];
        }

        /* @var $menu MenuInterface */
        $menu = array_shift($menus);

        if (!$menu->isEnabled()) {
            return $this->_build($menus, $locale);
        }

        $url = $menu->getUrl();
        if ($menu->getRouteName() !== null) {
            try {
                $url = $this->router->generate($menu->getRouteName(), $menu->getRouteParams());
            } catch (\Exception $exception) {
                return $this->_build($menus, $locale);
            }
        }

        $menuTranslations = null;
        try {
            // Search translated menu
            $translatableRepository = $this->entityManager->getRepository(Translation::class);
            $menuTranslations = $translatableRepository->findTranslations($menu);
        } catch (RuntimeException $exception) {
        }

        $node = [
            'url'                => $url,
            'key'                => $menu->getKey(),
            'route'              => $menu->getRoute(),
            'icon'               => $menu->getIcon(),
            'name'               => $menuTranslations[$locale]['name'] ?? $menu->getName(),
            'title'              => $menuTranslations[$locale]['title'] ?? $menu->getTitle(),
            'content'            => $menuTranslations[$locale]['content'] ?? $menu->getContent(),
            'children'           => $this->_build($menu->getChildren()->toArray(), $locale),
            'roles'              => $menu->getRoles(),
            'lvl'                => $menu->getLvl(),
            'active'             => false,
            'breadcrumb_display' => $menu->isBreadcrumbDisplay(),
        ];

        return array_merge([$node], $this->_build($menus, $locale));
    }

    protected function addItem($item, &$nodes, $position = 0)
    {
        array_splice($nodes, $position, null, [$item]);

        return $nodes;
    }

    protected function findRootMenus()
    {
        return $this->entityManager->getRepository(MenuInterface::class)->findBy(['lvl' => 0]);
    }

    public function getAnnotations()
    {
        if (null === $this->request || false === $this->request->get('_controller')) {
            return null;
        }

        if ($this->annotations) {
            return $this->annotations;
        }

        $seoUrls = $this->entityManager->getRepository(SeoInterface::class)->findAllActivesUrls();
        $annotations = [];
        try {
            $reflectionMethod = new \ReflectionMethod($this->request->get('_controller'));
            foreach ($this->annotationReader->getMethodAnnotations($reflectionMethod) as $key => $annotation) {
                if ($annotation instanceof Menu) {
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    $attributes = $this->request->attributes->all();
                    $url = null;

                    $params = $annotation->getParams();
                    foreach ($params as $_key => &$value) {
                        $value = $this->populate($propertyAccessor, $attributes, $value);
                    }
                    unset($value);
                    $annotation->setParams($params);

                    if ($annotation->getUrl()) {
                        $populatedUrl = strtolower($this->populate($propertyAccessor, $attributes, $annotation->getUrl()));
                        $path = filter_var($populatedUrl, FILTER_VALIDATE_URL) ? $populatedUrl : $this->request->getSchemeAndHttpHost().$populatedUrl;
                        if (in_array($populatedUrl, $seoUrls) || Helper::urlExists($path)) {
                            $url = $populatedUrl;
                        }
                    }

                    if ($annotation->getRoute()) {
                        try {
                            $url = $this->router->generate($annotation->getRoute(), $annotation->getParams());
                        } catch (\Exception $exception) {
                            continue;
                        }
                    }

                    $annotation
                        ->setName($this->populate($propertyAccessor, $attributes, $annotation->getName()))
                        ->setUrl($url);

                    $annotations[] = $annotation;
                }
            }
        } catch (\ReflectionException $exception) {
        }

        $this->annotations = $annotations;

        return $annotations;
    }

    private function populate(PropertyAccessor $propertyAccessor, array $attributes, $value)
    {
        try {
            if (preg_match_all('`\{(([^\.]+)\.([^\}]+))\}`', $value, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $_value = $propertyAccessor->getValue($attributes, sprintf('[%s].%s', $match[2], $match[3]));
                    $value = preg_replace(sprintf('`%s`', preg_quote($match[0])), $_value, $value);
                }
            }
        } catch (\RuntimeException $exception) {
            return '';
        }

        return $value;
    }
}
