<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoInterface;

class PageListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof PageInterface) {
            $this->prePersistOrUpdate($event, $event->getEntity());
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->getEntity() instanceof PageInterface) {
            $this->prePersistOrUpdate($event, $event->getEntity());

            $seo = $event->getEntity()->getSeo();
            $uow = $event->getEntityManager()->getUnitOfWork();
            $classMetadata = $event->getEntityManager()->getClassMetadata(SeoInterface::class);
            $uow->computeChangeSet($classMetadata, $seo);
            $uow->scheduleExtraUpdate($seo, $uow->getEntityChangeSet($seo));
        }
    }

    public function prePersistOrUpdate(LifecycleEventArgs $event, PageInterface $page)
    {
        $seo = $page->getSeo();
        $page->setKey($seo->getUid() ?? 'krg_seo_'.uniqid());
        $seo->setRoute([
            'name'   => 'krg_page_show',
            'params' => ['key' => $page->getKey()],
        ]);

        $seo->setEnabled($page->getEnabled());
    }
}
