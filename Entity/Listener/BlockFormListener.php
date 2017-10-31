<?php

namespace KRG\SeoBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\SeoBundle\Entity\BlockFormInterface;

class BlockFormListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof BlockFormInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->getEntity() instanceof BlockFormInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    /**
     * Can't be there if it is not working
     * @param BlockFormInterface $blockForm
     */
    public function prePersistOrUpdate(BlockFormInterface $blockForm)
    {
        $blockForm->setWorking(true);
    }
}
