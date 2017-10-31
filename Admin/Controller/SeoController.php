<?php

namespace KRG\SeoBundle\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use KRG\SeoBundle\Entity\SeoInterface;
use Symfony\Component\HttpFoundation\Request;

class SeoController extends BaseAdminController
{
    /**
     * List available parameters from an entity
     *
     * @param SeoInterface $seo
     *
     * @return array|string
     */
    private function getHelperProperties(SeoInterface $seo)
    {
        $route = $this->get('router')->getRouteCollection()->get($seo->getRoute());
        if (!$route) {
            return [];
        }

        $availableProperties = $route->compile()->getVariables();
        list($class, $method) = preg_split('`::`', $route->getDefault('_controller'));
        $reflection = new \ReflectionMethod($class, $method);

        /* @var $parameter \ReflectionParameter */
        foreach ($reflection->getParameters() as $parameter){
            if ($parameter->getClass() === NULL || $parameter->getClass() && $parameter->getClass()->getName() === Request::class) {
                continue;
            }

            $entityName = $parameter->getName();
            $properties = array_map(function(\ReflectionProperty $property) use ($entityName) {
                return sprintf('%s.%s', $entityName, $property->getName());
            }, $parameter->getClass()->getProperties());

            $availableProperties = array_merge($availableProperties, $properties);
        }

        if (count($availableProperties) === 0) {
            return [];
        }

        return $availableProperties;
    }
}
