<?php

namespace KRG\SeoBundle\Entity;

interface SeoRouteInterface
{
    /**
     * Set route
     *
     * @param array $route
     *
     * @return SeoInterface
     */
    public function setRoute(array $route);

    /**
     * Get route
     *
     * @return array
     */
    public function getRoute();

    /**
     * @return string|null
     */
    public function getRouteName();

    /**
     * @return string|null
     */
    public function getRoutePath();

    /**
     * @return array
     */
    public function getRouteParams();
}