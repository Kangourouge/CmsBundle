<?php

namespace KRG\CmsBundle\Routing;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class UrlResolver
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * UrlResolver constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function resolve($value)
    {
        $routeInfo = ['name' => null, 'params' => []];

        $context = new RequestContext();
        $collection = $this->router->getRouteCollection();
        $matcher = new UrlMatcher($collection, $context);
        $attributes = $matcher->match($value);

        $routeName = $attributes['_route'];
        if (    preg_match('/_redirect$/', $routeName)
            &&  preg_match('/:redirectAction$/', $attributes['_controller'])
        ) {
            $routeName = preg_replace('/_redirect$/', '', $routeName);
        }
        $routeInfo['name'] = $routeName;

        $route = $collection->get($routeName);
        if ($route instanceof Route) {
            $routeParams = $route->compile()->getPathVariables();
            foreach ($routeParams as $routeParam) {
                if (preg_match('/^_/', $routeParam)) {
                    continue;
                }
                else if (isset($attributes[$routeParam])) {
                    $routeInfo['params'][$routeParam] = $attributes[$routeParam];
                }
                else if ($route->hasDefault($routeParam)) {
                    $routeInfo['params'][$routeParam] = $route->hasDefault($routeParam);
                }
            }
        }

        return $routeInfo;
    }
}