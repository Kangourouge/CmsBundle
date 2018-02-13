<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class UrlDataTransformer implements DataTransformerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * RouteDataTransformer constructor.
     * @param RouterInterface $router
     */
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

        $route = [
            'url'    => $value,
            'name'   => null,
            'params' => [],
        ];

        try {
            $context = new RequestContext();
            $matcher = new UrlMatcher($this->router->getRouteCollection(), $context);
            $attributes = $matcher->match($value);
            $route['name'] = $attributes['_route'];
        } catch (ResourceNotFoundException $exception) {
        } catch (\Exception $exception) {
            throw new TransformationFailedException();
        }

        return $route;
    }
}
