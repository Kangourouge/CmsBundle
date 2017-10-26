<?php

namespace KRG\SeoBundle\Entity;

interface PageInterface extends BlockStaticInterface
{
    /**
     * Set seo
     *
     * @param SeoInterface $seo
     *
     * @return PageInterface
     */
    public function setSeo(SeoInterface $seo = null);

    /**
     * Get seo
     *
     * @return SeoInterface
     */
    public function getSeo();
}
