<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\BlockInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BlockListener implements EventSubscriber
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
            Events::postPersist,
            Events::postUpdate,
            Events::preUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof BlockInterface) {
            $this->clearCache();
        }
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof BlockInterface) {
            $event->getObject()->setWorking(true);
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof BlockInterface) {
            $this->clearCache();

        }
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof BlockInterface) {
            $this->clearCache();
        }
    }

    protected function clearCache()
    {
        $this->eventDispatcher->dispatch('cache:clear:twig');
    }
}
