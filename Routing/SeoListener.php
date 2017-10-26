<?php

namespace KRG\SeoBundle\Routing;

use Doctrine\ORM\EntityManagerInterface;
use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Repository\SeoRepository;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class SeoListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    /**
     * Update current request if URI match with one of SEOBUNDLE urls
     *
     * @param GetResponseEvent $event
     * @return null|void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->get('_route');

        // Retrieve the original route
        if (!preg_match("/^krg_seo_.+/", $route)) {
            return null;
        }

        /* @var $seoRepository SeoRepository */
        $className = $this->entityManager->getClassMetadata(SeoInterface::class)->getName();
        $seoRepository = $this->entityManager->getRepository($className);

        /* @var $seo SeoInterface */
        $seo = $seoRepository->findOneByUid($route);
        if ($seo === null) {
            return null;
        }

        // Update request to keep url intact
        $route = $this->router->getRouteCollection()->get($seo->getRoute());
        $params = array_merge($request->attributes->get('_route_params'), $seo->getParameters());
        $request->attributes->set('_controller', $route->getDefault('_controller'));
        $request->attributes->set('_route', $seo->getRoute());
        $request->attributes->set('_seo', $seo); // Store initial SEO to reuse it after
        $request->attributes->set('_route_params', $params);
        foreach($seo->getRouteParameters() as $key => $value) {
            if ($value) {
                $request->attributes->set($key, $value);
            }
        }
    }
}
