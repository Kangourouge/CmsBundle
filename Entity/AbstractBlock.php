<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractBlock implements BlockInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="`key`", type="string", unique=true, nullable=true)
     * @var string
     */
    protected $key;

    /**
     * @ORM\Column(type="boolean", name="is_enabled", options={"default":false})
     * @var boolean
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean", name="is_working", options={"default":true})
     * @var boolean
     */
    protected $working;

    public function __construct()
    {
        $this->enabled = false;
        $this->working = false;
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setWorking($working)
    {
        $this->working = $working;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorking()
    {
        return $this->working;
    }

    /**
     * {@inheritdoc}
     */
    public function isWorking()
    {
        return $this->working;
    }
}
