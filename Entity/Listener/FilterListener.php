<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use KRG\CmsBundle\Entity\SeoInterface;
use KRG\CmsBundle\Entity\FilterInterface;
use KRG\CmsBundle\Util\Str;

class FilterListener implements EventSubscriber
{
    CONST KRG_FILTER_SHOW_ROUTE  = 'krg_cms_filter_show';
    CONST KRG_SCHEDULE_TO_DELETE = 'krg_cms_url_to_delete';

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postUpdate,
        ];
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
            $seo = $event->getEntity()->getSeo();
            $uow = $event->getEntityManager()->getUnitOfWork();
            $uow->computeChangeSet($event->getEntityManager()->getClassMetadata(SeoInterface::class), $seo);
            $uow->scheduleExtraUpdate($seo, $uow->getEntityChangeSet($seo));
        }

        $this->preUpdateFromSeo($event);
    }

    /**
     * If the filter no longer needs an url, mark SEO entity on preUpdate and schedule to delete it on postUpdate
     */
    public function preUpdateFromSeo(PreUpdateEventArgs $event)
    {
        if ($event->getEntity() instanceof SeoInterface) {
            $seo = $event->getEntity();

            if ($seo->getRouteName() === self::KRG_FILTER_SHOW_ROUTE && $seo->getUrl() === null) {
                $seo->setUrl(self::KRG_SCHEDULE_TO_DELETE);

                $filter = $event->getEntityManager()->getRepository(FilterInterface::class)->findOneBy(['seo' => $seo->getId()]);
                $filter->setSeo(null);

                $uow = $event->getEntityManager()->getUnitOfWork();
                $uow->computeChangeSet($event->getEntityManager()->getClassMetadata(FilterInterface::class), $filter);
                $uow->scheduleExtraUpdate($filter, $uow->getEntityChangeSet($filter));
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof SeoInterface) {
            $seo = $event->getEntity();

            if ($seo->getUrl() === self::KRG_SCHEDULE_TO_DELETE) {
                $event->getEntityManager()->getUnitOfWork()->scheduleForDelete($seo);
            }
        }
    }

    public function prePersistOrUpdate(FilterInterface $filter)
    {
        $seo = $filter->getSeo();

        if (strlen($filter->getKey()) === 0) {
            $filter->setKey($this->generateKey($filter));
        }

        if ($seo->getUrl()) {
            $seo->setEnabled(true);
            $seo->setRoute([
                'name'   => self::KRG_FILTER_SHOW_ROUTE,
                'params' => ['key' => $filter->getKey(), 'page' => null],
            ]);
        } else {
            $filter->setSeo(null);
        }

        $filter->setWorking(true);
    }

    protected function generateKey(FilterInterface $menu, $index = 0)
    {
        $suffix = $index > 0 ? '_'.$index : '';
        $key = sprintf('%s%s', Str::underscoreCase($menu->getName()), $suffix);
        $key = (strlen($key) > 200) ? substr($key, 0, 200) : $key;
        if ($this->entityManager->getRepository(FilterInterface::class)->findOneBy(['key' => $key]) === null) {
            return $key;
        }

        return $this->generateKey($menu, $index + 1);
    }
}
