<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\MenuInterface;
use KRG\CmsBundle\Menu\MenuBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuListener implements EventSubscriber
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * MenuListener constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getSubscribedEvents()
    {
        return [Events::postUpdate];
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof MenuInterface) {
            $this->eventDispatcher->dispatch('cache:clear:data');
        }
    }
}
