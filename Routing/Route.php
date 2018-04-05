<?php

namespace KRG\CmsBundle\Routing;

use Symfony\Component\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    /**
     * Store seoClass in route to avoid loading entityManager in UrlGenerator
     */
    private $seoClass;

    public function getSeoClass()
    {
        return $this->seoClass;
    }

    public function setSeoClass($seoClass)
    {
        $this->seoClass = $seoClass;
    }

    public function getSeo()
    {
        return $this->getDefault('_seo');
    }

    public function setSeo($seo)
    {
        $this->setDefault('_seo', $seo);
    }
}
