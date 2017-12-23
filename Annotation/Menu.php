<?php

namespace KRG\SeoBundle\Annotation;

/**
 * Class Menu
 * @Annotation
 * @Target("METHOD")
 * @package KRG\SeoBundle\Annotation
 */
class Menu
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $params;

    public function __construct(array $data)
    {
        $this->name = $data['value'] ?? null;
        $this->route = $data['route'] ?? null;
        $this->params = $data['params'] ?? [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute(string $route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
}