<?php

namespace KRG\CmsBundle\EventSubscriber;

use KRG\CmsBundle\Breadcrumb\BreadcrumbBuilderInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class BreadcrumbListener
{
    /** @var BreadcrumbBuilderInterface */
    protected $breadcrumbBuilder;

    public function __construct(BreadcrumbBuilderInterface $breadcrumbBuilder)
    {
        $this->breadcrumbBuilder = $breadcrumbBuilder;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $this->breadcrumbBuilder->build($event->getRequest());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}
