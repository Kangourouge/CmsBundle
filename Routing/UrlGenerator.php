<?php

namespace KRG\CmsBundle\Routing;

use KRG\CmsBundle\Entity\Seo;
use KRG\CmsBundle\Entity\SeoInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UrlGenerator extends BaseUrlGenerator
{
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        if (isset($defaults['_seo_list'])) {
            $compiledRoute = $this->resolve($defaults['_seo_list'], $name, $parameters, $defaults['_cache_dir']);
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

        return parent::doGenerate(
            $variables,
            $defaults,
            $requirements,
            $tokens,
            $parameters,
            $name,
            $referenceType,
            $hostTokens,
            $requiredSchemes
        );
    }

    private function resolve(array $seos, $name, array $parameters, $cacheDir)
    {
        $filesystemAdapter = new FilesystemAdapter('seo', 0, $cacheDir);

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
            $weights = [];
            foreach ($seos as $idx => &$seo) {
                /* @var $seo SeoInterface */
                $seo = $serializer->deserialize($seo, Seo::class, 'json');
                if (($diff = $seo->diff($parameters)) >= 0) {
                    $weights[$idx] = $diff;
                }
            }
            unset($seo);
            if (count($weights) > 0) {
                asort($weights);
                $seo = $seos[key($weights)];

                $compiledRoute = $seo->getCompiledRoute();
            }
        }

        // Store in cache
        $cacheItem->set($compiledRoute);
        $filesystemAdapter->save($cacheItem);

        return $compiledRoute;
    }
}
