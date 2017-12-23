<?php

namespace KRG\CmsBundle\Entity;

interface BlockInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Page
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set key
     *
     * @param string $key
     *
     * @return BlockInterface
     */
    public function setKey($key);

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();

    /**
     * Set enabled
     *
     * @param $enabled
     *
     * @return BlockInterface
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
     * Set working
     *
     * @param $working
     *
     * @return BlockInterface
     */
    public function setWorking($working);

    /**
     * Get working
     *
     * @return boolean
     */
    public function getWorking();

    /**
     * Is working
     *
     * @return boolean
     */
    public function isWorking();
}
