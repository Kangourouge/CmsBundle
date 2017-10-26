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
     * Set enabled
     *
     * @param $enabled
     *
     * @return SeoInterface
     */
    public function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled();

    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return SeoInterface
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
     * @return SeoInterface
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
     * @return SeoInterface
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
     * @return SeoInterface
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
     * @return SeoInterface
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
     * @return SeoInterface
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
     * @return SeoInterface
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
     * @return SeoInterface
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
     * @return SeoInterface
     */
    public function setOgImage($ogImage);

    /**
     * Get ogImage
     *
     * @return string
     */
    public function getOgImage();

    /**
     * @param array $parameters
     * @return int
     */
    public function diff(array $parameters);

    /**
     * @param array $parameters
     * @return boolean
     */
    public function isValid(array $parameters);
}
