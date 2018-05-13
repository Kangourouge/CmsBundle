<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\MenuInterface;
use KRG\CmsBundle\Menu\MenuBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class MenuListener implements EventSubscriber
{
    /** @var FilesystemAdapter */
    protected $cache;

    public function __construct($twigCacheDir)
    {
        $this->cache = new FilesystemAdapter(MenuBuilder::CACHE_NAMESPACE, 0, $twigCacheDir);
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof MenuInterface) {
            $this->cache->deleteItem($event->getEntity()->getRootParent()->getKey());
        }
    }
}
