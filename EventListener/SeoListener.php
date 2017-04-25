<?php

namespace KRG\SeoBundle\EventListener;

use KRG\SeoBundle\Repository\SeoRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;

class SeoListener
{
    /**
     * @var $router Router
     */
    private $router;

    /**
     * @var $entityManager EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $seoClass;

    /**
     * Update current request if URI match with one of SEOBUNDLE urls
     *
     * @param GetResponseEvent $event
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
            return;
        }

        /* @var $seoRepository SeoRepository */
        $seoRepository = $this->entityManager->getRepository($this->seoClass);

        /* @var $seo SeoInterface */
        $seo = $seoRepository->findOneByUid($route);
        if ($seo === null) {
            return;
        }

        // Update request to keep url intact
        $route = $this->router->getRouteCollection()->get($seo->getRoute());
        $params = array_merge($request->attributes->get('_route_params'), $seo->getParameters());
        $request->attributes->set('_controller', $route->getDefault('_controller'));
        $request->attributes->set('_route', $seo->getRoute());
        $request->attributes->set('_seo', $seo); // Store initial SEO to reuse it after
        $request->attributes->set('_route_params', $params);
        foreach($seo->getParameters() as $key => $value) {
            if ($value) {
                $request->attributes->set($key, $value);
            }
        }
    }

    /* */

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $seoClass
     */
    public function setSeoClass($seoClass)
    {
        $this->seoClass = $seoClass;
    }
}
