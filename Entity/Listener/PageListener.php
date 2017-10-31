<?php

namespace KRG\SeoBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use KRG\SeoBundle\Entity\PageInterface;

class PageListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $event) {
        if ($event->getEntity() instanceof PageInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }


    public function preUpdate(PreUpdateEventArgs $event) {
        if ($event->getEntity() instanceof PageInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function prePersistOrUpdate(PageInterface $page) {
        $seo = $page->getSeo();

        $seo->setRoute([
            'name' => 'krg_page_show',
            'params' => ['key' => $page->getKey()]
        ]);

        $seo->setEnabled($page->getEnabled());
    }
}