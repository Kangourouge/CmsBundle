<?php

namespace KRG\CmsBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollection;

interface RoutingLoaderInterface extends LoaderInterface
{
    public function handle(RouteCollection $collection);
}