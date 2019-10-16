<?php

namespace KRG\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EMC\FileinputBundle\Entity\FileInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Regex("/^\w+$/", message="krg_cms.block.key.error")
     * @ORM\Column(name="`key`", type="string", unique=true, nullable=false)
     * @var string
     */
    protected $key;

    /**
     * @ORM\Column(type="boolean", name="is_working", options={"default":true})
     * @var boolean
     */
    protected $working;

    /**
     * @ORM\ManyToOne(targetEntity="EMC\FileinputBundle\Entity\FileInterface", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="thumbnail_id", referencedColumnName="id", nullable=true)
     * @var FileInterface
     */
    protected $thumbnail;

    /**
     * @ORM\Column(type="boolean", name="is_enabled", options={"default":false})
     * @var boolean
     */
    protected $enabled;

    public function __construct()
    {
        $this->enabled = false;
        $this->working = true;
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

    /**
     * @return FileInterface
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param FileInterface $thumbnail
     * @return AbstractBlock
     */
    public function setThumbnail(FileInterface $thumbnail = null)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }
}
