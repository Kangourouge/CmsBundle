<?php

namespace KRG\CmsBundle\Routing;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\DependencyInjection\KRGCmsExtension;
use KRG\CmsBundle\Entity\SeoInterface;
use KRG\CmsBundle\Repository\SeoRepository;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class SeoListener
{
    /** @var RouterInterface */
    private $router;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $routeName = $request->get('_route');

        // Retrieve the original route
        if (!preg_match("/^".KRGCmsExtension::KRG_ROUTE_SEO_PREFIX.".+/", $routeName)) {
            return null;
        }

        $routeCollection = $this->router->getRouteCollection();
        if (($route = $routeCollection->get($routeName)) && $route->hasDefault('_canonical_route')) {
            $routeName = $route->getDefault('_canonical_route');
        }

        /* @var $seo SeoInterface */
        $seo = $this->entityManager->getRepository(SeoInterface::class)->findOneByUid($routeName);
        if ($seo === null) {
            return null;
        }

        // Update request to keep url intact
        $route = $routeCollection->get($seo->getRouteName());
        $request->attributes->set('_controller', $route->getDefault('_controller'));
        $request->attributes->set('_route', $seo->getRouteName());
        $request->attributes->set('_seo', $seo);

        $routeParams = [];
        foreach ($seo->getRouteParams() as $parameter => $value) {
            $routeParams[$parameter] = null === $value ? $request->attributes->get($parameter) : $value;
        }
        $request->attributes->set('_route_params', $routeParams);

        foreach ($seo->getRouteParams() as $key => $value) {
            if ($value) {
                $request->attributes->set($key, $value);
            }
        }
    }
}
