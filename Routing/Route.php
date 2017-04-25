<?php

namespace KRG\SeoBundle\Routing;

use KRG\SeoBundle\Entity\SeoRoute;
use Symfony\Component\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    private $seoClass;

    /**
     * @return mixed
     */
    public function getSeoClass()
    {
        return $this->seoClass;
    }

    /**
     * @param mixed $seoClass
     */
    public function setSeoClass($seoClass)
    {
        $this->seoClass = $seoClass;
    }

    /**
     * @return SeoRoute
     */
    public function getSeo()
    {
        return $this->getDefault('_seo');
    }

    /**
     * @param $seo
     */
    public function setSeo($seo)
    {
        $this->setDefault('_seo', $seo);
    }
}
