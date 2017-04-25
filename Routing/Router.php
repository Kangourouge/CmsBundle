<?php

namespace KRG\SeoBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Routing\Router as BaseRouter;
use Symfony\Component\Routing\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class Router extends BaseRouter
{
    public function getGenerator()
    {
        if (null !== $this->generator) {
            return $this->generator;
        }

        $generator = parent::getGenerator();
        $generator->setSeoRoutes(array_filter($this->getRouteCollection()->all(), function (Route $route) {
            return $route instanceof \KRG\SeoBundle\Routing\Route;
        }));

        $generator->setSerializer(new Serializer(
            array(new PropertyNormalizer()),
            array(new JsonEncoder())
        ));

        return $generator;
    }
}
