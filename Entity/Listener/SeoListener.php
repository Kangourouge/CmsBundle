<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\DependencyInjection\KRGCmsExtension;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SeoListener implements EventSubscriber
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postFlush
        ];
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->getEntity() instanceof SeoInterface) {
            $this->prePersistOrUpdate($event, $event->getEntity());
        }
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof SeoInterface) {
            $seo = $event->getEntity();

            if (!$seo->getUid()) {
                $seo->setUid(KRGCmsExtension::KRG_ROUTE_SEO_PREFIX.uniqid());
            }

            $this->prePersistOrUpdate($event, $seo);
        }
    }

    public function prePersistOrUpdate(LifecycleEventArgs $event, SeoInterface $seo)
    {
        if (null === $seo->getPriority()) {
            $seo->setPriority(0);
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        $this->eventDispatcher->dispatch('cache:clear');
    }
}
