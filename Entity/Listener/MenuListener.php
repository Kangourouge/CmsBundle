<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use KRG\CmsBundle\Entity\MenuInterface;
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
            Events::postUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof MenuInterface) {
            /** @var $menu MenuInterface */
            $menu = $event->getEntity();
            if (strlen($menu->getKey()) === 0) {
                $menu->setKey($this->generateKey($menu));
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->getEntity() instanceof MenuInterface) {
            /** @var $menu MenuInterface */
            $menu = $event->getEntity();
            if (strlen($menu->getKey()) === 0) {
                $menu->setKey($this->generateKey($menu));
            }
        }
    }

    protected function generateKey(MenuInterface $menu, $index = 0)
    {
        $prefix = '';
        /** @var $elder MenuInterface */
        foreach ($menu->getHierarchy() as $elder) {
            $prefix .= $elder->getKey().'_';
        }

        $suffix = $index > 0 ? '_'.$index : '';
        $key = sprintf('%s%s%s', $prefix, self::underscoreCase($menu->getName()), $suffix);
        $key = (strlen($key) > 200) ? substr($key, 0, 200) : $key;
        if ($this->entityManager->getRepository(MenuInterface::class)->findOneBy(['key' => $key]) === null) {
            return $key;
        }

        return self::generateKey($menu, $index + 1);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof MenuInterface) {
            $this->eventDispatcher->dispatch('cache:clear:data');
        }
    }

    public static function underscoreCase($string)
    {
        $string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
        $string = trim($string);
        $string = str_replace(' ', '_', $string);
        $string = strtolower($string);

        return $string;
    }
}
