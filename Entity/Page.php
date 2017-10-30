<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageInterface
 *
 * @ORM\MappedSuperclass
 */
class Page extends Block implements PageInterface, BlockContentInterface
{
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToOne(targetEntity="KRG\SeoBundle\Entity\SeoInterface", inversedBy="page", cascade={"all"})
     * @ORM\JoinColumn(name="seo_id", referencedColumnName="id")
     * @var SeoInterface
     */
    protected $seo;

    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Page
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set seo
     *
     * @param SeoInterface $seo
     *
     * @return PageInterface
     */
    public function setSeo(SeoInterface $seo = null)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return SeoInterface
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * @param $enabled
     *
     * @return PageInterface
     */
    public function setEnabled($enabled)
    {
        parent::setEnabled($enabled);

        if ($this->seo) {
            $this->seo->setEnabled($enabled);
        }

        return $this;
    }
}
