<?php

namespace KRG\SeoBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\SeoBundle\DependencyInjection\ClearCache;
use KRG\SeoBundle\Entity\SeoInterface;

class SeoListener implements EventSubscriber
{
    /**
     * @var ClearCache
     */
    private $clearCache;

    /**
     * SeoListener constructor.
     *
     * @param ClearCache $clearCache
     */
    public function __construct(ClearCache $clearCache)
    {
        $this->clearCache = $clearCache;
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
        $this->clearCache->warmupRouting();
    }
}
