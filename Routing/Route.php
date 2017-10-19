<?php

namespace KRG\SeoBundle\Routing;

use KRG\SeoBundle\Entity\SeoInterface;
use Symfony\Component\Routing\Route as BaseRoute;

/**
 * Class Route
 * @package KRG\SeoBundle\Routing
 */
class Route extends BaseRoute
{
    /**
     * Store seoClass in route to avoid loading entityManager in UrlGenerator
     * @var string
     */
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
     * @return SeoInterface
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
