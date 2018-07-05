<?php

namespace KRG\CmsBundle\Routing;

use Doctrine\ORM\Query;
use KRG\CmsBundle\Entity\SeoInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

class SeoLoader extends Loader implements RoutingLoaderInterface
{
    /** @var Serializer */
    private $serializer;

    /** @var string */
    private $dataCacheDir;

    /** @var Query */
    private $query;

    public function __construct(EntityManagerInterface $entityManager, string $dataCacheDir, string $defaultLocale, array $intlLocales)
    {
        $normalizer = new PropertyNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $this->serializer = new Serializer([$normalizer], [new JsonEncoder()]);
        $this->dataCacheDir = $dataCacheDir;
        $this->query = $entityManager->getRepository(SeoInterface::class)->findEnabledQb()->getQuery();

        // If KRGIntlBundle is enabled, force default locale on Seos query for clean routes
        if (count($intlLocales) > 0) {
            $this->query->setHint(constant('Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE'), $defaultLocale);
            $this->query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        }
    }

    public function load($resource, $type = null)
    {
        /** @var RouteCollection $collection */
        $collection = $this->import($resource);

        return $this->handle($collection);
    }

    public function handle(RouteCollection $collection)
    {
        $seoCollection = new RouteCollection();
        try {
            foreach ($this->query->getResult() as $seo) {
                /** @var Route $route */
                /** @var Route $routeClone */
                /** @var SeoInterface $seo */
                if (null === ($route = $collection->get($seo->getRouteName()))) {
                    continue;
                }

                $route->setDefault('_cache_dir', $this->dataCacheDir);

                $routeClone = clone $route;
                $routeClone
                    ->setPath($seo->getUrl())
                    ->setDefaults(array_diff_key($route->getDefaults(), ['_seo_list' => null]));

                $seo->setCompiledRoute($routeClone->compile());

                $serializedSeo = $this->serializer->serialize($seo, 'json');
                if ($seo->getRouteName() === 'krg_page_show') {
                    $routeClone->setDefault('_seo', $serializedSeo);
                }

                $_seos = $route->getDefault('_seo_list') ?: [];
                $_seos[] = $serializedSeo;
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
