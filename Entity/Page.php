<?php

namespace KRG\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PageInterface
 *
 * @ORM\MappedSuperclass
 * @Gedmo\Loggable
 */
class Page extends Block implements PageInterface, BlockContentInterface
{
    /**
     * @ORM\OneToOne(targetEntity="KRG\CmsBundle\Entity\SeoInterface", cascade={"all"})
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
