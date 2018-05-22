<?php

namespace KRG\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Page
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

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
            $this->name = $this->name.' (clone)';
            $this->key = null;
            $this->enabled = false;
            $this->seo = clone $this->seo;
            $this->seo->setUrl($this->seo->getUrl().'-'.uniqid());
            $this->seo->setUid(null);
            $route = $this->seo->getRoute();
            if (isset($route['params']['key'])) {
                $route['params']['key'] = $this->seo->getUid();
                $this->seo->setRoute($route);
            }
        }
    }

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
