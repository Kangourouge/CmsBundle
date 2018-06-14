<?php

namespace KRG\CmsBundle\Routing;

use KRG\CmsBundle\Entity\SeoInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class SeoLoader extends Loader implements RoutingLoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Serializer */
    private $serializer;

    /** @var string */
    private $dataCacheDir;

    /** @var string */
    private $seoClass;

    public function __construct(EntityManagerInterface $entityManager, string $dataCacheDir)
    {
        $this->entityManager = $entityManager;
        $normalizer = new PropertyNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $this->serializer = new Serializer([$normalizer], [new JsonEncoder()]);
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

        $seoCollection = new RouteCollection();
        try {
            foreach ($seos as $seo) {
                /** @var Route $route */
                /** @var Route $routeClone */
                /** @var SeoInterface $seo */
                if (null === ($route = $collection->get($seo->getRouteName()))) {
                    continue;
                }

                $routeClone = clone $route;
                $routeClone
                    ->setPath($seo->getUrl())
                    ->setDefaults(array_diff_key($routeClone->getDefaults(), ['_cache_dir' => null, '_seo_list' => null]));

                if ($seo->getRouteName() === 'krg_page_show') {
                    $routeClone->setDefault('_seo_id', $seo->getId());
                }

                $seo->setCompiledRoute($routeClone->compile());

                $_seos = $route->getDefault('_seo_list') ?: [];
                $_seos[] = $this->serializer->serialize($seo, 'json');

                $route->setDefault('_cache_dir', $this->dataCacheDir);
                $route->setDefault('_seo_list', $_seos);

                $seoCollection->add($seo->getUid(), $routeClone);
            }
        } catch (\Exception $exception) {
        }

        return $seoCollection;
    }

    public function supports($resource, $type = null)
    {
        return 'seo' === $type;
    }
}
