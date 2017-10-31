<?php

namespace KRG\SeoBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\SeoBundle\DependencyInjection\ClearRoutingCache;
use KRG\SeoBundle\Entity\SeoInterface;

class SeoListener implements EventSubscriber
{
    /**
     * @var ClearRoutingCache
     */
    private $clearRoutingCache;

    /**
     * SeoListener constructor.
     *
     * @param ClearRoutingCache $clearRoutingCache
     */
    public function __construct(ClearRoutingCache $clearRoutingCache)
    {
        $this->clearRoutingCache = $clearRoutingCache;
    }

    public function getSubscribedEvents()
    {
        return [Events::prePersist, Events::preUpdate, Events::postFlush];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof SeoInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->getEntity() instanceof SeoInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function prePersistOrUpdate(SeoInterface $seo)
    {
        $route = $seo->getRouteName();
        $prefix = preg_match("/^krg_seo_.+/", $route) ? '' : 'krg_seo_';
        $seo->setUid(sprintf('%s%s_%s', $prefix, $route, uniqid()));
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        $this->clearRoutingCache->exec();
    }
}
