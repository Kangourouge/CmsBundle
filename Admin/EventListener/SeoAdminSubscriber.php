<?php

namespace KRG\SeoBundle\Admin\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use KRG\SeoBundle\DependencyInjection\ClearRoutingCache;
use KRG\SeoBundle\Entity\SeoInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\RouterInterface;

class SeoAdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ClearRoutingCache
     */
    private $clearRoutingCache;

    public function __construct(RouterInterface $router, ClearRoutingCache $clearRoutingCache)
    {
        $this->router = $router;
        $this->clearRoutingCache = $clearRoutingCache;
    }

    public static function getSubscribedEvents()
    {
        return array(
            EasyAdminEvents::PRE_PERSIST  => array('prePersist'),
            EasyAdminEvents::POST_UPDATE  => array('postUpdate'),
        );
    }

    public function postUpdate(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof SeoInterface)) {
            return;
        }

        $this->clearRoutingCache->exec();
    }

    /**
     * On creates, set the url of the selected route
     *
     * @param GenericEvent $event
     */
    public function prePersist(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof SeoInterface)) {
            return;
        }

        $entity->setEnabled(false);
        $entity->setUrl($this->getPathFromRoute($entity->getRoute()));

        $event['entity'] = $entity;
    }

    /**
     * Return url from route name
     *
     * @param $route
     * @return null|string
     */
    private function getPathFromRoute($route)
    {
        if ($route = $this->router->getRouteCollection()->get($route)) {
            return $route->getPath();
        }

        return null;
    }

}
