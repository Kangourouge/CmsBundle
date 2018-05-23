<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\MenuInterface;
use KRG\CmsBundle\Util\Str;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuListener implements EventSubscriber
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
            Events::preUpdate,
            Events::prePersist,
            Events::postUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof MenuInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->getEntity() instanceof MenuInterface) {
            $this->prePersistOrUpdate($event->getEntity());
        }
    }

    public function prePersistOrUpdate(MenuInterface $menu)
    {
        /** @var $menu MenuInterface */
        if (strlen($menu->getKey()) === 0) {
            $menu->setKey($this->generateKey($menu));
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof MenuInterface) {
            $this->eventDispatcher->dispatch('cache:clear:data');
        }
    }

    protected function generateKey(MenuInterface $menu, $index = 0)
    {
        $prefix = $menu->getParent() ? $menu->getParent()->getKey().'_' : '';
        $suffix = $index > 0 ? '_'.$index : '';

        $key = sprintf('%s%s%s', $prefix, Str::underscoreCase($menu->getName()), $suffix);
        $key = (strlen($key) > 200) ? substr($key, 0, 200) : $key;

        if ($this->entityManager->getRepository(MenuInterface::class)->findOneBy(['key' => $key]) === null) {
            return $key;
        }

        return $this->generateKey($menu, $index + 1);
    }
}
