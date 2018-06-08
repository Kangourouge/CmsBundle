<?php

namespace KRG\CmsBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

class RoutingLoader extends Loader
{
    /** @var array */
    private $loaders;

    public function __construct(LoaderResolverInterface $loaderResolver)
    {
        /** @var RoutingLoaderInterface $loader */
        $this->loaders = [];
        foreach ($loaderResolver->getLoaders() as $loader) {
            if ($loader instanceof RoutingLoaderInterface) {
                $this->loaders[] = $loader;
            }
        }
    }

    public function load($resource, $type = null)
    {
        $tmpCollection = new RouteCollection();
        /** @var $collection RouteCollection */
        $collection = $this->import($resource);

        try {
            foreach ($this->loaders as $loader) {
                $tmpCollection->addCollection($loader->handle($collection));
            }
        } catch (\Exception $exception) {
        }

        $finalCollection = new RouteCollection();

        $highPriorityCollection = $this->getHighPriorityCollection($collection);
        $lowPriorityCollection = $this->getLowPriorityCollection($tmpCollection);

        // 1 - High priority routes
        $finalCollection->addCollection($highPriorityCollection);
        // 2 - Loaded routes substracted from low priority routes
        $finalCollection->addCollection($this->removeCollection($tmpCollection, $lowPriorityCollection));
        // 3 - Original collection substracted from priority routes
        $finalCollection->addCollection($this->removeCollection($collection, $highPriorityCollection));
        // 4 - Low priority routes
        $finalCollection->addCollection($lowPriorityCollection);

        return $finalCollection;
    }

    public function getHighPriorityCollection(RouteCollection $collection)
    {
        $highPriorityCollection = new RouteCollection();

        foreach ($collection as $name => $route) {
            if (strstr($name, 'admin')) {
                $highPriorityCollection->add($name, $route);
            }
        }

        return $highPriorityCollection;
    }

    public function getLowPriorityCollection(RouteCollection $collection)
    {
        $lowPriorityCollection = new RouteCollection();

        foreach ($collection as $name => $route) {
            if ($route->hasOption('priority') && $route->getOption('priority') < 0) {
                $lowPriorityCollection->add($name, $route);
            }
        }

        return $lowPriorityCollection;
    }

    public function removeCollection(RouteCollection $srcCollection, RouteCollection $subCollection)
    {
        $srcCollection->remove(array_keys($subCollection->all()));

        return $srcCollection;
    }

    public function supports($resource, $type = null)
    {
        return 'krg.routing.loader' === $type;
    }
}
