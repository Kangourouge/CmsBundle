<?php

namespace KRG\SeoBundle\Entity;

trait SeoRouteTrait
{
    /**
     * @ORM\Column(type="json_array")
     * @var string
     */
    protected $route;

    /**
     * Set route
     *
     * @param array $route
     *
     * @return SeoInterface
     */
    public function setRoute(array $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return array
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return string|null
     */
    public function getRouteName() {
        return $this->route['name'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getRoutePath() {
        return $this->route['path'] ?? null;
    }

    /**
     * @return array
     */
    public function getRouteParams() {
        return $this->route['params'] ?? [];
    }
}