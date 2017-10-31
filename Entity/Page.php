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
     * @ORM\OneToOne(targetEntity="KRG\SeoBundle\Entity\SeoInterface", cascade={"all"})
     * @ORM\JoinColumn(name="seo_id", referencedColumnName="id")
     * @var SeoInterface
     */
    protected $seo;

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
