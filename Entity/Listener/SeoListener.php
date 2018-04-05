<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\DependencyInjection\ClearCache;
use KRG\CmsBundle\Entity\SeoInterface;

class SeoListener implements EventSubscriber
{
    /** @var ClearCache */
    private $clearCache;

    public function __construct(ClearCache $clearCache)
    {
        $this->clearCache = $clearCache;
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
        $this->clearCache->warmupRouting();
    }
}
