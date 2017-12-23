<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use KRG\CmsBundle\DependencyInjection\ClearCache;
use KRG\CmsBundle\Entity\BlockInterface;

class BlockListener implements EventSubscriber
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
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof BlockInterface) {
            $this->clearCache->warmupTwig();
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof BlockInterface) {
            $this->clearCache->warmupTwig();
        }
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof BlockInterface) {
            $this->clearCache->warmupTwig();
        }
    }
}
