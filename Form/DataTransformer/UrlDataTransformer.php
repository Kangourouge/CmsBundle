<?php

namespace KRG\CmsBundle\Form\DataTransformer;

use KRG\CmsBundle\Routing\UrlResolver;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class UrlDataTransformer implements DataTransformerInterface
{
    /** @var UrlResolver */
    protected $urlResolver;

    /**
     * UrlDataTransformer constructor.
     *
     * @param UrlResolver $urlResolver
     */
    public function __construct(UrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
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

        $routeInfo = [
            'url'    => $value,
            'name'   => null,
            'params' => [],
        ];

        try {
            $routeInfo = array_merge($routeInfo, $this->urlResolver->resolve($value));
        } catch (ResourceNotFoundException $exception) {
        } catch (\Exception $exception) {
            throw new TransformationFailedException();
        }

        return $routeInfo;
    }
}
