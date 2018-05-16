<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\PageInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PageListener implements EventSubscriber
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
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof PageInterface) {
            $page = $event->getEntity();
            $seo = $page->getSeo();
            $page->setKey(sprintf('krg_seo_krg_page_show_%s', uniqid()));
            $seo->setRoute([
                'name'   => 'krg_page_show',
                'params' => ['key' => $page->getKey()],
            ]);
            $seo->setUid($page->getKey());
            $this->prePersistOrUpdate($event, $page);
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
        $seo->setEnabled($page->getEnabled());
        $this->eventDispatcher->dispatch('cache:clear:twig');
    }
}
