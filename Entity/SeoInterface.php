<?php

namespace KRG\SeoBundle\Entity;

interface SeoInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set seoPage
     *
     * @param SeoPageInterface $seoPage
     *
     * @return SeoInterface
     */
    public function setSeoPage(SeoPageInterface $seoPage = null);

    /**
     * Get seoPage
     *
     * @return SeoPageInterface
     */
    public function getSeoPage();

    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return SeoRoute
     */
    public function setUid($uid);

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid();

    /**
     * Set route
     *
     * @param string $route
     *
     * @return SeoRoute
     */
    public function setRoute($route);

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute();

    /**
     * Set url
     *
     * @param string $url
     *
     * @return SeoRoute
     */
    public function setUrl($url);

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set parameters
     *
     * @param array $parameters
     *
     * @return SeoRoute
     */
    public function setParameters($parameters);

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters();

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     *
     * @return SeoRoute
     */
    public function setMetaTitle($metaTitle);

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle();

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return SeoRoute
     */
    public function setMetaDescription($metaDescription);

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set ogTitle
     *
     * @param string $ogTitle
     *
     * @return SeoRoute
     */
    public function setOgTitle($ogTitle);

    /**
     * Get ogTitle
     *
     * @return string
     */
    public function getOgTitle();

    /**
     * Set ogDescription
     *
     * @param string $ogDescription
     *
     * @return SeoRoute
     */
    public function setOgDescription($ogDescription);

    /**
     * Get ogDescription
     *
     * @return string
     */
    public function getOgDescription();

    /**
     * Set ogImage
     *
     * @param string $ogImage
     *
     * @return SeoRoute
     */
    public function setOgImage($ogImage);

    /**
     * Get ogImage
     *
     * @return string
     */
    public function getOgImage();

    /**
     * @return int
     */
    public function diff(array $parameters);

    /**
     * @return boolean
     */
    public function isValid(array $parameters);
}
