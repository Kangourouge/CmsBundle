<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlockForm
 *
 * @ORM\Entity
 * @ORM\Table(name="krg_block_form")
 */
class BlockForm extends AbstractBlock implements BlockFormInterface
{
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $route;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     * @var string
     */
    protected $parameters;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     * @var string
     */
    protected $data;

    /**
     * Set route
     *
     * @param string $route
     *
     * @return BlockFormInterface
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return BlockFormInterface
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     *
     * @return BlockFormInterface
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return BlockFormInterface
     */
    public function setData($data)
    {
        if (false === is_array($data)) {
            $data = json_decode($data, true);
        }

        unset($data['_token']);

        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
