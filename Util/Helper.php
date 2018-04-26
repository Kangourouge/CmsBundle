<?php

namespace KRG\CmsBundle\Util;

use Symfony\Component\Routing\Route;

class Helper
{
    static public function getAvailablePropertiesFromRoute($route)
    {
        if (!$route instanceof Route) {
            return [];
        }

        $availableProperties = $route->compile()->getVariables();

        if ($route->hasDefault('_controller')) {
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
        }

        return$availableProperties;
    }
}
