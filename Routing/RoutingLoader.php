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
        /** @var $appCollection RouteCollection */
        $appCollection = $this->import($resource);

        $tmpCollection = new RouteCollection();
        foreach ($this->loaders as $loader) {
            $appCollection->addCollection($loader->handle($appCollection));
        }

        return $appCollection;

        $highPriorityCollection = $this->getHighPriorityCollection($tmpCollection);

        $collection = new RouteCollection();
        $collection->addCollection($highPriorityCollection);
        $collection->addCollection($appCollection);
        $collection->addCollection($this->substractCollection($highPriorityCollection, $tmpCollection));

        return $collection;
    }

    public function getHighPriorityCollection(RouteCollection $collection)
    {
        $highPriorityCollection = new RouteCollection();

        foreach ($collection as $name => $route) {
            $variables = $route->compile()->getVariables();

            if (count($variables) === 0 && false === strstr($name, '_i18n_')) {
                $highPriorityCollection->add($name, $route);
            }
        }

        return $highPriorityCollection;
    }

    public function substractCollection(RouteCollection $subCollection, RouteCollection $srcCollection)
    {
        $srcCollection->remove(array_keys($subCollection->all()));

        return $srcCollection;
    }

    public function supports($resource, $type = null)
    {
        return 'krg.routing.loader' === $type;
    }
}
