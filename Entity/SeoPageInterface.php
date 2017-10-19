<?php

namespace KRG\SeoBundle\Entity;

interface SeoPageInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set seo
     *
     * @param SeoInterface $seo
     *
     * @return SeoPageInterface
     */
    public function setSeo(SeoInterface $seo = null);

    /**
     * Get seo
     *
     * @return SeoInterface
     */
    public function getSeo();

    /**
     * Set formRoute
     *
     * @param string $formRoute
     *
     * @return SeoPage
     */
    public function setFormRoute($formRoute);

    /**
     * Get formRoute
     *
     * @return string
     */
    public function getFormRoute();

    /**
     * Set formType
     *
     * @param string $formType
     *
     * @return SeoPage
     */
    public function setFormType($formType);

    /**
     * Get formType
     *
     * @return string
     */
    public function getFormType();

    /**
     * Set formParameters
     *
     * @param array $formParameters
     *
     * @return SeoPage
     */
    public function setFormParameters($formParameters);

    /**
     * Get formParameters
     *
     * @return array
     */
    public function getFormParameters();

    /**
     * Set formData
     *
     * @param string $formData
     *
     * @return SeoPage
     */
    public function setFormData($formData);

    /**
     * Get formData
     *
     * @return array
     */
    public function getFormData();
}
