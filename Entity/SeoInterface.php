<?php

namespace KRG\CmsBundle\Entity;

use Symfony\Component\Routing\CompiledRoute;

interface SeoInterface extends SeoRouteInterface
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
     * Is enabled
     *
     * @return boolean
     */
    public function isEnabled();

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
     * Get preContent
     *
     * @return string
     */
    public function getPreContent();

    /**
     * Set preContent
     *
     * @param string $preContent
     *
     * @return SeoInterface
     */
    public function setPreContent($preContent);

    /**
     * Get postContent
     *
     * @return string
     */
    public function getPostContent();

    /**
     * Set postContent
     *
     * @param string $postContent
     *
     * @return SeoInterface
     */
    public function setPostContent($postContent);

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

    /**
     * @return CompiledRoute
     */
    public function getCompiledRoute();

    /**
     * @param CompiledRoute $compiledRoute
     */
    public function setCompiledRoute(CompiledRoute $compiledRoute);
}
