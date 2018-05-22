<?php

namespace KRG\CmsBundle\Entity\Listener;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_LIST_QUERY_BUILDER => ['onPostListQueryBuilder'],
        ];
    }

    public function onPostListQueryBuilder($array)
    {
        dump($array);
    }
}
