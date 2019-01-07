<?php

namespace KRG\CmsBundle\Routing;

use Doctrine\ORM\EntityManagerInterface;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class RoutingLoader extends Loader
{
    /** @var array */
    private $loaders;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(LoaderResolverInterface $loaderResolver, EntityManagerInterface $entityManager)
    {
        /** @var RoutingLoaderInterface $loader */
        $this->loaders = [];
        foreach ($loaderResolver->getLoaders() as $loader) {
            if ($loader instanceof RoutingLoaderInterface) {
                $this->loaders[] = $loader;
            }
        }

        $this->entityManager = $entityManager;
    }

    public function load($resource, $type = null)
    {
        /** @var $appCollection RouteCollection */
        $appCollection = $this->import($resource);

        foreach ($this->loaders as $loader) {
            $appCollection->addCollection($loader->handle($appCollection));
        }

        $highPriorityCollection = $this->getHighPriorityCollection($appCollection);

        $collection = new RouteCollection();
        $collection->addCollection($highPriorityCollection);
        $collection->addCollection($this->substractCollection($highPriorityCollection, $appCollection));

        return $collection;
    }

    public function getHighPriorityCollection(RouteCollection $collection)
    {
        $highPriorityCollection = new RouteCollection();

        $serializer = new Serializer([new PropertyNormalizer()], [new JsonEncoder()]);
        $seoClass = $this->entityManager->getClassMetadata(SeoInterface::class)->getName();

        foreach ($collection as $name => $route) {
            if ($route->hasDefault('_seo_list')) {
                foreach ($route->getDefault('_seo_list') as $seo) {
                    /** @var $seo SeoInterface */
                    $seo = $serializer->deserialize($seo, $seoClass, 'json');
                    if ($seoRoute = $collection->get($seo->getUid())) {
                        $compiledSeoRoute = $seoRoute->compile();
                        $compiledRoute = $route->compile();

                        if ($compiledRoute->getStaticPrefix() === $compiledSeoRoute->getStaticPrefix()
                            || count($compiledSeoRoute->getVariables()) === 0
                        ) {
                            $highPriorityCollection->add($seo->getUid(), $seoRoute);
                        }
                    }
                }
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
