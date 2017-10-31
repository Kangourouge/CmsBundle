<?php

namespace KRG\SeoBundle\Admin\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use KRG\SeoBundle\Entity\SeoInterface;
use KRG\SeoBundle\Util\Redirector;
use Symfony\Component\EventDispatcher\GenericEvent;

class SeoAdminSubscriber extends AbstractSeoEventSubscriber
{
    public static function getSubscribedEvents()
    {
        return [];
//        return array(
//            EasyAdminEvents::PRE_PERSIST  => array('prePersist'),
//            EasyAdminEvents::POST_UPDATE  => array('clear'),
//            EasyAdminEvents::POST_PERSIST => array('postPersist'),
//            EasyAdminEvents::POST_REMOVE  => array('clear'),
//        );
    }

    public function clear(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof SeoInterface)) {
            return;
        }

        $this->clearRoutingCache();
    }

    /**
     * On creates, set the url of the selected route
     *
     * @param GenericEvent $event
     */
    public function prePersist(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof SeoInterface)) {
            return;
        }

        $event['entity'] = $this->initSeo($entity, $this->getPathFromRoute($entity->getRoute()));
    }

    /**
     * After creating redirect to edition page instead of list
     *
     * @param GenericEvent $event
     */
    public function postPersist(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof SeoInterface)) {
            return;
        }

        $event['request'] = Redirector::redirector($event, $this->router->generate('easyadmin'), 'Seo', 'edit', $entity->getId());
    }
}
