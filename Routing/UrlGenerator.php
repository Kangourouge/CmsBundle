<?php

namespace KRG\SeoBundle\Routing;

use KRG\SeoBundle\Entity\SeoInterface;
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

    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = array())
    {
        if (!preg_match("/^krg_seo_.+/", $name)) {
            if (($route = $this->resolve($name, $parameters)) !== null) {
                $compiledRoute = $route->compile();

                // Do not pass the parameters argument to keep rewritted urls intact
                return parent::doGenerate($compiledRoute->getVariables(), $route->getDefaults(), $route->getRequirements(), $compiledRoute->getTokens(), array(), $name, $referenceType, $compiledRoute->getHostTokens(), $route->getSchemes());
            }
        }

        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }

    private function resolve($name, array $parameters)
    {
        $routes = array_filter($this->seoRoutes, function(Route $route) use ($name, $parameters) {
            /* @var $seo SeoInterface */
            $seo = $this->serializer->deserialize($route->getSeo(), $route->getSeoClass(), 'json');
            return $seo->getRoute() === $name && $seo->isValid($parameters);
        });

        if (count($routes) === 0) {
            return null;
        }

        if (count($routes) === 1) {
            return reset($routes);
        }

        // Sort entries by number of matching parameters
        $weights = array();
        foreach ($routes as $idx => $route) {
            /* @var $seo SeoInterface */
            $seo = $this->serializer->deserialize($route->getSeo(), $route->getSeoClass(), 'json');
            $weights[$idx] = $seo->diff($parameters);
        }
        asort($weights);

        return $routes[key($weights)];
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
