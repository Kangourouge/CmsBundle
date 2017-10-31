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
     * {@inheritdoc}
     */
    public function setSeo(SeoInterface $seo = null)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeo()
    {
        return $this->seo;
    }
}
