<?php

namespace KRG\SeoBundle\Form;

class SeoFormRegistry
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    private $services;

    function __construct()
    {
        $this->services = array();
    }

    /**
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function add($service, $alias, $route)
    {
        $this->services[get_class($service)] = array(
            'alias' => $alias,
            'form'  => $service,
            'route' => $route
        );
    }

    /**
     * @param $alias
     *
     * @return array
     */
    public function get($alias)
    {
        if (!array_key_exists($alias, $this->services)) {
            throw new \InvalidArgumentException(sprintf('The service "%s" is not registered with the service container.', $alias));
        }

        return $this->services[$alias];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->services;
    }

    /**
     * @param $alias
     *
     * @return bool
     */
    public function has($alias)
    {
        return isset($this->services[$alias]);
    }
}
