<?php

namespace KRG\CmsBundle\Routing;

use KRG\CmsBundle\Entity\SeoInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Load custom routes
 */
class SeoLoader extends Loader implements RoutingLoaderInterface
{
    /** @var EntityManager */
    private $entityManager;

    /** @var Serializer */
    private $serializer;

    /** @var string */
    private $dataCacheDir;

    public function __construct(EntityManagerInterface $entityManager, EncoderInterface $encoder, ObjectNormalizer $normalizer, string $dataCacheDir)
    {
        $this->entityManager = $entityManager;
        $normalizer->setCircularReferenceHandler(function($object) {
            return $object->getId();
        });
        $this->serializer = new Serializer([$normalizer], [$encoder]);
        $this->dataCacheDir = $dataCacheDir;
    }

    public function load($resource, $type = null)
    {
        /** @var RouteCollection $collection */
        $collection = $this->import($resource);
        return $this->handle($collection);
    }

    public function handle(RouteCollection $collection)
    {
        $seoRepository = $this->entityManager->getRepository(SeoInterface::class);
        $seos = $seoRepository->findBy(['enabled' => true]);

        try {
            foreach($seos as $seo) {
                /** @var Route $route */
                /** @var Route $routeClone */
                /** @var Route $routeRedirect */
                /** @var SeoInterface $seo */
                $route = $collection->get($seo->getRouteName());
                if ($route === null) {
                    continue;
                }

                $routeClone = clone $route;
                $routeClone->setPath($seo->getUrl());
                $routeClone->setDefaults(array_diff_key($routeClone->getDefaults(), ['_cache_dir' => null, '_seo_list' => null]));

                $seo->setCompiledRoute($routeClone->compile());

                $_seos = $route->getDefault('_seo_list') ?: [];
                $_seos[] = $this->serializer->serialize($seo, 'json');

                $route->setDefault('_cache_dir', $this->dataCacheDir);
                $route->setDefault('_seo_list', $_seos);

                $collection->add($seo->getUid(), $routeClone);
            }
        } catch (\Exception $exception) {}

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'seo' === $type;
    }
}
