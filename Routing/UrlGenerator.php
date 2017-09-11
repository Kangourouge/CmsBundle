<?php

namespace KRG\SeoBundle\Routing;

use KRG\SeoBundle\Entity\SeoInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use Symfony\Component\Serializer\Serializer;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * @var array
     */
    private $seoRoutes;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var FilesystemAdapter
     */
    private $cache;

    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        if (!preg_match("/^krg_seo_.+/", $name)) {
            $route = $this->resolve($name, $parameters);
            if ($route !== null) {
                /* @var $compiledRoute CompiledRoute */
                $compiledRoute = $route->compile();

                // If parameters setted from SeoPage, do not set it twice
                if (count($compiledRoute->getVariables()) === 0) {
                    $parameters = array();
                }

                // Do not pass the parameters argument to keep rewritted urls intact
                return parent::doGenerate(
                    $compiledRoute->getVariables(),
                    $route->getDefaults(),
                    $route->getRequirements(),
                    $compiledRoute->getTokens(),
                    $parameters,
                    $name,
                    $referenceType,
                    $compiledRoute->getHostTokens(),
                    $route->getSchemes()
                );
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

    private function resolve($name, array $parameters)
    {
        if (!$this->cache) {
            $this->cache = new FilesystemAdapter('seo'); // TODO: APCu ?
        }

        // Check if route can be resolved from cache
        $identifier = $name;
        $flatten = implode(array_values($parameters));
        if (is_string($flatten)) { // "" is a string too
            $identifier .= $flatten;
            $cacheItem = $this->cache->getItem(md5($identifier));
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }
        }

        // Get all compatible routes
        $routes = array_filter($this->seoRoutes, function (Route $route) use ($name, $parameters) {
            /* @var $seo SeoInterface */
            $seo = $this->serializer->deserialize($route->getSeo(), $route->getSeoClass(), 'json');

            return $seo->getRoute() === $name && $seo->isValid($parameters);
        });

        $nbRoute = count($routes);

        // Sort entries by number of matching parameters
        if ($nbRoute > 1) {
            $weights = array();
            foreach ($routes as $idx => $route) {
                /* @var $seo SeoInterface */
                $seo = $this->serializer->deserialize($route->getSeo(), $route->getSeoClass(), 'json');
                $weights[$idx] = $seo->diff($parameters);
            }
            asort($weights);
            $route = $routes[key($weights)];
        } else if ($nbRoute === 1) {
            $route = reset($routes);
        } else {
            $route = null;
        }

        // Store in cache
        if (isset($cacheItem)) {
            $cacheItem->set($route);
            $this->cache->save($cacheItem);
        }

        return $route;
    }

    /**
     * @param array $seoRoutes
     */
    public function setSeoRoutes(array $routes)
    {
        $this->seoRoutes = $routes;
    }

    /**
     * @param Serializer $serializer
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }
}
