<?php

namespace KRG\SeoBundle\Admin\EventListener;

use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use KRG\SeoBundle\DependencyInjection\ClearRoutingCache;
use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Entity\SeoPageInterface;
use KRG\SeoBundle\Util\Redirector;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\RouterInterface;

class SeoPageAdminSubscriber extends AbstractSeoEventSubscriber
{
    public static function getSubscribedEvents()
    {
        return array(
            EasyAdminEvents::PRE_PERSIST  => array('prePersist'),
            EasyAdminEvents::POST_UPDATE  => array('postUpdate'),
            EasyAdminEvents::POST_PERSIST => array('postPersist'),
        );
    }

    public function postUpdate(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof SeoPageInterface)) {
            return;
        }

        $this->clearRoutingCache();
    }

    /**
     * Assign a Seo object to SeoPage
     *
     * @param GenericEvent $event
     */
    public function prePersist(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof SeoPageInterface)) {
            return;
        }

        /* @var $seo SeoInterface */
        $seo = $this->entityManager->getClassMetadata(SeoInterface::class)->getReflectionClass()->newInstanceArgs();
        $seo = $this->initSeo($seo, $entity->getUrl(), true);
        $entity->setSeo($seo);

        if ($formType = $entity->getFormType()) {
            if ($service = $this->seoFormRegistry->get($formType)) {
                $entity->setFormRoute($service['route']);
                $entity->setFormParameters([]);
            }
        }

        $event['entity'] = $entity;
    }

    /**
     * After creating redirect to edition page instead of list
     *
     * @param GenericEvent $event
     */
    public function postPersist(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof SeoPageInterface)) {
            return;
        }

        $event['request'] = Redirector::redirector($event, $this->router->generate('easyadmin'), 'SeoPage', 'edit', $entity->getId());
    }
}
