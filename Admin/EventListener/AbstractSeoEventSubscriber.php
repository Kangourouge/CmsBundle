<?php

namespace KRG\SeoBundle\Admin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use KRG\SeoBundle\DependencyInjection\ClearRoutingCache;
use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Form\SeoFormRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractSeoEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ClearRoutingCache
     */
    protected $clearRoutingCache;

    /**
     * @var SeoFormRegistry
     */
    protected $seoFormRegistry;

    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager,  ClearRoutingCache $clearRoutingCache, SeoFormRegistry $seoFormRegistry)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->clearRoutingCache = $clearRoutingCache;
        $this->seoFormRegistry = $seoFormRegistry;
    }

    public function clearRoutingCache()
    {
        $this->clearRoutingCache->exec();
    }

    /**
     * initSeo object
     *
     * @param SeoInterface $seo
     * @param $url
     * @param bool $forSeoPage
     * @return SeoInterface
     */
    public function initSeo(SeoInterface $seo, $url, $forSeoPage = false)
    {
        $seo->setEnabled(false);
        $seo->setUrl($url);
        if ($forSeoPage) {
            $seo->setRoute('krg_seo_page_show');
        }

        return $seo;
    }

    /**
     * Return url from route name
     *
     * @param $route
     * @return null|string
     */
    protected function getPathFromRoute($route)
    {
        if ($route = $this->router->getRouteCollection()->get($route)) {
            return $route->getPath();
        }

        return null;
    }

}
