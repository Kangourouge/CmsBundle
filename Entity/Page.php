<?php

namespace KRG\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageInterface
 *
 * @ORM\MappedSuperclass
 */
class Page extends BlockStatic implements PageInterface
{
    /**
     * @ORM\OneToOne(targetEntity="KRG\SeoBundle\Entity\SeoInterface", inversedBy="page", cascade={"all"})
     * @ORM\JoinColumn(name="seo_id", referencedColumnName="id")
     */
    protected $seo;

    function __toString()
    {
        return $this->getSeo() ? (string) $this->getSeo() : '';
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
}
