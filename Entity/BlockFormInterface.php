<?php

namespace KRG\SeoBundle\Entity;

interface BlockFormInterface extends BlockInterface
{
    /**
     * Set route
     *
     * @param string $route
     *
     * @return BlockFormInterface
     */
    public function setRoute($route);

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute();

    /**
     * Set type
     *
     * @param string $type
     *
     * @return BlockFormInterface
     */
    public function setType($type);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set parameters
     *
     * @param array $parameters
     *
     * @return BlockFormInterface
     */
    public function setParameters($parameters);

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters();

    /**
     * Set data
     *
     * @param string $data
     *
     * @return BlockFormInterface
     */
    public function setData($data);

    /**
     * Get data
     *
     * @return array
     */
    public function getData();
}
