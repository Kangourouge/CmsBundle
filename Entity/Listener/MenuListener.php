<?php

namespace KRG\CmsBundle\Entity\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Gedmo\Translatable\Entity\Translation;
use KRG\CmsBundle\Entity\Menu;
use KRG\CmsBundle\Entity\MenuInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use KRG\CmsBundle\Util\Str;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuListener implements EventSubscriber
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var array */
    private $intlLocales;

    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager, array $intlLocales)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->intlLocales = $intlLocales;
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
        $menu = $event->getEntity();

        if ($menu instanceof MenuInterface) {
            $this->prePersistOrUpdate($menu);

            if ($menu->getParent()) {
                $position = $menu->getParent()->getPosition();
                foreach ($menu->getParent()->getChildren() as $siblingMenu) {
                    $position = max($position, $siblingMenu->getPosition());
                }
                $menu->setPosition($position + 1);
            }
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

        // Find other locales Seo urls
        if ($this->intlLocales) {
            $transRepository = $this->entityManager->getRepository(Translation::class);
            $seoRepository = $this->entityManager->getRepository(SeoInterface::class);
            foreach ($this->intlLocales as $locale) {
                if ($seo = $seoRepository->findOneBy(['url' => $menu->getUrl()])) {
                    $seoTranslations = $transRepository->findTranslations($seo);

                    if (isset($seoTranslations[$locale]['url'])) {
                        $transMenuRouteInfo = $menu->getRoute();
                        $transMenuRouteInfo['url'] = $seoTranslations[$locale]['url'];
                        $transRepository->translate($menu, 'route', $locale, $transMenuRouteInfo);
                    }
                }
            }
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
