<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Util\Str;
use KRG\CmsBundle\Entity\BlockInterface;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BlockListener implements EventSubscriber
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postPersist,
            Events::postUpdate,
            Events::preUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof BlockInterface) {
            $this->clearCache();
        }
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof BlockInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof BlockInterface) {
            $event->getEntity()->setWorking(true);
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function prePersistOrUpdate(BlockInterface $block)
    {
        /** @var $menu BlockInterface */
        if (strlen($block->getKey()) === 0) {
            $block->setKey($this->generateKey($block));
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof BlockInterface) {
            $this->clearCache();
        }
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof BlockInterface) {
            $this->clearCache();
        }
    }

    protected function clearCache()
    {
        $this->eventDispatcher->dispatch('cache:clear:twig');
    }

    protected function generateKey(BlockInterface $menu, $index = 0)
    {
        $suffix = $index > 0 ? '_'.$index : '';
        $key = sprintf('%s%s', Str::underscoreCase($menu->getName()), $suffix);
        $key = (strlen($key) > 200) ? substr($key, 0, 200) : $key;

        if ($this->entityManager->getRepository(BlockInterface::class)->findOneBy(['key' => $key]) === null) {
            return $key;
        }

        return $this->generateKey($menu, $index + 1);
    }
}
