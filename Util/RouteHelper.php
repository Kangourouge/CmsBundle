<?php

namespace KRG\CmsBundle\Util;

use KRG\CmsBundle\DependencyInjection\KRGCmsExtension;
use Symfony\Component\Routing\RouteCollection;

class RouteHelper
{
    const EXCLUDED_ROUTES = [
        'wdt',
        'admin',
        'easyadmin',
        'liip',
        'profiler',
        '_twig',
        '_guess_token',
        'file_manager',
        'krg_cms_',
        'krg_page_snippets',
        'krg_page_show',
        'krg_page_edit',
        KRGCmsExtension::KRG_ROUTE_SEO_PREFIX
    ];

    public static function getRouteNames(RouteCollection $routeCollection, string $regexp = null, \Closure $callable = null)
    {
        $regexp = $regexp ?: '/('.join('|', self::EXCLUDED_ROUTES).')/';

        $routes = [];
        foreach ($routeCollection as $name => $route) {
            if (preg_match($regexp, $name) || $route->hasRequirement('_locale')) {
                continue;
            }

            if ($callable) {
                if (false === call_user_func_array($callable, [$route])) {
                    continue;
                }
            }

            $routes[$route->getPath()] = $name;
        }

        return $routes;
    }
}
