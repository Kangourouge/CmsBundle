<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class UrlDataTransformer implements DataTransformerInterface
{
    /** @var RouterInterface */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function transform($value)
    {
        if ($value === null) {
            return null;
        }

        return $value['url'] ?? null;
    }

    public function reverseTransform($value)
    {
        if ($value === null || strlen($value) === 0) {
            return [];
        }

        $data = [
            'url'    => $value,
            'name'   => null,
            'params' => [],
        ];

        try {
            $context = new RequestContext();
            $collection = $this->router->getRouteCollection();
            $matcher = new UrlMatcher($collection, $context);
            $attributes = $matcher->match($value);

            $routeName = $attributes['_route'];
            if (preg_match('/_redirect$/', $routeName) && preg_match('/:urlRedirectAction$/', $attributes['_controller'])) {
                $routeName = preg_replace('/_redirect$/', '', $routeName);
            }
            $data['name'] = $routeName;

            $route = $collection->get($routeName);
            if ($route instanceof Route) {
                $routeParams = $route->compile()->getPathVariables();
                foreach ($routeParams as $routeParam) {
                    if (preg_match('/^_/', $routeParam)) {
                        continue;
                    }
                    else if (isset($attributes[$routeParam])) {
                        $data['params'][$routeParam] = $attributes[$routeParam];
                    }
                    else if ($route->hasDefault($routeParam)) {
                        $data['params'][$routeParam] = $route->hasDefault($routeParam);
                    }
                }
            }
        } catch (ResourceNotFoundException $exception) {
        } catch (\Exception $exception) {
            throw new TransformationFailedException();
        }

        return $data;
    }
}
