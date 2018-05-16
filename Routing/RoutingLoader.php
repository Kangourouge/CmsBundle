<?php

namespace KRG\CmsBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutingLoader extends Loader
{
    /**
     * @var array
     */
    private $loaders;

    /**
     * RoutingLoader constructor.
     *
     * @param LoaderResolverInterface $loaderResolver
     * @param array $loaders
     */
    public function __construct(LoaderResolverInterface $loaderResolver)
    {
        /** @var RoutingLoaderInterface $loader */
        $this->loaders = [];
        foreach($loaderResolver->getLoaders() as $loader) {
            if ($loader instanceof RoutingLoaderInterface) {
                $this->loaders[] = $loader;
            }
        }
    }

    public function load($resource, $type = null)
    {
        $collection = $this->import($resource);
        try {
            foreach ($this->loaders as $loader) {
                $collection = $loader->handle($collection);
            }
        } catch (\Exception $exception) {
        }
        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'krg.routing.loader' === $type;
    }
}