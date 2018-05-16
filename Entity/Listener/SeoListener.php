<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SeoListener implements EventSubscriber
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * BlockListener constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postFlush
        ];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof SeoInterface) {
            $seo = $event->getEntity();

            if (!$seo->getUid()) {
                $seo->setUid(sprintf('krg_seo_krg_page_show_%s', uniqid()));
            }
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        $this->eventDispatcher->dispatch('cache:clear');
    }
}
