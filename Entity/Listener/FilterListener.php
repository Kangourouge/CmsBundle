<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\FilterInterface;

class FilterListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof FilterInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->getEntity() instanceof FilterInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    /**
     * Can't be there if it is not working
     *
     * @param FilterInterface $filter
     */
    public function prePersistOrUpdate(FilterInterface $filter)
    {
        $filter->setWorking(true);
    }
}
