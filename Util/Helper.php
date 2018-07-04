<?php

namespace KRG\CmsBundle\Util;

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

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

        return $availableProperties;
    }

    static public function urlExists(string $url)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        return $code >= 200 && $code < 400;
    }
}
