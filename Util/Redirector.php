<?php

namespace KRG\CmsBundle\Util;

use Symfony\Component\BrowserKit\Request;
use Symfony\Component\EventDispatcher\GenericEvent;

class Redirector
{
    /**
     * Generate easyadmin EasyAdmin working URL
     */
    public static function getEasyAdminUrl($basePath, $entityName, $action, $id = null)
    {
        $url = $basePath;
        $url .= '?action='.$action;
        $url .= '&entity='.$entityName;
        if ($id) {
            $url .= '&id='.$id;
        }

        return $url;
    }

    /**
     * Update GenericEvent request with EasyAdmin working URL
     */
    public static function redirector(GenericEvent $event, $basePath, $entityName, $action, $id = null)
    {
        $url = self::getEasyAdminUrl($basePath, $entityName, $action, $id);

        /* @var $request Request */
        $request = $event->getArgument('request');
        $request->request->set('referer', $url);
        $request->query->set('referer', $url);

        return $request;
    }
}
