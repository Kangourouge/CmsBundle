<?php

namespace KRG\CmsBundle\Model;

class RouteInfo
{
    /** @var string */
    protected $route;

    /** @var string */
    protected $controller;

    /** @var array */
    protected $parameters;

    /** @var array */
    protected $properties;

    public function toArray()
    {
        return [
            'route'      => $this->getRoute(),
            'controller' => $this->getController(),
            'parameters' => $this->getParameters(),
            'properties' => $this->getProperties(),
        ];
    }

    public function __construct(string $route = null, string $controller = null, array $parameters = [], array $properties = [])
    {
        $this->route = $route;
        $this->controller = $controller;
        $this->parameters = $parameters;
        $this->properties = $properties;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute(string $route = null)
    {
        $this->route = $route;

        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController(string $controller = null)
    {
        $this->controller = $controller;

        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters = [])
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperties(array $properties = [])
    {
        $this->properties = $properties;

        return $this;
    }

    static public function extractParameters(array $data = [])
    {
        $parameters = [];
        foreach ($data as $key => $value) {
            if ($key[0] !== '_') {
                $parameters[$key] = $value !== 0 ? $value : '';
            }
        }

        return $parameters;
    }

    static public function extractProperties(array $data = [])
    {
        $properties = [];
        foreach ($data as $key => $value) {
            if (false === strstr($value, 'request')) {
                $properties[] = $value;
            }
        }

        return $properties;
    }
}
