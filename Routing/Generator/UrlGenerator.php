<?php

namespace KRG\CmsBundle\Routing\Generator;

use KRG\CmsBundle\Entity\Seo;
use KRG\CmsBundle\Entity\SeoInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

class UrlGenerator extends \Symfony\Component\Routing\Generator\UrlGenerator
{
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $locale = $parameters['_locale'] ?? $this->context->getParameter('_locale');

        if (null !== $locale && ($localizedRoute = $this->routes->get($name.'.'.$locale)) && $localizedRoute->getDefault('_canonical_route') === $name) {
            $name = $name.'.'.$locale;
        }

        return parent::generate($name, $parameters, $referenceType);
    }

    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        if (isset($defaults['_seo_list'])) {
            $compiledRoute = $this->resolve($defaults['_seo_list'], $name, $parameters, $requirements, $defaults);
            if ($compiledRoute !== null) {
                $_parameters = [];
                foreach ($parameters as $key => $value) {
                    if (in_array($key, $compiledRoute['pathVariables'])) {
                        $_parameters[$key] = $value;
                    }
                }

                $parameters = $_parameters;
                $variables = $compiledRoute['variables'];
                $tokens = $compiledRoute['tokens'];
                $hostTokens = $compiledRoute['hostTokens'];
            }
        }

        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }

    private function resolve(array $seos, $name, array $parameters, array $requirements, array $defaults)
    {
        $filesystemAdapter = new FilesystemAdapter('seo', 0, $defaults['_cache_dir']);

        // Check if route can be resolved from cache
        $cacheKey = sha1(sprintf('%s_%s', $name, json_encode($parameters)));
        $cacheItem = $filesystemAdapter->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $serializer = new Serializer([new PropertyNormalizer()], [new JsonEncoder()]);
        $compiledRoute = null;
        // Sort entries by number of matching parameters
        if (count($seos) > 0) {
            $defaultLocale = $this->getDefaultLocale($defaults);
            $weights = [];
            foreach ($seos as $idx => &$seo) {
                /* @var $seo SeoInterface */
                $seo = $serializer->deserialize($seo, Seo::class, 'json'); // Get class from metadata factory

                // Skip Seos with other locale than expected
                $_routeParams = $seo->getRouteParams();
                if (isset($requirements['_locale']) &&
                    ((false === isset($_routeParams['_locale']) && $requirements['_locale'] !== $defaultLocale) ||
                    (isset($_routeParams['_locale']) && $_routeParams['_locale'] !== $requirements['_locale']))) {
                    continue;
                }

                if (($diff = $seo->diff($parameters)) >= 0) {
                    $weights[$idx] = $seo->diff($parameters);
                }
            }
            unset($seo);

            if (count($weights) > 0) {
                asort($weights);
                // Won't generate a route with parameters if passed parameters are empty
                if (count($parameters) === 0 && current($weights) !== 0) {
                    return null;
                }
                $seo = $seos[key($weights)];


                $compiledRoute = $seo->getCompiledRoute();
            }
        }

        // Cache
        $cacheItem->set($compiledRoute);
        $filesystemAdapter->save($cacheItem);

        return $compiledRoute;
    }

    /**
     * Get defaultLocale from canonical route because UrlGenerator is not a service
     */
    protected function getDefaultLocale(array $routeData)
    {
        if (isset($routeData['_canonical_route']) && $route = $this->routes->get($routeData['_canonical_route'])) {
            return $route->getDefault('_locale');
        }

        return null;
    }
}
